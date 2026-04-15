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

    {{-- Exit Intent Email Capture Popup --}}
    @if(!$exitEmailCaptured && !$completed)
    <div x-data="{ show: false, fired: false }" x-cloak
         x-init="
            const shouldTrigger = () => !fired && !$wire.exitEmailCaptured && !$wire.completed && !window.__quizCompleted;

            // Desktop: mouse leaves viewport toward top (close button / address bar)
            document.addEventListener('mouseleave', (e) => {
                if (e.clientY < 5 && shouldTrigger()) {
                    show = true;
                    fired = true;
                }
            });

            // Tab close / navigate away: show native 'are you sure?' dialog
            // If user stays, show our email popup
            window.addEventListener('beforeunload', (e) => {
                if (shouldTrigger()) {
                    e.preventDefault();
                    e.returnValue = '';
                    // Browser shows native dialog. If user clicks 'Stay',
                    // the page remains and we show our popup after a brief delay
                    setTimeout(() => {
                        if (shouldTrigger()) {
                            show = true;
                            fired = true;
                        }
                    }, 500);
                }
            });

            // Mobile: page going to background (tab switch, app switch)
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible' && !show && shouldTrigger()) {
                    // User came back to the tab - show popup now
                    show = true;
                    fired = true;
                }
            });
         "
         x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);"
         @keydown.escape.window="show = false">

        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 relative"
             @click.outside="show = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            {{-- Close button --}}
            <button @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Don't lose your progress!</h3>
                <p class="text-gray-600 mt-2 text-sm">Enter your email to save your quiz results. We'll send your personalized peptide recommendation.</p>
            </div>

            <form wire:submit="submitExitEmail" class="space-y-3">
                <input type="email" wire:model="exitEmail" placeholder="your@email.com" required autocomplete="email"
                    class="w-full rounded-xl border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-center py-3 text-lg">
                @error('exitEmail') <p class="text-red-500 text-xs text-center">{{ $message }}</p> @enderror
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="submitExitEmail">Save My Results</span>
                    <span wire:loading wire:target="submitExitEmail">Saving...</span>
                </button>
            </form>

            <button @click="show = false" class="block w-full text-center text-sm text-gray-400 hover:text-gray-600 mt-4">
                No thanks, I'll start over later
            </button>
        </div>
    </div>
    @endif
</div>

@script
<script>
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
        if (window.PPTracker) {
            window.PPTracker.trackQuizStart(quizId);
        }
    });

    // Track quiz completion
    $wire.on('quiz-completed', (data) => {
        console.log('Quiz completed:', data);
        if (window.PPTracker) {
            const payload = Array.isArray(data) ? data[0] : data;
            window.PPTracker.trackQuizComplete(payload.quizId, payload);
        }
        // Quiz completed — disable abandonment beacon
        window.__quizCompleted = true;
    });

    // Close exit popup when email is captured
    $wire.on('exit-email-captured', () => {
        const popup = document.querySelector('[x-data*="show: false, fired: false"]');
        if (popup && popup.__x) {
            popup.__x.$data.show = false;
        }
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
