<div class="quiz-player max-w-2xl mx-auto">
    @if(!$completed)
        <!-- Progress Bar -->
        @if(($quiz->settings ?? [])['show_progress_bar'] ?? true)
            <div class="mb-6">
                <div class="flex justify-end text-sm text-gray-600 mb-2">
                    <span>{{ $this->progress }}%</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-primary transition-all duration-300" style="width: {{ $this->progress }}%"></div>
                </div>
            </div>
        @endif

        {{-- Legacy email form (for quizzes without email_capture slide) --}}
        @if($showEmailForm)
            <div class="card p-8" wire:key="email-capture-legacy">
                <h3 class="text-xl font-semibold mb-4">Get your personalized results via email</h3>
                <form wire:submit="submitEmail" class="space-y-4" novalidate>
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="Enter your email"
                        autocomplete="email"
                        class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary disabled:opacity-50 @error('email') border-red-500 ring-red-500 @enderror"
                        wire:loading.attr="disabled"
                    >
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    <div class="flex gap-3">
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary flex-1 disabled:opacity-50">
                            <span wire:loading.remove>Continue</span>
                            <span wire:loading>Submitting...</span>
                        </button>
                        <button type="button" wire:click="skipEmail" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Skip</button>
                    </div>
                </form>
            </div>
        @elseif($this->currentSlide)
            {{-- Slide Type Router --}}
            @switch($this->currentSlideType)
                @case('question')
                    @include('livewire.quiz-slides.question')
                    @break

                @case('question_text')
                    @include('livewire.quiz-slides.question-text')
                    @break

                @case('intermission')
                    @include('livewire.quiz-slides.intermission')
                    @break

                @case('loading')
                    @include('livewire.quiz-slides.loading')
                    @break

                @case('email_capture')
                    @include('livewire.quiz-slides.email-capture')
                    @break

                @case('peptide_reveal')
                    @include('livewire.quiz-slides.peptide-reveal')
                    @break

                @case('vendor_reveal')
                    @include('livewire.quiz-slides.vendor-reveal')
                    @break

                @case('bridge')
                    @include('livewire.quiz-slides.bridge')
                    @break

                @case('peptide_search')
                    @include('livewire.quiz-slides.peptide-search')
                    @break

                @default
                    {{-- Fallback: render as question (backwards compatible) --}}
                    @include('livewire.quiz-slides.question')
            @endswitch
        @endif

        {{-- Save Progress Indicator --}}
        @if($currentStep > 0 && $this->currentSlideType !== 'loading')
            <div
                class="flex justify-center mt-6"
                x-data="{
                    state: 'idle',
                    timer: null,
                    count: 0,
                    msgs: [
                        'Nice choice! Locked in.',
                        'Got it! Moving right along.',
                        'Noted! You are on a roll.',
                        'Saved! Keep going, almost there.',
                        'Great pick! Progress secured.',
                        'Boom! Answer saved.',
                        'Locked and loaded!',
                        'Smart choice! All saved.',
                    ],
                    get funMsg() { return this.msgs[this.count % this.msgs.length]; }
                }"
                x-init="
                    Livewire.hook('commit', ({ succeed }) => {
                        state = 'saving';
                        clearTimeout(timer);
                        succeed(() => {
                            count++;
                            state = 'saved';
                            timer = setTimeout(() => state = 'idle', 3500);
                        });
                    })
                "
            >
                {{-- Saving... --}}
                <div
                    x-show="state === 'saving'"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-50/80 border border-amber-200/50 backdrop-blur-sm"
                >
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span class="text-xs font-medium text-amber-700 tracking-wide">Saving...</span>
                </div>

                {{-- Saved (fun rotating messages) --}}
                <div
                    x-show="state === 'saved'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-400"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/50 shadow-sm shadow-emerald-100/50"
                >
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 shadow-sm">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span class="text-xs font-semibold text-emerald-700" x-text="funMsg"></span>
                </div>

                {{-- Idle --}}
                <div
                    x-show="state === 'idle'"
                    x-transition:enter="transition ease-in duration-600"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5"
                >
                    <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                    <span class="text-[11px] text-gray-300 font-medium">Progress saved &middot; come back anytime</span>
                </div>
            </div>
        @endif

    @else
        <!-- Results -->
        <div class="card p-8 text-center" wire:key="results">
            @if($outcome)
                @if($outcome->result_image)
                    <img src="{{ Storage::url($outcome->result_image) }}" alt="{{ $outcome->result_title }}" loading="lazy" class="w-32 h-32 mx-auto mb-6 rounded-full object-cover">
                @endif

                <h2 class="text-3xl font-bold mb-4">{{ $outcome->result_title ?? $outcome->name }}</h2>
                <p class="text-lg text-gray-600 mb-6">{{ $outcome->result_message }}</p>

                @if($outcome->redirect_url)
                    <a href="{{ $outcome->redirect_url }}" class="btn btn-primary inline-block">
                        {{ $outcome->redirect_type === 'product' ? 'View Product' : 'Continue' }}
                    </a>
                @endif

                <!-- Segment Badge — only for segmentation quizzes -->
                @if($quiz->type === 'segmentation' && $response->segment)
                    <div class="mt-8 pt-6 border-t">
                        <p class="text-sm text-gray-500 mb-2">Your profile:</p>
                        <span class="inline-block px-4 py-2 rounded-full text-sm font-medium
                            {{ $response->segment === 'bof' ? 'bg-green-100 text-green-800' :
                               ($response->segment === 'mof' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ $response->getSegmentLabel() }}
                        </span>
                    </div>
                @endif
            @else
                <h2 class="text-2xl font-bold mb-4">Thank you for completing the quiz!</h2>
                <p class="text-gray-600">Your results have been recorded.</p>
            @endif
        </div>
    @endif

    {{-- Exit Intent Multi-Step Popup (BioLinkX-style) --}}
    @if(!$exitEmailCaptured && !$completed)
    <div x-data="quizExitPopup()" x-cloak x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);"
         @keydown.escape.window="dismiss()"
         @click.self="dismiss()">

        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            {{-- Close button --}}
            <button @click="dismiss()" aria-label="Close"
                class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 hover:bg-white text-gray-500 hover:text-gray-700 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            {{-- Progress dots --}}
            <div class="flex justify-center gap-1.5 pt-6">
                <div class="w-1.5 h-1.5 rounded-full transition-all" :class="currentStep === 1 ? 'bg-primary-500 w-6' : 'bg-gray-300'"></div>
                <div class="w-1.5 h-1.5 rounded-full transition-all" :class="currentStep === 2 ? 'bg-primary-500 w-6' : 'bg-gray-300'"></div>
                <div class="w-1.5 h-1.5 rounded-full transition-all" :class="currentStep === 3 ? 'bg-primary-500 w-6' : 'bg-gray-300'"></div>
            </div>

            {{-- STEP 1: Why leaving / survey --}}
            <div x-show="currentStep === 1" class="p-8 pt-6"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 leading-tight">Wait! Before you go...</h3>
                    <p class="text-gray-600 mt-2 text-sm">You're almost done. What's on your mind?</p>
                </div>

                <div class="space-y-2">
                    <button type="button" @click="selectReason('want_results')"
                        class="w-full py-3.5 px-4 text-left font-medium rounded-xl border-2 border-primary-200 bg-primary-50 text-primary-700 hover:bg-primary-100 hover:border-primary-300 transition">
                        📬 Send me my results
                    </button>
                    <button type="button" @click="selectReason('too_many_questions')"
                        class="w-full py-3.5 px-4 text-left font-medium rounded-xl border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                        ⏱️ Too many questions
                    </button>
                    <button type="button" @click="selectReason('not_sure')"
                        class="w-full py-3.5 px-4 text-left font-medium rounded-xl border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                        🤔 Not sure peptides are right for me
                    </button>
                    <button type="button" @click="selectReason('later')"
                        class="w-full py-3.5 px-4 text-left font-medium rounded-xl border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                        🕒 I'll come back later
                    </button>
                </div>

                <button @click="dismiss()" class="block w-full text-center text-xs text-gray-400 hover:text-gray-600 mt-5 underline">
                    No thanks, close this
                </button>
            </div>

            {{-- STEP 2: Email capture with contextual copy --}}
            <div x-show="currentStep === 2" class="p-8 pt-6"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900" x-text="step2Headline"></h3>
                    <p class="text-gray-600 mt-2 text-sm" x-text="step2Subtitle"></p>
                </div>

                <form wire:submit="submitExitEmail" class="space-y-3" @submit="onEmailSubmit">
                    <input type="email" wire:model="exitEmail" placeholder="your@email.com" required autocomplete="email"
                        class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-center py-3.5 text-base">
                    @error('exitEmail') <p class="text-red-500 text-xs text-center">{{ $message }}</p> @enderror
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full py-3.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="submitExitEmail" x-text="step2CTA"></span>
                        <span wire:loading wire:target="submitExitEmail">Saving...</span>
                    </button>
                    <p class="text-center text-[11px] text-gray-400">No spam. Unsubscribe anytime.</p>
                </form>

                <button @click="currentStep = 1" class="block w-full text-center text-xs text-gray-400 hover:text-gray-600 mt-4">
                    ← Go back
                </button>
            </div>

            {{-- STEP 3: Success --}}
            <div x-show="currentStep === 3" class="p-8 pt-6 text-center"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">You're all set!</h3>
                <p class="text-gray-600 mt-2 text-sm mb-6">Your results are saved. We'll email your personalized peptide match if you don't finish today.</p>

                <button @click="continueQuiz()" class="w-full py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition">
                    Finish the Quiz Now
                </button>
                <button @click="dismiss()" class="block w-full text-center text-xs text-gray-400 hover:text-gray-600 mt-3">
                    I'll come back later
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

