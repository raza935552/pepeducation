<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizResponse;
use App\Models\QuizOutcome;
use App\Models\ResultsBank;
use App\Models\StackProduct;
use App\Services\Quiz\QuizFunnelEngine;
use App\Services\SubscriberService;
use App\Services\Klaviyo\KlaviyoService;
use Illuminate\Support\Facades\DB;
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

    // Text input for question_text slides
    public string $textAnswer = '';

    // Navigation history for back button across non-linear slides
    public array $navigationHistory = [];

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz->load('questions', 'outcomes');
        $this->questions = $this->quiz->questions->sortBy('order')->values()->toArray();

        if (count($this->questions) === 0) {
            $this->completed = true;
            return;
        }

        $this->startQuiz();
    }

    public function startQuiz(): void
    {
        $trackingSessionId = request()->cookie('pp_session_id') ?? session()->getId();

        // Check for existing in-progress response to prevent duplicate starts on refresh
        $existing = QuizResponse::where('quiz_id', $this->quiz->id)
            ->where('session_id', $trackingSessionId)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            // Expire stale responses older than 24 hours
            if ($existing->started_at && $existing->started_at->diffInHours(now()) > 24) {
                $existing->update(['status' => 'abandoned']);
            } else {
                $this->response = $existing;
                $this->answers = $existing->answers ?? [];
                $this->currentStep = max(0, count($this->answers) - 1);
                // Trim navigation history to match resumed step
                // (prevents back button jumping to slides beyond the resumed position)
                $savedHistory = $existing->navigation_history ?? [];
                $this->navigationHistory = array_filter($savedHistory, fn ($step) => $step <= $this->currentStep);
                $this->navigationHistory = array_values($this->navigationHistory);
                $this->recalculateScores();
                return;
            }
        }

        $this->response = QuizResponse::create([
            'quiz_id' => $this->quiz->id,
            'session_id' => $trackingSessionId,
            'answers' => [],
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $this->quiz->increment('starts_count');
        $this->dispatch('quiz-started', quizId: $this->quiz->id);
    }

    /**
     * Get the current slide data.
     */
    public function getCurrentSlideProperty(): ?array
    {
        return $this->questions[$this->currentStep] ?? null;
    }

    /**
     * Get the current slide with dynamic content resolved and tokens interpolated.
     * Views should use this instead of currentSlide for rendering text.
     */
    public function getResolvedSlideProperty(): ?array
    {
        $slide = $this->currentSlide;
        if (!$slide) return null;

        $engine = app(QuizFunnelEngine::class);

        // Build extra context tokens (peptide name, etc.)
        $context = [];
        $entry = $this->resultsBankEntry;
        if ($entry) {
            $context['peptide_name'] = $entry->peptide_name;
            $context['star_rating'] = (string) $entry->star_rating;
            $context['rating_label'] = $entry->rating_label;
        }

        // Resolve dynamic content (swap title/body if map matches)
        $dynamic = $engine->resolveDynamicContent($slide, $this->answers);
        if ($dynamic) {
            if (!empty($dynamic['title'])) $slide['content_title'] = $dynamic['title'];
            if (!empty($dynamic['body'])) $slide['content_body'] = $dynamic['body'];
            if (!empty($dynamic['source'])) $slide['content_source'] = $dynamic['source'];
        }

        // Interpolate tokens in all text fields
        $textFields = ['content_title', 'content_body', 'content_source', 'question_text', 'cta_text'];
        foreach ($textFields as $field) {
            if (!empty($slide[$field])) {
                $slide[$field] = $engine->interpolateTokens($slide[$field], $this->answers, $context);
            }
        }

        return $slide;
    }

    /**
     * Get slide type of current slide.
     */
    public function getCurrentSlideTypeProperty(): string
    {
        return $this->currentSlide['slide_type'] ?? QuizQuestion::SLIDE_QUESTION;
    }

    /**
     * Resolve the Results Bank entry for the peptide reveal slide.
     * Looks up health_goal and experience_level from quiz answers by klaviyo_property.
     */
    public function getResultsBankEntryProperty(): ?ResultsBank
    {
        $healthGoal = $this->getAnswerByKlaviyoProperty('health_goal');
        $experienceLevel = $this->getAnswerByKlaviyoProperty('experience_level');

        if (!$healthGoal) return null;

        // BOF-C stackers are experienced — default to advanced
        $bofIntent = $this->getAnswerByKlaviyoProperty('bof_intent');
        if (!$experienceLevel) {
            $experienceLevel = ($bofIntent === 'want_to_stack') ? 'advanced' : 'beginner';
        }

        return ResultsBank::resolve($healthGoal, $experienceLevel);
    }

    /**
     * Get the StackProduct linked to the resolved ResultsBank entry.
     * Used by vendor-reveal and bridge slides for store comparison.
     */
    public function getStackProductProperty(): ?StackProduct
    {
        // BOF-A: user selected a specific peptide — look up StackProduct by slug directly
        $selectedPeptide = $this->getAnswerByKlaviyoProperty('selected_peptide');
        if ($selectedPeptide) {
            $slugMap = ['kisspeptin' => 'kisspeptin-10'];
            $slug = $slugMap[$selectedPeptide] ?? $selectedPeptide;
            $product = StackProduct::where('slug', $slug)->first();
            if ($product) return $product;
        }

        // Standard path: via ResultsBank entry
        $entry = $this->resultsBankEntry;
        if (!$entry || !$entry->stack_product_id) return null;

        return $entry->stackProduct;
    }

    /**
     * Find an answer's klaviyo_value by its klaviyo_property name.
     */
    private function getAnswerByKlaviyoProperty(string $property): ?string
    {
        foreach ($this->answers as $answer) {
            if (($answer['klaviyo_property'] ?? null) === $property) {
                return $answer['klaviyo_value'] ?? $answer['text_value'] ?? null;
            }
        }
        return null;
    }

    /**
     * Handle answer selection for choice-based question slides.
     * This is the existing behavior — kept intact for backwards compatibility.
     */
    public function selectAnswer(int $questionIndex, string $optionId): void
    {
        if ($questionIndex < 0 || $questionIndex >= count($this->questions)) {
            return;
        }

        // Only allow answering the current question (prevent out-of-order submissions)
        if ($questionIndex !== $this->currentStep) {
            return;
        }

        $question = $this->questions[$questionIndex] ?? null;
        if (!$question) return;

        $options = $question['options'] ?? [];
        // Support both old format (id key) and new format (value key)
        $selectedOption = collect($options)->first(fn ($o) => ($o['value'] ?? $o['id'] ?? '') === $optionId);
        if (!$selectedOption) return;

        // Subtract old answer scores if changing answer (back button fix)
        if (isset($this->answers[$questionIndex])) {
            $oldOptionId = $this->answers[$questionIndex]['option_id'];
            $oldOption = collect($options)->first(fn ($o) => ($o['value'] ?? $o['id'] ?? '') === $oldOptionId);
            if ($oldOption) {
                $this->segmentScores['tof'] -= (int) ($oldOption['score_tof'] ?? 0);
                $this->segmentScores['mof'] -= (int) ($oldOption['score_mof'] ?? 0);
                $this->segmentScores['bof'] -= (int) ($oldOption['score_bof'] ?? 0);
            }
        }

        $this->answers[$questionIndex] = [
            'question_id' => $question['id'] ?? null,
            'question_text' => $question['question_text'] ?? '',
            'option_id' => $optionId,
            'option_text' => $selectedOption['text'] ?? $selectedOption['label'] ?? '',
            'klaviyo_property' => $question['klaviyo_property'] ?? null,
            'klaviyo_value' => $selectedOption['klaviyo_value'] ?? $selectedOption['text'] ?? $selectedOption['label'] ?? '',
        ];

        // Add new answer scores
        $this->segmentScores['tof'] += (int) ($selectedOption['score_tof'] ?? 0);
        $this->segmentScores['mof'] += (int) ($selectedOption['score_mof'] ?? 0);
        $this->segmentScores['bof'] += (int) ($selectedOption['score_bof'] ?? 0);

        $this->response->update([
            'answers' => $this->answers,
            'questions_answered' => count($this->answers),
            'navigation_history' => $this->navigationHistory,
        ]);

        // Check if we should collect email now (legacy setting — new system uses email_capture slides)
        if ($this->shouldCollectEmailNow()) {
            $this->showEmailForm = true;
            return;
        }

        $this->nextStep();
    }

    /**
     * Handle text input submission for question_text slides.
     */
    public function submitTextAnswer(): void
    {
        $question = $this->questions[$this->currentStep] ?? null;
        if (!$question) return;

        $slideType = $question['slide_type'] ?? QuizQuestion::SLIDE_QUESTION;
        if ($slideType !== QuizQuestion::SLIDE_QUESTION_TEXT) return;

        $this->validate(['textAnswer' => 'required|string|max:1000']);

        $this->answers[$this->currentStep] = [
            'question_id' => $question['id'] ?? null,
            'question_text' => $question['question_text'] ?? '',
            'text_value' => $this->textAnswer,
            'klaviyo_property' => $question['klaviyo_property'] ?? null,
            'klaviyo_value' => $this->textAnswer,
        ];

        $this->response->update([
            'answers' => $this->answers,
            'questions_answered' => count($this->answers),
            'navigation_history' => $this->navigationHistory,
        ]);

        $this->textAnswer = '';
        $this->nextStep();
    }

    /**
     * Handle email submission from email_capture slides.
     */
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

        // Store answer for email capture slides
        $question = $this->questions[$this->currentStep] ?? null;
        if ($question) {
            $slideType = $question['slide_type'] ?? QuizQuestion::SLIDE_QUESTION;
            if ($slideType === QuizQuestion::SLIDE_EMAIL_CAPTURE) {
                $this->answers[$this->currentStep] = [
                    'question_id' => $question['id'] ?? null,
                    'question_text' => $question['question_text'] ?? 'Email',
                    'text_value' => $this->email,
                    'klaviyo_property' => $question['klaviyo_property'] ?? 'email',
                    'klaviyo_value' => $this->email,
                ];
            }
        }

        $this->showEmailForm = false;
        $this->nextStep();
    }

    public function skipEmail(): void
    {
        $this->showEmailForm = false;
        $this->nextStep();
    }

    /**
     * Advance from non-question slides (intermission, loading, reveals, bridge).
     * Called by "Next" buttons and auto-advance timers.
     */
    public function advanceSlide(): void
    {
        $this->nextStep();
    }

    private function shouldCollectEmailNow(): bool
    {
        $settings = $this->quiz->settings ?? [];
        if (!($settings['require_email'] ?? false)) return false;

        // If there's an email_capture slide in the quiz, don't use legacy email gate
        $hasEmailSlide = collect($this->questions)->contains(fn ($q) =>
            ($q['slide_type'] ?? 'question') === QuizQuestion::SLIDE_EMAIL_CAPTURE
        );
        if ($hasEmailSlide) return false;

        $emailStep = $settings['email_step'] ?? 'before_results';

        // After last question, before results
        if ($emailStep === 'before_results') {
            return $this->currentStep === count($this->questions) - 1 && empty($this->email);
        }

        return false;
    }

    public function nextStep(?string $skipToQuestionId = null): void
    {
        // Track navigation for back button
        $this->navigationHistory[] = $this->currentStep;

        // Persist navigation history for session resume
        if ($this->response) {
            $this->response->update(['navigation_history' => $this->navigationHistory]);
        }

        $engine = app(QuizFunnelEngine::class);

        // If no explicit skip_to, check if current answer has one
        if (!$skipToQuestionId) {
            $currentAnswer = $this->answers[$this->currentStep] ?? null;
            $currentQuestion = $this->questions[$this->currentStep] ?? null;
            if ($currentAnswer && $currentQuestion) {
                $skipToQuestionId = $engine->getSkipToFromAnswer($currentAnswer, $currentQuestion);
            }
        }

        $nextIndex = $engine->resolveNextSlide(
            $this->questions,
            $this->currentStep,
            $this->answers,
            $skipToQuestionId
        );

        if ($nextIndex !== null) {
            $this->currentStep = $nextIndex;
        } else {
            $this->completeQuiz();
        }
    }

    public function previousStep(): void
    {
        $settings = $this->quiz->settings ?? [];
        if (!($settings['allow_back'] ?? true)) return;

        // Use navigation history if available (respects non-linear flow)
        if (!empty($this->navigationHistory)) {
            $this->currentStep = array_pop($this->navigationHistory);
        } elseif ($this->currentStep > 0) {
            $this->currentStep--;
        }

        // Pre-fill text answer if going back to a text question
        $question = $this->questions[$this->currentStep] ?? null;
        if ($question && ($question['slide_type'] ?? 'question') === QuizQuestion::SLIDE_QUESTION_TEXT) {
            $this->textAnswer = $this->answers[$this->currentStep]['text_value'] ?? '';
        }
    }

    public function completeQuiz(): void
    {
        // Idempotency: skip if already completed in memory
        if ($this->completed) {
            return;
        }

        $segment = $this->determineSegment();
        $this->outcome = $this->determineOutcome($segment);
        $klaviyoProperties = $this->buildKlaviyoProperties();

        // Wrap everything in a transaction with pessimistic locking
        // to prevent race conditions on concurrent completion attempts
        $updated = DB::transaction(function () use ($segment, $klaviyoProperties) {
            // Lock the row to prevent concurrent completion
            $lockedResponse = DB::table('quiz_responses')
                ->where('id', $this->response->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedResponse || $lockedResponse->status !== 'in_progress') {
                return false;
            }

            DB::table('quiz_responses')
                ->where('id', $this->response->id)
                ->update([
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
                    'klaviyo_properties' => json_encode($klaviyoProperties),
                    'updated_at' => now(),
                ]);

            // Increment inside transaction so it's atomic with the completion
            $this->quiz->increment('completions_count');

            return true;
        });

        $this->completed = true;

        if (!$updated) {
            $this->response->refresh();
            return;
        }

        $this->response->refresh();

        // Store segment in session/cookie for targeting
        session(['pp_segment' => $segment]);
        cookie()->queue('pp_segment', $segment, 60 * 24 * 30);

        // Sync to Klaviyo outside transaction (external API call)
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
            'pp_segment' => $this->determineSegment(),
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

    private function recalculateScores(): void
    {
        $this->segmentScores = ['tof' => 0, 'mof' => 0, 'bof' => 0];
        foreach ($this->answers as $questionIndex => $answer) {
            $question = $this->questions[$questionIndex] ?? null;
            if (!$question) continue;
            $option = collect($question['options'] ?? [])->first(fn ($o) => ($o['value'] ?? $o['id'] ?? '') === ($answer['option_id'] ?? ''));
            if (!$option) continue;
            $this->segmentScores['tof'] += (int) ($option['score_tof'] ?? 0);
            $this->segmentScores['mof'] += (int) ($option['score_mof'] ?? 0);
            $this->segmentScores['bof'] += (int) ($option['score_bof'] ?? 0);
        }
    }

    private function determineSegment(): string
    {
        $scores = $this->segmentScores;
        $max = max($scores);

        // Tie-breaking: prefer higher intent (BOF > MOF > TOF)
        if ($scores['bof'] === $max) return 'bof';
        if ($scores['mof'] === $max) return 'mof';
        return 'tof';
    }

    public function getProgressProperty(): int
    {
        if (count($this->questions) === 0) return 0;

        // Estimate journey length from first answer (journey selector)
        $journeyLength = $this->estimateJourneyLength();
        $stepsCompleted = count($this->navigationHistory) + 1; // history + current

        return min(100, round($stepsCompleted / $journeyLength * 100));
    }

    private function estimateJourneyLength(): int
    {
        // Check first answer to determine journey path
        $journeyAnswer = $this->answers[0]['option_id'] ?? null;
        $subPathAnswer = null;

        // Find sub-path answer if BOF
        if ($journeyAnswer === 'ready_to_buy') {
            foreach ($this->answers as $a) {
                if (in_array($a['option_id'] ?? '', ['know_what_i_want', 'know_my_goal', 'want_to_stack'])) {
                    $subPathAnswer = $a['option_id'];
                    break;
                }
            }
        }

        return match ($journeyAnswer) {
            'brand_new' => 16,   // TOF
            'researching' => 16, // MOF
            'ready_to_buy' => match ($subPathAnswer) {
                'know_what_i_want' => 8,  // BOF-A
                'know_my_goal' => 9,      // BOF-B
                'want_to_stack' => 10,    // BOF-C
                default => 9,             // BOF default before sub-path chosen
            },
            default => count($this->questions), // Fallback for non-funnel quizzes
        };
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
