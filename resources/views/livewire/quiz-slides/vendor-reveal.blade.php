{{-- Vendor Reveal Slide — Store Comparison via StackProduct --}}
<div class="card p-8" wire:key="slide-vendor-reveal-{{ $currentStep }}">

    @php
        $result = $this->resultsBankEntry;
        $stackProduct = $this->stackProduct;
        $preferredCategory = $this->preferredStoreCategory;

        // Filter stores: prefer user's buying priority category, fall back to all in-stock
        if ($stackProduct) {
            $inStockStores = $stackProduct->stores->where('pivot.is_in_stock', true);
            if ($preferredCategory) {
                $filtered = $inStockStores->where('category', $preferredCategory);
                // Only use filtered if it has results; otherwise show all
                if ($filtered->count()) {
                    $stackProduct->setRelation('stores', $filtered);
                }
            }
        }
    @endphp

    @if($stackProduct && $stackProduct->stores->where('pivot.is_in_stock', true)->count())
        @php
            $categoryLabels = \App\Models\StackStore::CATEGORIES;
            $categoryLabel = $preferredCategory ? ($categoryLabels[$preferredCategory] ?? null) : null;

            // Category-specific descriptions
            $categoryDescriptions = [
                'telehealth' => 'Licensed clinics with doctor consultations and prescriptions',
                'research_grade' => 'Lab-tested peptides for research purposes',
                'affordable' => 'Budget-friendly peptide suppliers',
            ];
            $categoryDesc = $preferredCategory ? ($categoryDescriptions[$preferredCategory] ?? null) : null;

            // Category-specific icons and colors
            $categoryStyles = [
                'telehealth' => ['bg' => 'bg-purple-50', 'icon' => 'text-purple-500', 'badge' => 'bg-purple-100 text-purple-700'],
                'research_grade' => ['bg' => 'bg-blue-50', 'icon' => 'text-blue-500', 'badge' => 'bg-blue-100 text-blue-700'],
                'affordable' => ['bg' => 'bg-green-50', 'icon' => 'text-green-500', 'badge' => 'bg-green-100 text-green-700'],
            ];
            $style = $categoryStyles[$preferredCategory] ?? ['bg' => 'bg-blue-50', 'icon' => 'text-blue-500', 'badge' => 'bg-blue-100 text-blue-700'];
        @endphp

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 {{ $style['bg'] }} rounded-full flex items-center justify-center mx-auto mb-4">
                @if($preferredCategory === 'telehealth')
                    <svg aria-hidden="true" class="w-8 h-8 {{ $style['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
                    </svg>
                @else
                    <svg aria-hidden="true" class="w-8 h-8 {{ $style['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.29 48.29 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                    </svg>
                @endif
            </div>
            @if($categoryLabel)
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide mb-3 {{ $style['badge'] }}">{{ $categoryLabel }}</span>
            @endif
            <h2 class="text-2xl font-bold text-gray-900">Where to Get {{ $result?->peptide_name ?? $stackProduct->name }}</h2>
            <p class="text-gray-500 mt-2">{{ $categoryDesc ?? 'Compare pricing from verified peptide suppliers' }}</p>
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
