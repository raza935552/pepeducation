{{-- Vendor Reveal Slide — Store Comparison via StackProduct --}}
<div class="card p-8" wire:key="slide-vendor-reveal-{{ $currentStep }}">

    @php
        $result = $this->resultsBankEntry;
        $stackProduct = $this->stackProduct;
    @endphp

    @if($stackProduct && $stackProduct->stores->where('pivot.is_in_stock', true)->count())
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg aria-hidden="true" class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
                </svg>
            </div>
            <p class="text-sm text-blue-600 font-semibold uppercase tracking-wide mb-2">Trusted Vendors</p>
            <h2 class="text-2xl font-bold text-gray-900">Where to Get {{ $result?->peptide_name ?? $stackProduct->name }}</h2>
            <p class="text-gray-500 mt-2">Compare pricing from verified peptide suppliers</p>
        </div>

        {{-- Store Comparison — reuse existing partial --}}
        <div class="max-w-md mx-auto mb-8">
            @include('livewire.partials.store-comparison', ['product' => $stackProduct])
        </div>

        {{-- Trust note --}}
        <div class="text-center mb-6">
            <p class="text-xs text-gray-400">
                <svg class="w-3.5 h-3.5 inline-block mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                All vendors are third-party tested with certificates of analysis
            </p>
        </div>
    @else
        {{-- Fallback when no StackProduct linked or no stores in stock --}}
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg aria-hidden="true" class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-4">Vendor Information</h2>
            <p class="text-gray-500 mb-6">No vendor information is available for this recommendation yet.</p>
        </div>

        {{-- Fallback CTA from slide config --}}
        @if(!empty($this->currentSlide['cta_text']))
            <div class="text-center mb-6">
                <a href="{{ $this->currentSlide['cta_url'] ?? '#' }}" class="btn btn-primary inline-block">
                    {{ $this->currentSlide['cta_text'] }}
                </a>
            </div>
        @endif
    @endif

    {{-- Navigation --}}
    <div class="flex items-center justify-between mt-6">
        @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
            <button wire:click="previousStep" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                <svg aria-hidden="true" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
        @else
            <div></div>
        @endif

        <button wire:click="advanceSlide" class="btn btn-primary">
            Continue
            <svg aria-hidden="true" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>
