{{-- Email Capture Slide --}}
<div class="card p-8" wire:key="slide-email-{{ $currentStep }}">
    @if(!empty($this->currentSlide['content_title']))
        <h3 class="text-xl font-semibold mb-4">{{ $this->currentSlide['content_title'] }}</h3>
    @else
        <h3 class="text-xl font-semibold mb-4">Get your personalized results via email</h3>
    @endif

    @if(!empty($this->currentSlide['content_body']))
        <p class="text-gray-600 mb-6">{{ $this->currentSlide['content_body'] }}</p>
    @endif

    <form wire:submit="submitEmail" class="space-y-4">
        <input
            type="email"
            wire:model="email"
            placeholder="Enter your email"
            autocomplete="email"
            required
            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold disabled:opacity-50"
            wire:loading.attr="disabled"
        >
        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        <div class="flex gap-3">
            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary flex-1 disabled:opacity-50">
                <span wire:loading.remove wire:target="submitEmail">Continue</span>
                <span wire:loading wire:target="submitEmail">Submitting...</span>
            </button>
            <button type="button" wire:click="skipEmail" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Skip</button>
        </div>
    </form>

    @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
        <div class="flex justify-start mt-6">
            <button wire:click="previousStep" class="text-sm text-gray-500 hover:text-gray-700">
                <svg aria-hidden="true" class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
        </div>
    @endif
</div>
