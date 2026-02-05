<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizResponse;
use App\Models\QuizOutcome;
use App\Services\SubscriberService;
use App\Services\Klaviyo\KlaviyoService;
use Livewire\Component;

class QuizPlayer extends Component
{
    public Quiz $quiz;
    public array $questions = [];
    public int $currentStep = 0;
    public array $answers = [];
    public array $segmentScores = ['tof' => 0, 'mof' => 0, 'bof' => 0];
    public ?QuizOutcome $outcome = null;
    public ?QuizResponse $response = null;
    public bool $completed = false;
    public string $email = '';
    public bool $showEmailForm = false;

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz->load('questions', 'outcomes');
        $this->questions = $this->quiz->questions->sortBy('order')->values()->toArray();
        $this->startQuiz();
    }

    public function startQuiz(): void
    {
        // Use tracking session ID from cookie (same as tracker.js)
        $trackingSessionId = request()->cookie('pp_session_id') ?? session()->getId();

        $this->response = QuizResponse::create([
            'quiz_id' => $this->quiz->id,
            'session_id' => $trackingSessionId,
            'answers' => [],
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $this->quiz->increment('starts_count');

        // Dispatch to frontend for JavaScript tracker
        $this->dispatch('quiz-started', quizId: $this->quiz->id);
    }

    public function selectAnswer(int $questionIndex, string $optionId): void
    {
        $question = $this->questions[$questionIndex] ?? null;
        if (!$question) return;

        $options = $question['options'] ?? [];
        $selectedOption = collect($options)->firstWhere('id', $optionId);
        if (!$selectedOption) return;

        $this->answers[$questionIndex] = [
            'question_id' => $question['id'],
            'question_text' => $question['question_text'],
            'option_id' => $optionId,
            'option_text' => $selectedOption['text'] ?? '',
            'klaviyo_property' => $question['klaviyo_property'] ?? null,
            'klaviyo_value' => $selectedOption['klaviyo_value'] ?? $selectedOption['text'] ?? '',
        ];

        // Update segment scores
        if (isset($selectedOption['score_tof'])) {
            $this->segmentScores['tof'] += (int) $selectedOption['score_tof'];
        }
        if (isset($selectedOption['score_mof'])) {
            $this->segmentScores['mof'] += (int) $selectedOption['score_mof'];
        }
        if (isset($selectedOption['score_bof'])) {
            $this->segmentScores['bof'] += (int) $selectedOption['score_bof'];
        }

        $this->response->update([
            'answers' => $this->answers,
            'questions_answered' => count($this->answers),
        ]);

        // Check if we should collect email now
        if ($this->shouldCollectEmailNow()) {
            $this->showEmailForm = true;
            return;
        }

        $this->nextStep();
    }

    public function submitEmail(): void
    {
        $this->validate(['email' => 'required|email']);

        $service = app(SubscriberService::class);

        $subscriber = $service->subscribe($this->email, [
            'source' => 'quiz:' . $this->quiz->slug,
            'segment' => $this->determineSegment(),
            'first_session_id' => $this->response->session_id,
            'first_landing_page' => url()->current(),
        ]);

        // Link subscriber to response
        $this->response->update([
            'email' => $this->email,
            'subscriber_id' => $subscriber->id,
        ]);

        $service->setEmailCookie($this->email);

        $this->showEmailForm = false;
        $this->nextStep();
    }

    public function skipEmail(): void
    {
        $this->showEmailForm = false;
        $this->nextStep();
    }

    private function shouldCollectEmailNow(): bool
    {
        $settings = $this->quiz->settings ?? [];
        if (!($settings['require_email'] ?? false)) return false;

        $emailStep = $settings['email_step'] ?? 'before_results';

        // After last question, before results
        if ($emailStep === 'before_results') {
            return $this->currentStep === count($this->questions) - 1 && empty($this->email);
        }

        return false;
    }

    public function nextStep(): void
    {
        if ($this->currentStep < count($this->questions) - 1) {
            $this->currentStep++;
        } else {
            $this->completeQuiz();
        }
    }

    public function previousStep(): void
    {
        $settings = $this->quiz->settings ?? [];
        if (($settings['allow_back'] ?? true) && $this->currentStep > 0) {
            $this->currentStep--;
        }
    }

    public function completeQuiz(): void
    {
        $segment = $this->determineSegment();
        $this->outcome = $this->determineOutcome($segment);

        // Build Klaviyo properties from answers
        $klaviyoProperties = $this->buildKlaviyoProperties();

        $this->response->update([
            'outcome_id' => $this->outcome?->id,
            'outcome_name' => $this->outcome?->name,
            'score_tof' => $this->segmentScores['tof'],
            'score_mof' => $this->segmentScores['mof'],
            'score_bof' => $this->segmentScores['bof'],
            'total_score' => array_sum($this->segmentScores),
            'segment' => $segment,
            'status' => 'completed',
            'completed_at' => now(),
            'duration_seconds' => now()->diffInSeconds($this->response->started_at),
            'klaviyo_properties' => $klaviyoProperties,
        ]);

        $this->quiz->increment('completions_count');
        $this->completed = true;

        // Store segment in session/cookie for targeting
        session(['pp_segment' => strtoupper($segment)]);
        cookie()->queue('pp_segment', strtoupper($segment), 60 * 24 * 30);

        // Sync to Klaviyo if subscriber exists
        if ($this->response->subscriber_id) {
            $this->syncToKlaviyo();
        }

        $this->dispatch('quiz-completed', [
            'quizId' => $this->quiz->id,
            'outcomeId' => $this->outcome?->id,
            'segment' => $segment,
            'answers' => $this->answers,
        ]);
    }

    private function buildKlaviyoProperties(): array
    {
        $properties = [
            'pp_segment' => strtoupper($this->determineSegment()),
            'pp_quiz_name' => $this->quiz->name,
        ];

        foreach ($this->answers as $answer) {
            if (!empty($answer['klaviyo_property'])) {
                $properties[$answer['klaviyo_property']] = $answer['klaviyo_value'];
            }
        }

        // Add outcome properties
        if ($this->outcome?->klaviyo_properties) {
            $properties = array_merge($properties, $this->outcome->klaviyo_properties);
        }

        return $properties;
    }

    private function syncToKlaviyo(): void
    {
        try {
            $klaviyo = app(KlaviyoService::class);
            if ($klaviyo->isEnabled()) {
                $this->response->load('subscriber');
                $klaviyo->trackQuizCompleted($this->response);
            }
        } catch (\Exception $e) {
            // Log but don't fail the quiz
            logger()->error('Klaviyo sync failed', ['error' => $e->getMessage()]);
        }
    }

    private function determineOutcome(string $segment): ?QuizOutcome
    {
        $outcomes = $this->quiz->outcomes->sortBy('priority');

        // First try answer-based matching (for product quizzes)
        $answerMatch = $outcomes->first(fn ($o) => $o->matchesAnswer($this->answers));
        if ($answerMatch) return $answerMatch;

        // Then try segment-based matching (for segmentation quizzes)
        $segmentMatch = $outcomes->first(fn ($o) => $o->matchesSegment($segment));
        if ($segmentMatch) return $segmentMatch;

        // Fallback to first outcome
        return $outcomes->first();
    }

    private function determineSegment(): string
    {
        $max = max($this->segmentScores);
        foreach ($this->segmentScores as $segment => $score) {
            if ($score === $max) return $segment;
        }
        return 'tof';
    }

    public function getProgressProperty(): int
    {
        if (count($this->questions) === 0) return 0;
        return round(($this->currentStep + 1) / count($this->questions) * 100);
    }

    public function getCurrentQuestionProperty(): ?array
    {
        return $this->questions[$this->currentStep] ?? null;
    }

    public function render()
    {
        return view('livewire.quiz-player');
    }
}
