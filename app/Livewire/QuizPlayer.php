<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizResponse;
use App\Models\QuizOutcome;
use App\Models\ResultsBank;
use App\Models\StackProduct;
use App\Models\StackStore;
use App\Models\StackStorePeptideLink;
use App\Services\Quiz\QuizFunnelEngine;
use App\Services\SubscriberService;
use App\Services\CustomerIo\CustomerIoService;
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
    public bool $exitEmailCaptured = false;
    public string $exitEmail = '';
    public string $exitReason = '';

    // Text input for question_text slides
    public string $textAnswer = '';

    // Multiple-choice selections (temporary until confirmed)
    public array $multiSelections = [];

    // UTM parameters captured from the URL
    public array $utmParams = [];

    // Response ID exposed to JS for abandon beacon
    public ?int $responseId = null;

    // Navigation history for back button across non-linear slides
    public array $navigationHistory = [];

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz->load('questions', 'outcomes');
        $this->questions = $this->quiz->questions->sortBy('order')->values()->toArray();

        // Capture UTM parameters from the URL
        $this->utmParams = array_filter([
            'utm_source' => request()->query('utm_source'),
            'utm_medium' => request()->query('utm_medium'),
            'utm_campaign' => request()->query('utm_campaign'),
        ]);

        if (count($this->questions) === 0) {
            $this->completed = true;
            return;
        }

        // Pre-fill email from cookie for known subscribers
        $ppEmail = request()->cookie('pp_email');
        if ($ppEmail) {
            $this->email = $ppEmail;
            $this->exitEmailCaptured = true;
        }

        $this->startQuiz();
    }

    public function startQuiz(): void
    {
        $trackingSessionId = request()->cookie('pp_session_id') ?? session()->getId();

        // Check for existing resumable response (in_progress, or recently abandoned by beacon on page refresh)
        $existing = QuizResponse::where('quiz_id', $this->quiz->id)
            ->where('session_id', $trackingSessionId)
            ->whereIn('status', ['in_progress', 'abandoned'])
            ->latest('started_at')
            ->first();

        if ($existing) {
            // Expire stale responses older than 24 hours
            if ($existing->started_at && $existing->started_at->diffInHours(now()) > 24) {
                if ($existing->status !== 'abandoned') {
                    $existing->update(['status' => 'abandoned']);
                }
            } else {
                // Validate quiz structure hasn't changed since session was saved
                $savedAnswers = $existing->answers ?? [];
                $structureValid = $this->validateResumedAnswers($savedAnswers);

                if (!$structureValid) {
                    // Quiz was edited — abandon old response and start fresh
                    $existing->update(['status' => 'abandoned']);
                } else {
                    // Resume: restore progress and mark back to in_progress
                    $existing->update(['status' => 'in_progress']);
                    $this->response = $existing;
                    $this->responseId = $existing->id;
                    $this->answers = $savedAnswers;
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
        }

        // Check if we already know this subscriber (e.g. from popup)
        $subscriberData = [];
        $ppEmail = request()->cookie('pp_email');
        if ($ppEmail) {
            $existingSub = \App\Models\Subscriber::where('email', strtolower(trim($ppEmail)))->first();
            if ($existingSub) {
                $subscriberData = [
                    'subscriber_id' => $existingSub->id,
                    'email' => $existingSub->email,
                ];
                $this->email = $existingSub->email;
            }
        }

        $this->response = QuizResponse::create(array_merge([
            'quiz_id' => $this->quiz->id,
            'session_id' => $trackingSessionId,
            'answers' => [],
            'started_at' => now(),
            'status' => 'in_progress',
        ], $subscriberData, $this->utmParams));
        $this->responseId = $this->response->id;

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
     * Looks up health_goal and experience_level from quiz answers by marketing_property.
     */
    public function getResultsBankEntryProperty(): ?ResultsBank
    {
        $healthGoal = $this->getAnswerByMarketingProperty('health_goal');
        $experienceLevel = $this->getAnswerByMarketingProperty('experience_level');
        $categoryPref = $this->getAnswerByMarketingProperty('category_preference');
        $peptidePref = $this->getAnswerByMarketingProperty('peptide_preference');

        // Path 3 "same category": infer health goal from the peptide they were using
        if (!$healthGoal && $peptidePref) {
            $healthGoal = $this->inferHealthGoalFromPeptide($peptidePref);
        }

        // Direct peptide selection (BOF-A peptide search): look up via StackProduct link
        // Note: inline the product lookup to avoid circular dependency with getStackProductProperty()
        if (!$healthGoal) {
            $selectedPeptide = $this->getAnswerByMarketingProperty('selected_peptide');
            if ($selectedPeptide) {
                $product = StackProduct::where('slug', $selectedPeptide)->first()
                    ?? StackProduct::where('name', $selectedPeptide)->first();
                if ($product) {
                    return ResultsBank::where('stack_product_id', $product->id)
                        ->where('is_active', true)
                        ->with('stackProduct.stores')
                        ->orderByDesc('star_rating')
                        ->first();
                }
            }
            return null;
        }

        // Map quiz answer values to ResultsBank experience levels
        $bofIntent = $this->getAnswerByMarketingProperty('bof_intent');
        $advancedValues = ['i\'ve_already_tried_peptides', 'tried_peptides', 'advanced', 'want_to_stack'];
        if (in_array($experienceLevel, $advancedValues, true) || $bofIntent === 'want_to_stack') {
            $experienceLevel = 'advanced';
        } else {
            $experienceLevel = 'beginner';
        }

        // Path 3 same-category: exclude the peptide they're switching from
        if ($categoryPref === 'same_category' && $peptidePref) {
            return ResultsBank::resolveExcluding($healthGoal, $experienceLevel, $peptidePref);
        }

        return ResultsBank::resolve($healthGoal, $experienceLevel);
    }

    /**
     * Get the StackProduct linked to the resolved ResultsBank entry.
     * Used by vendor-reveal and bridge slides for store comparison.
     */
    public function getStackProductProperty(): ?StackProduct
    {
        $categoryPref = $this->getAnswerByMarketingProperty('category_preference');

        // Direct peptide selection: BOF-A (selected_peptide) or Path 1 (peptide_preference)
        // Skip peptide_preference for Path 3 — it's the peptide they're switching FROM
        $selectedPeptide = $this->getAnswerByMarketingProperty('selected_peptide');
        if (!$categoryPref) {
            $selectedPeptide = $selectedPeptide ?? $this->getAnswerByMarketingProperty('peptide_preference');
        }
        if ($selectedPeptide) {
            $slugMap = ['kisspeptin' => 'kisspeptin-10'];
            $slug = $slugMap[$selectedPeptide] ?? $selectedPeptide;
            $product = StackProduct::where('slug', $slug)->first()
                ?? StackProduct::where('name', $selectedPeptide)->first();
            if ($product) return $product;
        }

        // Standard path: via ResultsBank entry
        $entry = $this->resultsBankEntry;
        if (!$entry || !$entry->stack_product_id) return null;

        return $entry->stackProduct;
    }

    /**
     * Get the user's preferred store category based on their buying_priority answer.
     * Maps buying_priority values to StackStore categories.
     */
    public function getPreferredStoreCategoryProperty(): ?string
    {
        $buyingPriority = $this->getAnswerByMarketingProperty('buying_priority');
        if (!$buyingPriority) return null;

        // Direct match: value is the exact store category slug
        if (array_key_exists($buyingPriority, StackStore::CATEGORIES)) {
            return $buyingPriority;
        }

        // Legacy fallback for old option values
        return match ($buyingPriority) {
            'doctor_guidance', 'TELE', 'doctor_route' => StackStore::CATEGORY_TELEHEALTH,
            'RUO', 'RUO-Research', 'research_route'   => StackStore::CATEGORY_RESEARCH_GRADE,
            default                                    => null,
        };
    }

    /**
     * Get all peptide links grouped by peptide name for the peptide search slide.
     * Pulls from StackProducts + their store pivot data.
     */
    public function getPeptideSearchDataProperty(): array
    {
        $products = StackProduct::with(['stores' => fn($q) => $q->where('stack_stores.is_active', true)])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Batch-load all outbound links referenced by store pivots to avoid N+1
        $outboundLinkIds = $products->flatMap(fn ($p) => $p->stores->pluck('pivot.outbound_link_id'))
            ->filter()
            ->unique()
            ->values();
        $outboundLinks = $outboundLinkIds->isNotEmpty()
            ? \App\Models\OutboundLink::whereIn('id', $outboundLinkIds)->get()->keyBy('id')
            : collect();

        $grouped = [];
        foreach ($products as $product) {
            $inStockStores = $product->stores->where('pivot.is_in_stock', true);

            $vendors = [];
            foreach ($inStockStores->sortBy('pivot.price') as $store) {
                $url = '#';
                if ($store->pivot->outbound_link_id) {
                    $outbound = $outboundLinks->get($store->pivot->outbound_link_id);
                    if ($outbound) $url = $outbound->getTrackingUrl();
                } elseif ($store->pivot->url) {
                    $url = $store->pivot->url;
                } elseif ($store->website_url) {
                    $url = $store->website_url;
                }

                $vendors[] = [
                    'store_name' => $store->name,
                    'store_logo' => $store->logo,
                    'store_category' => $store->category ?? null,
                    'price' => $store->pivot->price,
                    'url' => $url,
                    'is_recommended' => $store->pivot->is_recommended ?? $store->is_recommended,
                ];
            }

            // Include ALL active products — has_deal flag controls quiz routing
            $grouped[$product->name] = [
                'vendors' => $vendors,
                'has_deal' => (bool) $product->has_deal,
            ];
        }

        return $grouped;
    }

    /**
     * Find an answer's marketing_value by its marketing_property name.
     */
    /**
     * Infer health goal from a peptide slug (for Path 3 "same category" logic).
     */
    private function inferHealthGoalFromPeptide(string $peptideSlug): ?string
    {
        $entry = ResultsBank::where('peptide_slug', $peptideSlug)
            ->where('is_active', true)
            ->first();

        return $entry?->health_goal;
    }

    private function getAnswerByMarketingProperty(string $property): ?string
    {
        $result = null;
        foreach ($this->answers as $answer) {
            if (($answer['marketing_property'] ?? null) === $property) {
                $result = $answer['marketing_value'] ?? $answer['text_value'] ?? null;
            }
        }
        return $result;
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
            'marketing_property' => $question['marketing_property'] ?? null,
            'marketing_value' => (!empty($selectedOption['marketing_value']))
                ? $selectedOption['marketing_value']
                : ($selectedOption['value'] ?? $selectedOption['text'] ?? $selectedOption['label'] ?? ''),
            'tags' => $selectedOption['tags'] ?? [],
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

        // Real-time marketing sync
        $this->syncAnswerToMarketing(
            $question['marketing_property'] ?? null,
            $this->answers[$questionIndex]['marketing_value'] ?? null
        );

        // Client-side CDP event (PepTracking.track)
        $this->dispatch('quiz-answer', [
            'quizId' => $this->quiz->id,
            'question' => $question['question_text'] ?? '',
            'answer' => $selectedOption['label'] ?? $selectedOption['text'] ?? $optionId,
            'step' => $questionIndex + 1,
        ]);

        // Check if we should collect email now (legacy setting — new system uses email_capture slides)
        if ($this->shouldCollectEmailNow()) {
            $this->showEmailForm = true;
            return;
        }

        $this->nextStep();
    }

    /**
     * Toggle an option for multiple-choice questions (does not advance).
     */
    public function toggleMultipleAnswer(int $questionIndex, string $optionId): void
    {
        if ($questionIndex !== $this->currentStep) return;

        if (in_array($optionId, $this->multiSelections)) {
            $this->multiSelections = array_values(array_diff($this->multiSelections, [$optionId]));
        } else {
            // Enforce max_selections limit
            $question = $this->questions[$questionIndex] ?? null;
            $maxSelections = $question['max_selections'] ?? null;
            if ($maxSelections && count($this->multiSelections) >= $maxSelections) {
                return;
            }
            $this->multiSelections[] = $optionId;
        }
    }

    /**
     * Commit multiple-choice selections and advance to next slide.
     */
    public function submitMultipleAnswer(): void
    {
        $questionIndex = $this->currentStep;
        $question = $this->questions[$questionIndex] ?? null;
        if (!$question || empty($this->multiSelections)) return;

        $options = $question['options'] ?? [];

        // Subtract old answer scores if re-answering (back button)
        if (isset($this->answers[$questionIndex]) && !empty($this->answers[$questionIndex]['option_ids'])) {
            foreach ($this->answers[$questionIndex]['option_ids'] as $oldId) {
                $oldOption = collect($options)->first(fn ($o) => ($o['value'] ?? $o['id'] ?? '') === $oldId);
                if ($oldOption) {
                    $this->segmentScores['tof'] -= (int) ($oldOption['score_tof'] ?? 0);
                    $this->segmentScores['mof'] -= (int) ($oldOption['score_mof'] ?? 0);
                    $this->segmentScores['bof'] -= (int) ($oldOption['score_bof'] ?? 0);
                }
            }
        }

        $selectedOptions = collect($options)->filter(
            fn ($o) => in_array($o['value'] ?? $o['id'] ?? '', $this->multiSelections)
        );

        $tags = [];
        foreach ($selectedOptions as $opt) {
            foreach ($opt['tags'] ?? [] as $tag) {
                $tags[] = $tag;
            }
            $this->segmentScores['tof'] += (int) ($opt['score_tof'] ?? 0);
            $this->segmentScores['mof'] += (int) ($opt['score_mof'] ?? 0);
            $this->segmentScores['bof'] += (int) ($opt['score_bof'] ?? 0);
        }

        $this->answers[$questionIndex] = [
            'question_id' => $question['id'] ?? null,
            'question_text' => $question['question_text'] ?? '',
            'option_ids' => $this->multiSelections,
            'option_texts' => $selectedOptions->map(fn ($o) => $o['text'] ?? $o['label'] ?? '')->values()->toArray(),
            'option_id' => implode(',', $this->multiSelections),
            'option_text' => $selectedOptions->map(fn ($o) => $o['text'] ?? $o['label'] ?? '')->implode(', '),
            'marketing_property' => $question['marketing_property'] ?? null,
            'marketing_value' => $selectedOptions->map(fn ($o) =>
                (!empty($o['marketing_value'])) ? $o['marketing_value']
                : ($o['value'] ?? $o['text'] ?? $o['label'] ?? '')
            )->implode(', '),
            'tags' => array_values(array_unique($tags)),
        ];

        $this->response->update([
            'answers' => $this->answers,
            'questions_answered' => count($this->answers),
            'navigation_history' => $this->navigationHistory,
        ]);

        // Real-time marketing sync
        $this->syncAnswerToMarketing(
            $question['marketing_property'] ?? null,
            $this->answers[$questionIndex]['marketing_value'] ?? null
        );

        $this->multiSelections = [];
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
            'marketing_property' => $question['marketing_property'] ?? null,
            'marketing_value' => $this->textAnswer,
        ];

        $this->response->update([
            'answers' => $this->answers,
            'questions_answered' => count($this->answers),
            'navigation_history' => $this->navigationHistory,
        ]);

        // Real-time marketing sync
        $this->syncAnswerToMarketing(
            $question['marketing_property'] ?? null,
            $this->textAnswer ?: null
        );

        $this->textAnswer = '';
        $this->nextStep();
    }

    /**
     * Handle email submission from email_capture slides.
     */
    public function submitEmail(): void
    {
        $this->validate(
            ['email' => 'required|email:rfc'],
            ['email.required' => 'Please enter your email address.', 'email.email' => 'Please enter a valid email address.']
        );

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
        $this->exitEmailCaptured = true;

        // Track marketing events now that we have a subscriber
        $this->trackEmailEvents($subscriber);

        // Store answer for email capture slides
        $question = $this->questions[$this->currentStep] ?? null;
        if ($question) {
            $slideType = $question['slide_type'] ?? QuizQuestion::SLIDE_QUESTION;
            if (in_array($slideType, [QuizQuestion::SLIDE_EMAIL_CAPTURE, QuizQuestion::SLIDE_PEPTIDE_SEARCH])) {
                $this->answers[$this->currentStep] = [
                    'question_id' => $question['id'] ?? null,
                    'question_text' => $question['question_text'] ?? 'Email',
                    'text_value' => $this->email,
                    'marketing_property' => $question['marketing_property'] ?? 'email',
                    'marketing_value' => $this->email,
                ];
            }
        }

        $this->showEmailForm = false;
        $this->nextStep();
    }

    public function skipEmail(): void
    {
        // If popup already captured email, use it instead of truly skipping
        $ppEmail = request()->cookie('pp_email');
        if ($ppEmail && filter_var($ppEmail, FILTER_VALIDATE_EMAIL)) {
            $this->email = $ppEmail;
            $this->submitEmail();
            return;
        }

        $this->showEmailForm = false;
        $this->nextStep();
    }

    /**
     * Handle email submission from exit-intent popup.
     * Captures the lead without disrupting quiz flow.
     */
    public function submitExitEmail(): void
    {
        $this->validate(
            ['exitEmail' => 'required|email:rfc'],
            ['exitEmail.required' => 'Please enter your email.', 'exitEmail.email' => 'Please enter a valid email.']
        );

        $service = app(SubscriberService::class);

        $subscriber = $service->subscribe($this->exitEmail, [
            'source' => 'quiz_exit:' . $this->quiz->slug . ($this->exitReason ? ':' . $this->exitReason : ''),
            'segment' => $this->determineSegment(),
            'first_session_id' => $this->response?->session_id,
            'first_landing_page' => url()->current(),
        ]);

        if ($this->response) {
            $this->response->update([
                'email' => $this->exitEmail,
                'subscriber_id' => $subscriber->id,
            ]);
        }

        $service->setEmailCookie($this->exitEmail);
        $this->email = $this->exitEmail;
        $this->exitEmailCaptured = true;

        // Fire tracking events
        $this->trackEmailEvents($subscriber);

        $this->dispatch('exit-email-captured');
    }

    /**
     * Select a peptide from the search slide, record the answer, and advance.
     */
    public function selectPeptide(string $peptideName): void
    {
        $question = $this->questions[$this->currentStep] ?? null;
        if (!$question) return;

        // Check if this peptide has a deal (for routing)
        $searchData = $this->peptideSearchData;
        $hasDeal = ($searchData[$peptideName]['has_deal'] ?? false);

        $this->answers[$this->currentStep] = [
            'question_id' => $question['id'] ?? null,
            'question_text' => $question['question_text'] ?? $question['content_title'] ?? 'Peptide Search',
            'option_id' => $hasDeal ? 'available' : 'unavailable',
            'text_value' => $peptideName,
            'marketing_property' => $question['marketing_property'] ?? 'selected_peptide',
            'marketing_value' => $peptideName,
        ];

        if ($this->response) {
            $this->response->update([
                'answers' => $this->answers,
                'questions_answered' => count($this->answers),
            ]);
        }

        // Real-time marketing sync
        $this->syncAnswerToMarketing('selected_peptide', $peptideName);

        $this->nextStep();
    }

    /**
     * Advance from non-question slides (intermission, loading, reveals, bridge).
     * Called by "Next" buttons and auto-advance timers.
     * Also captures the recommended peptide when leaving a peptide_reveal slide.
     */
    public function advanceSlide(): void
    {
        $question = $this->questions[$this->currentStep] ?? null;

        // Capture recommended peptide when advancing past a peptide_reveal slide
        if ($question && ($question['slide_type'] ?? '') === QuizQuestion::SLIDE_PEPTIDE_REVEAL) {
            $entry = $this->resultsBankEntry;
            if ($entry) {
                $this->answers[$this->currentStep] = [
                    'question_id' => $question['id'] ?? null,
                    'question_text' => $question['question_text'] ?? 'Peptide Reveal',
                    'marketing_property' => 'selected_peptide',
                    'marketing_value' => $entry->peptide_name,
                    'text_value' => $entry->peptide_name,
                ];

                if ($this->response) {
                    $this->response->update([
                        'answers' => $this->answers,
                        'questions_answered' => count($this->answers),
                    ]);
                }

                // Real-time marketing sync
                $this->syncAnswerToMarketing('selected_peptide', $entry->peptide_name);
            }
        }

        $skipTo = $question['skip_to_question'] ?? null;
        $this->nextStep($skipTo ? (string) $skipTo : null);
    }

    /**
     * Reset quiz state so the user can start over from the beginning.
     * Clears answers, step, and navigation history. Creates a fresh QuizResponse.
     */
    public function retakeQuiz(): void
    {
        $this->currentStep = 0;
        $this->answers = [];
        $this->segmentScores = ['tof' => 0, 'mof' => 0, 'bof' => 0];
        $this->outcome = null;
        $this->completed = false;
        $this->navigationHistory = [];
        $this->textAnswer = '';
        $this->multiSelections = [];
        $this->response = null;

        // Start a fresh quiz response
        $this->startQuiz();
    }

    /**
     * Auto-skip email_capture slides when user is already a known subscriber.
     * Silently records the email as an answer, links the subscriber, and advances.
     */
    private function autoSkipEmailCaptureIfKnown(): void
    {
        $slide = $this->questions[$this->currentStep] ?? null;
        if (!$slide) return;

        $slideType = $slide['slide_type'] ?? QuizQuestion::SLIDE_QUESTION;
        if ($slideType !== QuizQuestion::SLIDE_EMAIL_CAPTURE) return;

        $ppEmail = request()->cookie('pp_email');
        if (!$ppEmail) return;

        $service = app(SubscriberService::class);
        $subscriber = $service->findByEmail($ppEmail);
        if (!$subscriber) return;

        // Known subscriber — link to response and record answer
        $this->response->update([
            'email' => $ppEmail,
            'subscriber_id' => $subscriber->id,
        ]);

        $this->answers[$this->currentStep] = [
            'question_id' => $slide['id'] ?? null,
            'question_text' => $slide['question_text'] ?? 'Email',
            'text_value' => $ppEmail,
            'marketing_property' => $slide['marketing_property'] ?? 'email',
            'marketing_value' => $ppEmail,
        ];

        $this->response->update([
            'answers' => $this->answers,
            'questions_answered' => count($this->answers),
            'navigation_history' => $this->navigationHistory,
        ]);

        // Advance past the email slide
        $this->nextStep();
    }

    private function shouldCollectEmailNow(): bool
    {
        // Skip for known subscribers
        if (request()->cookie('pp_email')) return false;

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
            // Auto-skip email_capture for known subscribers
            $this->autoSkipEmailCaptureIfKnown();

            // Terminal slides (vendor_reveal) complete the quiz while still showing the slide
            $slideType = $this->questions[$this->currentStep]['slide_type'] ?? 'question';
            if ($slideType === QuizQuestion::SLIDE_VENDOR_REVEAL && !$this->completed) {
                $this->completeQuiz();
                // Keep showing the vendor_reveal slide instead of the generic results page
                $this->completed = false;
            }
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

        // Pre-fill multi selections if going back to a multiple-choice question
        if ($question && ($question['question_type'] ?? '') === QuizQuestion::TYPE_MULTIPLE) {
            $this->multiSelections = $this->answers[$this->currentStep]['option_ids'] ?? [];
        } else {
            $this->multiSelections = [];
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
        $marketingProperties = $this->buildMarketingProperties();

        // Wrap everything in a transaction with pessimistic locking
        // to prevent race conditions on concurrent completion attempts
        $updated = DB::transaction(function () use ($segment, $marketingProperties) {
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
                    'marketing_properties' => json_encode($marketingProperties),
                    'tags' => json_encode($this->collectAllTags()),
                    'updated_at' => now(),
                ]);

            // Increment inside transaction so it's atomic with the completion
            $this->quiz->increment('completions_count');

            // Increment outcome shown_count
            if ($this->outcome) {
                $this->outcome->increment('shown_count');
            }

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

        // Last-chance subscriber link from popup cookie
        if (!$this->response->subscriber_id) {
            $ppEmail = request()->cookie('pp_email');
            if ($ppEmail && filter_var($ppEmail, FILTER_VALIDATE_EMAIL)) {
                $service = app(SubscriberService::class);
                $subscriber = $service->findByEmail($ppEmail);
                if ($subscriber) {
                    $this->response->update([
                        'email' => $ppEmail,
                        'subscriber_id' => $subscriber->id,
                    ]);
                }
            }
        }

        // Sync to Customer.io outside transaction (external API call)
        if ($this->response->subscriber_id) {
            $this->syncToCustomerIo();
        }

        $this->dispatch('quiz-completed', [
            'quizId' => $this->quiz->id,
            'outcomeId' => $this->outcome?->id,
            'segment' => $segment,
            'answers' => $this->answers,
        ]);
    }

    private function buildMarketingProperties(): array
    {
        $properties = [
            'pp_segment' => $this->determineSegment(),
            'pp_quiz_name' => $this->quiz->name,
        ];

        foreach ($this->answers as $answer) {
            if (!empty($answer['marketing_property']) && !empty($answer['marketing_value'])) {
                $properties[$answer['marketing_property']] = $answer['marketing_value'];
            }
        }

        // Add recommended peptide from ResultsBank (for peptide_reveal paths)
        $resultsBankEntry = $this->resultsBankEntry;
        if ($resultsBankEntry) {
            $properties['recommended_peptide'] = $resultsBankEntry->peptide_name;
            if ($resultsBankEntry->stackProduct) {
                $properties['recommended_peptide_slug'] = $resultsBankEntry->stackProduct->slug;
            }
        }

        // Add collected tags
        $tags = $this->collectAllTags();
        if (!empty($tags)) {
            $properties['pp_tags'] = $tags;
        }

        // Add outcome properties
        if ($this->outcome?->marketing_properties) {
            $properties = array_merge($properties, $this->outcome->marketing_properties);
        }

        return $properties;
    }

    private function syncToCustomerIo(): void
    {
        try {
            $customerIo = app(CustomerIoService::class);
            if ($customerIo->isEnabled()) {
                $this->response->load('subscriber');
                $success = $customerIo->trackQuizCompleted($this->response);
                $customerIo->trackPeptidePaired($this->response);

                if (!$success) {
                    logger()->warning('Customer.io quiz sync returned false — will retry via scheduled command', [
                        'quiz_response_id' => $this->response->id,
                        'subscriber_id' => $this->response->subscriber_id,
                    ]);
                }
            }
        } catch (\Exception $e) {
            logger()->error('Customer.io sync failed', [
                'quiz_response_id' => $this->response->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Fire "Started Quiz" and "Email Captured" events to Customer.io
     * once a subscriber is linked (after email submission).
     * Also subscribes to list and pushes all answers collected so far.
     */
    private function trackEmailEvents(\App\Models\Subscriber $subscriber): void
    {
        try {
            $customerIo = app(CustomerIoService::class);
            if (!$customerIo->isEnabled()) return;

            // Track quiz start (retroactively, using actual start time)
            $customerIo->trackQuizStarted($subscriber, $this->quiz->id, $this->quiz->name);

            // Track email captured
            $this->response->load('quiz');
            $customerIo->trackEmailCaptured($subscriber, $this->response);

            // Push all answers collected so far as profile properties
            $properties = $this->buildMarketingProperties();
            if (!empty($properties)) {
                $customerIo->updateProperties($subscriber, $properties);
            }
        } catch (\Exception $e) {
            logger()->warning('Customer.io email event tracking failed', [
                'error' => $e->getMessage(),
                'subscriber_id' => $subscriber->id,
            ]);
        }
    }

    /**
     * Push a single answer property to Customer.io in real-time.
     * Only fires if subscriber is already linked (post-email capture).
     */
    private function syncAnswerToMarketing(?string $property, ?string $value): void
    {
        if (!$property || !$value) return;

        // Refresh subscriber_id from DB in case popup linked it mid-quiz
        if (!$this->response?->subscriber_id) {
            $freshSubId = \Illuminate\Support\Facades\DB::table('quiz_responses')
                ->where('id', $this->response->id)
                ->value('subscriber_id');
            if ($freshSubId) {
                $this->response->subscriber_id = $freshSubId;
            } else {
                return;
            }
        }

        try {
            $customerIo = app(CustomerIoService::class);
            if (!$customerIo->isEnabled()) return;

            $subscriber = \App\Models\Subscriber::find($this->response->subscriber_id);
            if (!$subscriber) return;

            $customerIo->updateProperties($subscriber, [$property => $value]);
        } catch (\Exception $e) {
            logger()->warning('Customer.io real-time answer sync failed', [
                'property' => $property,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function determineOutcome(string $segment): ?QuizOutcome
    {
        $outcomes = $this->quiz->outcomes->where('is_active', true)->sortBy('priority');
        $totalScore = array_sum($this->segmentScores);

        // Check outcomes in priority order — first match wins
        foreach ($outcomes as $outcome) {
            $conditions = $outcome->conditions ?? [];
            $type = $conditions['type'] ?? '';

            $matches = match ($type) {
                'answer' => $outcome->matchesAnswer($this->answers),
                'segment' => $outcome->matchesSegment($segment),
                'score' => $outcome->matchesScore($totalScore, $conditions['score_type'] ?? 'total'),
                default => empty($conditions), // "always" = empty conditions, matches everyone
            };

            if ($matches) return $outcome;
        }

        // Ultimate fallback if nothing matched (shouldn't happen with an "always" outcome)
        return $outcomes->last();
    }

    /**
     * Validate that saved answers still match the current quiz structure.
     * Returns false if questions were deleted, reordered, or IDs don't match.
     */
    private function validateResumedAnswers(array $savedAnswers): bool
    {
        foreach ($savedAnswers as $index => $answer) {
            $question = $this->questions[$index] ?? null;
            if (!$question) return false;

            // Check question ID matches (catches reordering and deletion)
            $savedQuestionId = $answer['question_id'] ?? null;
            $currentQuestionId = $question['id'] ?? null;
            if ($savedQuestionId && $currentQuestionId && $savedQuestionId !== $currentQuestionId) {
                return false;
            }
        }

        return true;
    }

    private function recalculateScores(): void
    {
        $this->segmentScores = ['tof' => 0, 'mof' => 0, 'bof' => 0];
        foreach ($this->answers as $questionIndex => $answer) {
            $question = $this->questions[$questionIndex] ?? null;
            if (!$question) continue;
            $options = collect($question['options'] ?? []);

            // Multiple-choice: iterate through each selected option
            if (!empty($answer['option_ids']) && is_array($answer['option_ids'])) {
                foreach ($answer['option_ids'] as $selectedId) {
                    $option = $options->first(fn ($o) => ($o['value'] ?? $o['id'] ?? '') === $selectedId);
                    if (!$option) continue;
                    $this->segmentScores['tof'] += (int) ($option['score_tof'] ?? 0);
                    $this->segmentScores['mof'] += (int) ($option['score_mof'] ?? 0);
                    $this->segmentScores['bof'] += (int) ($option['score_bof'] ?? 0);
                }
                continue;
            }

            // Single-choice: match by option_id
            $option = $options->first(fn ($o) => ($o['value'] ?? $o['id'] ?? '') === ($answer['option_id'] ?? ''));
            if (!$option) continue;
            $this->segmentScores['tof'] += (int) ($option['score_tof'] ?? 0);
            $this->segmentScores['mof'] += (int) ($option['score_mof'] ?? 0);
            $this->segmentScores['bof'] += (int) ($option['score_bof'] ?? 0);
        }
    }

    private function collectAllTags(): array
    {
        $tags = [];
        foreach ($this->answers as $answer) {
            foreach ($answer['tags'] ?? [] as $tag) {
                $tags[] = $tag;
            }
        }
        return array_values(array_unique($tags));
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

        $stepsCompleted = count($this->navigationHistory) + 1;

        // Count remaining slides that would be visible given current answers.
        // Slides whose conditions reference unanswered questions are counted
        // optimistically (they may become visible once that question is reached).
        $engine = app(QuizFunnelEngine::class);
        $remainingVisible = 0;

        for ($i = $this->currentStep + 1; $i < count($this->questions); $i++) {
            $slide = $this->questions[$i];

            if ($engine->shouldShowSlide($slide, $this->questions, $this->answers)) {
                $remainingVisible++;
                continue;
            }

            // Slide failed — check if any condition references an unanswered
            // question; if so, the slide may still appear on this path.
            $conditions = $slide['show_conditions']['conditions'] ?? [];
            foreach ($conditions as $condition) {
                $qId = $condition['question_id'] ?? null;
                if ($qId) {
                    $idx = $engine->findSlideIndexById($this->questions, $qId);
                    if ($idx !== null && !isset($this->answers[$idx])) {
                        $remainingVisible++;
                        break;
                    }
                }
            }
        }

        $total = $stepsCompleted + $remainingVisible;

        return min(100, (int) round($stepsCompleted / $total * 100));
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