@script
<script>
    // Multi-step exit intent popup
    window.quizExitPopup = function() {
        return {
            show: false,
            fired: false,
            currentStep: 1,
            selectedReason: null,
            step2Headline: 'Save your progress',
            step2Subtitle: "We'll email your personalized peptide match.",
            step2CTA: 'Save My Results',

            shouldTrigger() {
                return !this.fired && !this.$wire.exitEmailCaptured && !this.$wire.completed && !window.__quizCompleted;
            },

            trigger() {
                if (this.shouldTrigger()) {
                    this.show = true;
                    this.fired = true;
                }
            },

            init() {
                // Desktop: mouseleave toward top
                document.addEventListener('mouseleave', (e) => {
                    if (e.clientY < 5) this.trigger();
                });

                // Tab close / navigate away
                window.addEventListener('beforeunload', (e) => {
                    if (this.shouldTrigger()) {
                        e.preventDefault();
                        e.returnValue = '';
                        setTimeout(() => this.trigger(), 500);
                    }
                });

                // Mobile: tab switch / app switch
                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'visible' && !this.show && this.shouldTrigger()) {
                        this.trigger();
                    }
                });

                // Listen for successful exit email capture
                this.$wire.on('exit-email-captured', () => {
                    this.currentStep = 3;
                });
            },

            selectReason(reason) {
                this.selectedReason = reason;
                this.$wire.set('exitReason', reason, false);
                // Tailor step 2 copy based on selected reason
                const copy = {
                    want_results: {
                        headline: "Where should we send your results?",
                        subtitle: "Enter your email and we'll deliver your personalized peptide match.",
                        cta: 'Send My Results'
                    },
                    too_many_questions: {
                        headline: "Short on time?",
                        subtitle: "Leave your email — we'll send a quick summary so you can finish when you're ready.",
                        cta: 'Send Me a Summary'
                    },
                    not_sure: {
                        headline: "Still curious?",
                        subtitle: "Get a free peptide beginner's guide plus your quiz results.",
                        cta: 'Send Me the Guide'
                    },
                    later: {
                        headline: "Save your spot.",
                        subtitle: "We'll email a link so you can pick up right where you left off.",
                        cta: 'Email Me the Link'
                    },
                };
                const c = copy[reason] || copy.want_results;
                this.step2Headline = c.headline;
                this.step2Subtitle = c.subtitle;
                this.step2CTA = c.cta;
                this.currentStep = 2;
            },

            onEmailSubmit() {
                // Livewire will handle the submission, and we listen for exit-email-captured
            },

            continueQuiz() {
                this.show = false;
            },

            dismiss() {
                this.show = false;
            }
        };
    };

    // Scroll quiz container into view after each slide transition
    Livewire.hook('morph.updated', ({ el }) => {
        if (el.classList && el.classList.contains('quiz-player')) {
            requestAnimationFrame(() => {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    });

    // Track quiz start
    $wire.on('quiz-started', ({ quizId }) => {
        console.log('Quiz started:', quizId);
        if (window.PPTracker) window.PPTracker.trackQuizStart(quizId);
        if (window.PepTracking) window.PepTracking.track('Quiz Started', { quiz_id: quizId });
    });

    // Track individual quiz answers
    $wire.on('quiz-answer', (data) => {
        const payload = Array.isArray(data) ? data[0] : data;
        if (window.PepTracking) {
            window.PepTracking.track('Quiz Answer', {
                question: payload.question || '',
                answer: payload.answer || '',
                step: payload.step || 0,
                quiz_id: payload.quizId || null,
            });
        }
    });

    // Fire "Completed Quiz" event with peptide_match at email capture.
    // This is the event post-submission flows listen for to branch on peptide.
    $wire.on('completed-quiz-event', (data) => {
        const payload = Array.isArray(data) ? data[0] : data;
        console.log('Completed Quiz event:', payload);
        if (window.PepTracking) {
            // Identify first so the event is attached to the right person
            if (payload.email) {
                window.PepTracking.identify(payload.email, {
                    email: payload.email,
                    peptide_match: payload.peptide_match || null,
                    goal: payload.goal || null,
                    level: payload.level || null,
                });
            }
            // Fire the event — strip email from properties (already on profile)
            const eventData = Object.assign({}, payload);
            delete eventData.email;
            window.PepTracking.track('Completed Quiz', eventData);
        }
    });

    // Legacy quiz-completed event (kept for PPTracker compatibility)
    $wire.on('quiz-completed', (data) => {
        console.log('Quiz completed (final slide reached):', data);
        const payload = Array.isArray(data) ? data[0] : data;
        if (window.PPTracker) window.PPTracker.trackQuizComplete(payload.quizId, payload);
        // Quiz completed — disable abandonment beacon
        window.__quizCompleted = true;
    });

    // Mark quiz as abandoned when user navigates away mid-quiz
    window.addEventListener('beforeunload', () => {
        if (window.__quizCompleted) return;
        const responseId = $wire.responseId;
        if (!responseId) return;
        const data = new FormData();
        data.append('response_id', responseId);
        data.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        navigator.sendBeacon('{{ route("quiz.abandon") }}', data);
    });
</script>
@endscript
