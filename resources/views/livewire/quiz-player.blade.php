<div class="quiz-player max-w-2xl mx-auto">
    @if(!$completed)
        <!-- Progress Bar -->
        @if(($quiz->settings ?? [])['show_progress_bar'] ?? true)
            <div class="mb-6">
                <div class="flex justify-end text-sm text-gray-600 mb-2">
                    <span>{{ $this->progress }}%</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-brand-gold transition-all duration-300" style="width: {{ $this->progress }}%"></div>
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
                        class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold disabled:opacity-50 @error('email') border-red-500 ring-red-500 @enderror"
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

                @default
                    {{-- Fallback: render as question (backwards compatible) --}}
                    @include('livewire.quiz-slides.question')
            @endswitch
        @endif

        {{-- Save Progress Indicator --}}
        @if($currentStep > 0 && $this->currentSlideType !== 'loading')
            <div
                class="flex justify-center mt-4"
                x-data="{ state: 'idle', timer: null }"
                x-init="
                    Livewire.hook('commit', ({ succeed }) => {
                        state = 'saving';
                        clearTimeout(timer);
                        succeed(() => {
                            state = 'saved';
                            timer = setTimeout(() => state = 'idle', 3000);
                        });
                    })
                "
            >
                {{-- Saving... --}}
                <span
                    x-show="state === 'saving'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="inline-flex items-center gap-1.5 text-xs text-gray-400"
                >
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    Saving...
                </span>

                {{-- Saved --}}
                <span
                    x-show="state === 'saved'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="inline-flex items-center gap-1.5 text-xs text-gray-400"
                >
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Progress saved
                </span>

                {{-- Idle (muted) --}}
                <span
                    x-show="state === 'idle'"
                    x-transition:enter="transition ease-in duration-700"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="inline-flex items-center gap-1.5 text-xs text-gray-300"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Your progress is saved
                </span>
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
</div>

@script
<script>
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
