<div class="quiz-player max-w-2xl mx-auto">
    @if(!$completed)
        <!-- Progress Bar -->
        @if(($quiz->settings ?? [])['show_progress_bar'] ?? true)
            <div class="mb-6">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Step {{ $currentStep + 1 }} of {{ count($questions) }}</span>
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
    });
</script>
@endscript
