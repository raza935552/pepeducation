{{-- Intermission Slide — informational content + Next button --}}
@php $resolved = $this->resolvedSlide; @endphp
<div class="card p-8" wire:key="slide-intermission-{{ $currentStep }}">
    @if(!empty($resolved['content_title']))
        <h2 class="text-2xl font-bold mb-4">{{ $resolved['content_title'] }}</h2>
    @endif

    @if(!empty($resolved['content_body']))
        <div class="text-gray-700 leading-relaxed space-y-3 mb-6">
            {!! nl2br(e($resolved['content_body'])) !!}
        </div>
    @endif

    @if(!empty($resolved['content_source']))
        <p class="text-xs text-gray-400 mb-6 italic">Source: {{ $resolved['content_source'] }}</p>
    @endif

    @include('livewire.partials.slide-accordion')

    {{-- Optional CTA --}}
    @if(!empty($this->currentSlide['cta_text']))
        <div class="text-center mb-6">
            @if(!empty($this->currentSlide['cta_url']))
                <a href="{{ $this->currentSlide['cta_url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary inline-block text-lg px-8 py-3">
                    {{ $this->currentSlide['cta_text'] }}
                </a>
            @else
                <button wire:click="advanceSlide" wire:loading.attr="disabled" wire:loading.class="opacity-50 pointer-events-none" class="btn btn-primary text-lg px-8 py-3">
                    {{ $this->currentSlide['cta_text'] }}
                </button>
            @endif
        </div>
    @endif

    <div class="flex items-center justify-between">
        @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
            <button wire:click="previousStep" wire:loading.attr="disabled" wire:loading.class="opacity-50 pointer-events-none" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                <svg aria-hidden="true" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
        @else
            <span></span>
        @endif
        <button wire:click="advanceSlide" wire:loading.attr="disabled" wire:loading.class="opacity-50 pointer-events-none" class="btn btn-primary">
            <span wire:loading.remove wire:target="advanceSlide">Continue</span>
            <span wire:loading wire:target="advanceSlide">Loading...</span>
            <svg aria-hidden="true" class="w-4 h-4 ml-2" wire:loading.remove wire:target="advanceSlide" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>
