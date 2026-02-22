{{-- Bridge (Final CTA) Slide — Context-Aware with Peptide/Vendor Info --}}
<div class="card p-8 text-center" wire:key="slide-bridge-{{ $currentStep }}">

    @php
        $resolved = $this->resolvedSlide;
        $result = $this->resultsBankEntry;
        $stackProduct = $this->stackProduct;
        $slideTitle = $resolved['content_title'] ?? null;
        $slideBody = $resolved['content_body'] ?? null;
        $slideCta = $resolved['cta_text'] ?? null;
        $slideCtaUrl = $resolved['cta_url'] ?? null;
    @endphp

    {{-- Icon --}}
    <div class="w-16 h-16 bg-brand-gold/10 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg aria-hidden="true" class="w-8 h-8 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
        </svg>
    </div>

    {{-- Title --}}
    @if($slideTitle)
        <h2 class="text-2xl font-bold mb-4">{{ $slideTitle }}</h2>
    @elseif($result && $stackProduct)
        <h2 class="text-2xl font-bold mb-4">Ready to Start with {{ $result->peptide_name }}?</h2>
    @else
        <h2 class="text-2xl font-bold mb-4">What Happens Next</h2>
    @endif

    {{-- Body --}}
    @if($slideBody)
        <div class="text-gray-700 leading-relaxed space-y-3 mb-6 max-w-lg mx-auto">
            {!! nl2br(e($slideBody)) !!}
        </div>
    @elseif($result && $stackProduct)
        <p class="text-gray-600 mb-6 max-w-lg mx-auto">
            Compare pricing across trusted vendors and find the best deal on <strong>{{ $result->peptide_name }}</strong> for your {{ $result->goal_label }} goals.
        </p>
    @endif

    {{-- CTA Button --}}
    @if($slideCta)
        <a href="{{ $slideCtaUrl ?? '#' }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary inline-block text-lg px-8 py-3">
            {{ $slideCta }}
        </a>
    @elseif($stackProduct)
        <a href="{{ route('stack-builder') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary inline-block text-lg px-8 py-3">
            Compare Vendors on Stack Builder
        </a>
    @else
        <button wire:click="advanceSlide" class="btn btn-primary text-lg px-8 py-3">Continue</button>
    @endif

    {{-- Finish Quiz button — always present to complete the quiz --}}
    <div class="mt-4">
        <button wire:click="advanceSlide" class="text-sm font-medium text-brand-gold hover:text-brand-gold/80 underline underline-offset-2">
            Finish Quiz &amp; See Results
        </button>
    </div>

    {{-- Back button --}}
    @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
        <div class="flex justify-center mt-4">
            <button wire:click="previousStep" class="text-sm text-gray-500 hover:text-gray-700">
                <svg aria-hidden="true" class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
        </div>
    @endif
</div>
