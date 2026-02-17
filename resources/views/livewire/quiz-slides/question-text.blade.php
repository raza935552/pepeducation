{{-- Question (Text Input) Slide --}}
<div class="card p-8" wire:key="slide-text-{{ $currentStep }}">
    <h2 class="text-2xl font-semibold mb-6">{{ $this->currentSlide['question_text'] }}</h2>

    @if(!empty($this->currentSlide['question_subtext']))
        <p class="text-gray-600 mb-6">{{ $this->currentSlide['question_subtext'] }}</p>
    @endif

    <form wire:submit="submitTextAnswer" class="space-y-4">
        <textarea
            wire:model="textAnswer"
            rows="3"
            required
            placeholder="{{ $this->currentSlide['settings']['placeholder'] ?? 'Type your answer...' }}"
            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"
        ></textarea>
        @error('textAnswer') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

        <div class="flex items-center justify-between">
            @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
                <button type="button" wire:click="previousStep" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                    <svg aria-hidden="true" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </button>
            @else
                <span></span>
            @endif
            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary disabled:opacity-50">
                <span wire:loading.remove wire:target="submitTextAnswer">Continue</span>
                <span wire:loading wire:target="submitTextAnswer">Submitting...</span>
            </button>
        </div>
    </form>
</div>
