{{-- Vendor Reveal Slide — Two-Section Layout (Doctor + Research) --}}
<div class="card p-8" wire:key="slide-vendor-reveal-{{ $currentStep }}">

    @php
        $resolved = $this->resolvedSlide;
        $settings = $resolved['settings'] ?? [];
        $result = $this->resultsBankEntry;
        $stackProduct = $this->stackProduct;
        $peptideName = $result?->peptide_name ?? ($stackProduct->name ?? 'this peptide');

        // FDA-approved peptides (available via telehealth/doctor)
        $fdaApprovedPeptides = [
            'Semaglutide',
            'Tirzepatide',
            'Liraglutide',
            'Bremelanotide (PT-141)',
            'Tesamorelin',
            'Triptorelin',
            'Oxytocin',
        ];

        $isFdaApproved = false;
        if ($stackProduct) {
            foreach ($fdaApprovedPeptides as $approved) {
                if (stripos($stackProduct->name, $approved) !== false || stripos($approved, $stackProduct->name) !== false) {
                    $isFdaApproved = true;
                    break;
                }
            }
        }

        // Split stores by category, sorted by price ascending
        $allInStock = $stackProduct ? $stackProduct->stores->where('pivot.is_in_stock', true) : collect();
        $telehealthStores = $allInStock->where('category', 'telehealth')->sortBy('pivot.price')->values();

        // Research: BioLinx first, then others sorted by price (only if priced higher)
        $researchRaw = $allInStock->where('category', 'research_grade');
        $biolinx = $researchRaw->first(fn ($s) => $s->id === 15); // BiolinxLabs store ID
        $biolinxPrice = $biolinx?->pivot?->price;
        $others = $researchRaw->filter(fn ($s) => $s->id !== 15)
            ->when($biolinxPrice, fn ($c) => $c->filter(fn ($s) => (float) $s->pivot->price >= (float) $biolinxPrice))
            ->sortBy('pivot.price');
        $researchStores = $biolinx
            ? collect([$biolinx])->concat($others)->values()
            : $others->values();
    @endphp

    @if($stackProduct)
        {{-- Page header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg aria-hidden="true" class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $resolved['content_title'] ?? "Where to Get {$peptideName}" }}</h2>
            <p class="text-gray-500 mt-2">{{ $resolved['content_body'] ?? 'Compare pricing from verified peptide suppliers' }}</p>
        </div>

        {{-- ===== DOCTOR / TELEHEALTH SECTION ===== --}}
        <div class="mb-8">
            <div class="flex flex-col items-center text-center gap-2 mb-4">
                <div class="w-10 h-10 bg-purple-50 rounded-full flex items-center justify-center">
                    <svg aria-hidden="true" class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $settings['doctor_heading'] ?? 'Doctor / Telehealth' }}</h3>
                    <p class="text-xs text-gray-500">{{ $settings['doctor_description'] ?? 'Licensed clinics with doctor consultations and prescriptions' }}</p>
                </div>
            </div>

            @if($isFdaApproved && $telehealthStores->count())
                <div class="max-w-md mx-auto">
                    @include('livewire.partials.store-comparison', ['product' => $stackProduct, 'stores' => $telehealthStores])
                </div>
            @else
                <div class="p-4 rounded-xl bg-purple-50 border border-purple-100 text-center">
                    <p class="text-sm text-purple-700">
                        {{ $settings['doctor_unavailable_text'] ?? ($isFdaApproved
                            ? 'No telehealth vendors are available for this peptide at this time.'
                            : 'This peptide is not currently available through a doctor or telehealth provider.') }}
                    </p>
                </div>
            @endif
        </div>

        {{-- ===== RESEARCH SECTION ===== --}}
        <div class="mb-8">
            <div class="flex flex-col items-center text-center gap-2 mb-4">
                <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center">
                    <svg aria-hidden="true" class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.29 48.29 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $settings['research_heading'] ?? 'Research' }}</h3>
                    <p class="text-xs text-gray-500">{{ $settings['research_description'] ?? 'Lab-tested peptides for research purposes' }}</p>
                </div>
            </div>

            @if($researchStores->count())
                <div class="max-w-md mx-auto">
                    @include('livewire.partials.store-comparison', ['product' => $stackProduct, 'stores' => $researchStores])
                </div>
            @else
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 text-center">
                    <p class="text-sm text-blue-700">{{ $settings['research_unavailable_text'] ?? 'No research vendors are available for this peptide at this time.' }}</p>
                </div>
            @endif
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
        {{-- Fallback when no StackProduct linked --}}
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

    {{-- Navigation (back only — vendor reveal is the final slide) --}}
    <div class="flex items-center mt-6">
        @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
            <button wire:click="previousStep" wire:loading.attr="disabled" wire:loading.class="opacity-50 pointer-events-none" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                <svg aria-hidden="true" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
        @endif
    </div>
</div>
