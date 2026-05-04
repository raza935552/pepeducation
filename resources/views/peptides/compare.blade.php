@php
    $title = ($peptideA && $peptideB)
        ? $peptideA->name.' vs '.$peptideB->name.' - Side-by-Side Comparison'
        : 'Peptide Comparison Tool: Compare Any Two Peptides';
    $description = ($peptideA && $peptideB)
        ? 'Side-by-side comparison of '.$peptideA->name.' and '.$peptideB->name.': dosing, half-life, mechanism, benefits, safety, and use cases.'
        : 'Compare any two peptides side-by-side: dosing, half-life, mechanism, safety profile, and clinical use. Free interactive tool.';
    $canonical = ($peptideA && $peptideB)
        ? route('peptides.compare.pair', ['slugA' => $peptideA->slug, 'slugB' => $peptideB->slug])
        : route('peptides.compare');
@endphp

<x-public-layout :title="$title" :description="$description" :canonical="$canonical">
    @push('head')
    @if($peptideA && $peptideB)
    @php
        $compareSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $title,
            'description' => $description,
            'url' => $canonical,
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => 2,
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => $peptideA->name,
                        'url' => route('peptides.show', $peptideA->slug),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $peptideB->name,
                        'url' => route('peptides.show', $peptideB->slug),
                    ],
                ],
            ],
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Peptides', 'item' => route('peptides.index')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => 'Compare', 'item' => route('peptides.compare')],
                    ['@type' => 'ListItem', 'position' => 4, 'name' => $peptideA->name.' vs '.$peptideB->name],
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($compareSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    @endif
    @endpush

    <section class="bg-gradient-to-br from-dark-900 via-dark-800 to-primary-900/30 text-white py-12 md:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-sm text-surface-400 mb-6">
                <a href="{{ route('peptides.index') }}" class="hover:text-white transition-colors">Peptides</a>
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-white">Compare</span>
            </nav>
            <h1 class="text-3xl md:text-5xl font-bold mb-3">
                @if($peptideA && $peptideB)
                    {{ $peptideA->name }} vs {{ $peptideB->name }}
                @else
                    Peptide Comparison Tool
                @endif
            </h1>
            <p class="text-base md:text-lg text-surface-300 max-w-2xl">
                @if($peptideA && $peptideB)
                    Side-by-side breakdown of {{ $peptideA->name }} and {{ $peptideB->name }}. Dosing, half-life, mechanism, safety, and clinical use.
                @else
                    Pick any two peptides to compare side-by-side. Dosing, half-life, mechanism, safety, and use cases at a glance.
                @endif
            </p>
        </div>
    </section>

    <section class="py-8 md:py-12 bg-surface-50">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8" x-data="comparePicker(@js($peptideA?->slug), @js($peptideB?->slug))">
            <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto_1fr] gap-3 sm:gap-4 items-end mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peptide A</label>
                    <select x-model="slugA" @change="navigate()"
                        class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Select a peptide...</option>
                        @foreach($allPeptides as $p)
                            <option value="{{ $p->slug }}">{{ $p->name }}{{ $p->abbreviation && $p->abbreviation !== $p->name ? ' ('.$p->abbreviation.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-center text-2xl font-bold text-gray-400 pb-2 hidden sm:block">VS</div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peptide B</label>
                    <select x-model="slugB" @change="navigate()"
                        class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Select a peptide...</option>
                        @foreach($allPeptides as $p)
                            <option value="{{ $p->slug }}">{{ $p->name }}{{ $p->abbreviation && $p->abbreviation !== $p->name ? ' ('.$p->abbreviation.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if(!$peptideA || !$peptideB)
                <div class="rounded-2xl bg-white border border-surface-200 p-8 text-center">
                    <p class="text-gray-500 mb-4">Select two peptides above to see them compared.</p>
                    <p class="text-sm text-gray-400">Or try a popular comparison:</p>
                    <div class="mt-3 flex flex-wrap justify-center gap-2">
                        <a href="{{ route('peptides.compare.pair', ['slugA' => 'tirzepatide', 'slugB' => 'semaglutide']) }}" class="px-3 py-1.5 rounded-full bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100">Tirzepatide vs Semaglutide</a>
                        <a href="{{ route('peptides.compare.pair', ['slugA' => 'bpc-157', 'slugB' => 'tb-500']) }}" class="px-3 py-1.5 rounded-full bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100">BPC-157 vs TB-500</a>
                        <a href="{{ route('peptides.compare.pair', ['slugA' => 'cjc-1295', 'slugB' => 'sermorelin']) }}" class="px-3 py-1.5 rounded-full bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100">CJC-1295 vs Sermorelin</a>
                        <a href="{{ route('peptides.compare.pair', ['slugA' => 'retatrutide', 'slugB' => 'tirzepatide']) }}" class="px-3 py-1.5 rounded-full bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100">Retatrutide vs Tirzepatide</a>
                        <a href="{{ route('peptides.compare.pair', ['slugA' => 'mk-677', 'slugB' => 'ipamorelin']) }}" class="px-3 py-1.5 rounded-full bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100">MK-677 vs Ipamorelin</a>
                    </div>
                </div>
            @else
                @php
                    $rows = [
                        ['Type', $peptideA->type, $peptideB->type],
                        ['Full Name', $peptideA->full_name, $peptideB->full_name],
                        ['Abbreviation', $peptideA->abbreviation, $peptideB->abbreviation],
                        ['Route', $peptideA->route, $peptideB->route],
                        ['Typical Dose', $peptideA->typical_dose, $peptideB->typical_dose],
                        ['Frequency', $peptideA->dose_frequency, $peptideB->dose_frequency],
                        ['Cycle', $peptideA->cycle, $peptideB->cycle],
                        ['Half-Life', $peptideA->half_life, $peptideB->half_life],
                        ['Peak Time', $peptideA->peak_time, $peptideB->peak_time],
                        ['Storage', $peptideA->storage, $peptideB->storage],
                        ['Research Status', ucfirst($peptideA->research_status ?? '-'), ucfirst($peptideB->research_status ?? '-')],
                        ['Molecular Weight', $peptideA->molecular_weight, $peptideB->molecular_weight],
                        ['Amino Acid Length', $peptideA->amino_acid_length, $peptideB->amino_acid_length],
                    ];
                @endphp
                <div class="bg-white rounded-2xl border border-surface-200 overflow-hidden">
                    <div class="grid grid-cols-[150px_1fr_1fr] sm:grid-cols-[200px_1fr_1fr] divide-y divide-surface-200">
                        <div class="contents">
                            <div class="p-4 bg-surface-50"></div>
                            <div class="p-4 bg-surface-50">
                                <a href="{{ route('peptides.show', $peptideA->slug) }}" class="font-bold text-gray-900 hover:text-primary-600 text-base sm:text-lg">{{ $peptideA->name }}</a>
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($peptideA->categories->take(2) as $cat)
                                        <span class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full" style="background:{{ $cat->color }}20; color:{{ $cat->color }};">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="p-4 bg-surface-50">
                                <a href="{{ route('peptides.show', $peptideB->slug) }}" class="font-bold text-gray-900 hover:text-primary-600 text-base sm:text-lg">{{ $peptideB->name }}</a>
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($peptideB->categories->take(2) as $cat)
                                        <span class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full" style="background:{{ $cat->color }}20; color:{{ $cat->color }};">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @foreach($rows as $row)
                            @if(!empty($row[1]) || !empty($row[2]))
                                <div class="contents">
                                    <div class="p-3 sm:p-4 text-xs sm:text-sm font-semibold text-gray-500 bg-surface-50/50">{{ $row[0] }}</div>
                                    <div class="p-3 sm:p-4 text-sm text-gray-800 break-words">{{ $row[1] ?: '-' }}</div>
                                    <div class="p-3 sm:p-4 text-sm text-gray-800 break-words">{{ $row[2] ?: '-' }}</div>
                                </div>
                            @endif
                        @endforeach

                        {{-- Mechanism of action (long-form) --}}
                        @if($peptideA->mechanism_of_action || $peptideB->mechanism_of_action)
                            <div class="contents">
                                <div class="p-3 sm:p-4 text-xs sm:text-sm font-semibold text-gray-500 bg-surface-50/50">Mechanism</div>
                                <div class="p-3 sm:p-4 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit(strip_tags($peptideA->mechanism_of_action ?? '-'), 200) }}</div>
                                <div class="p-3 sm:p-4 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit(strip_tags($peptideB->mechanism_of_action ?? '-'), 200) }}</div>
                            </div>
                        @endif

                        {{-- Key benefits --}}
                        @if(!empty($peptideA->key_benefits) || !empty($peptideB->key_benefits))
                            <div class="contents">
                                <div class="p-3 sm:p-4 text-xs sm:text-sm font-semibold text-gray-500 bg-surface-50/50">Key Benefits</div>
                                <div class="p-3 sm:p-4 text-sm text-gray-700">
                                    @if(is_array($peptideA->key_benefits))
                                        <ul class="list-disc pl-4 space-y-1">
                                            @foreach(array_slice($peptideA->key_benefits, 0, 4) as $b)
                                                <li>{{ $b }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="p-3 sm:p-4 text-sm text-gray-700">
                                    @if(is_array($peptideB->key_benefits))
                                        <ul class="list-disc pl-4 space-y-1">
                                            @foreach(array_slice($peptideB->key_benefits, 0, 4) as $b)
                                                <li>{{ $b }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Per-peptide CTAs --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-buy-cta :peptide="$peptideA" context="compare-A" variant="banner" />
                    <x-buy-cta :peptide="$peptideB" context="compare-B" variant="banner" />
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
    <script>
    function comparePicker(initialA, initialB) {
        return {
            slugA: initialA || '',
            slugB: initialB || '',
            navigate() {
                if (this.slugA && this.slugB) {
                    window.location = '/peptides/compare/' + encodeURIComponent(this.slugA) + '/vs/' + encodeURIComponent(this.slugB);
                } else if (this.slugA || this.slugB) {
                    // Stay on the current page, just update the dropdowns
                }
            },
        };
    }
    </script>
    @endpush
</x-public-layout>
