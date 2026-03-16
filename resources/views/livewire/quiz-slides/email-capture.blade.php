{{-- Email Capture Slide --}}
@php $resolved = $this->resolvedSlide; @endphp
<div class="card p-8" wire:key="slide-email-{{ $currentStep }}">
    @if(!empty($resolved['content_title']))
        <h3 class="text-xl font-semibold mb-4">{{ $resolved['content_title'] }}</h3>
    @else
        <h3 class="text-xl font-semibold mb-4">Get your personalized results via email</h3>
    @endif

    @if(!empty($resolved['content_body']))
        <p class="text-gray-600 mb-6">{{ $resolved['content_body'] }}</p>
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
            <button type="button" wire:click="skipEmail" wire:loading.attr="disabled" wire:loading.class="opacity-50 pointer-events-none" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Skip</button>
        </div>
    </form>

    {{-- Optional CTA --}}
    @if(!empty($this->currentSlide['cta_text']) && !empty($this->currentSlide['cta_url']))
        <div class="text-center mt-4">
            <a href="{{ $this->currentSlide['cta_url'] }}" target="_blank" rel="noopener noreferrer" class="text-sm text-brand-gold hover:underline">
                {{ $this->currentSlide['cta_text'] }}
            </a>
        </div>
    @endif

    @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
        <div class="flex justify-start mt-6">
            <button wire:click="previousStep" wire:loading.attr="disabled" wire:loading.class="opacity-50 pointer-events-none" class="text-sm text-gray-500 hover:text-gray-700">
                <svg aria-hidden="true" class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
        </div>
    @endif
</div>
