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

    {{-- Hero --}}
    <section class="bg-gradient-to-br from-dark-900 via-dark-800 to-primary-900/30 text-white py-10 md:py-14">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-xs sm:text-sm text-surface-400 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                <svg aria-hidden="true" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('peptides.index') }}" class="hover:text-white transition-colors">Peptides</a>
                <svg aria-hidden="true" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-white">Compare</span>
            </nav>
            @if($peptideA && $peptideB)
                <h1 class="text-3xl md:text-5xl font-bold mb-2">
                    {{ $peptideA->name }} <span class="text-primary-300">vs</span> {{ $peptideB->name }}
                </h1>
                <p class="text-sm md:text-base text-surface-300 max-w-2xl">A clear side-by-side breakdown so you can decide which peptide fits your goal.</p>
            @else
                <h1 class="text-3xl md:text-5xl font-bold mb-2">Compare Any Two Peptides</h1>
                <p class="text-sm md:text-base text-surface-300 max-w-2xl">Pick two peptides below and see them side-by-side: dosing, mechanism, benefits, safety, and more.</p>
            @endif
        </div>
    </section>

    {{-- Picker --}}
    <section class="bg-white border-b border-surface-200 py-5 sticky top-16 z-30 shadow-sm" x-data="comparePicker(@js($peptideA?->slug), @js($peptideB?->slug))">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row gap-3 sm:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Peptide 1</label>
                    <select x-model="slugA" @change="navigate()"
                        class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm font-medium">
                        <option value="">Pick a peptide...</option>
                        @foreach($allPeptides as $p)
                            <option value="{{ $p->slug }}">{{ $p->name }}{{ $p->abbreviation && $p->abbreviation !== $p->name ? ' ('.$p->abbreviation.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hidden sm:flex items-end pb-2.5">
                    <span class="text-xl font-bold text-gray-300">VS</span>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Peptide 2</label>
                    <select x-model="slugB" @change="navigate()"
                        class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 text-sm font-medium">
                        <option value="">Pick a peptide...</option>
                        @foreach($allPeptides as $p)
                            <option value="{{ $p->slug }}">{{ $p->name }}{{ $p->abbreviation && $p->abbreviation !== $p->name ? ' ('.$p->abbreviation.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                @if($peptideA && $peptideB)
                <button type="button" @click="swap()"
                    class="hidden sm:inline-flex items-center gap-1 px-3 py-2.5 rounded-lg border border-surface-200 bg-white text-gray-600 hover:bg-surface-50 text-sm">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Swap
                </button>
                @endif
            </div>
        </div>
    </section>

    @if(!$peptideA || !$peptideB)
        <section class="py-12 bg-surface-50">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl border border-surface-200 p-8 text-center">
                    <svg aria-hidden="true" class="w-12 h-12 mx-auto text-primary-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Pick two peptides to start</h2>
                    <p class="text-sm text-gray-500 mb-6">Use the dropdowns above, or jump straight into a popular comparison:</p>
                    <div class="flex flex-wrap justify-center gap-2 max-w-2xl mx-auto">
                        @foreach([['tirzepatide','semaglutide'],['bpc-157','tb-500'],['cjc-1295','sermorelin'],['retatrutide','tirzepatide'],['mk-677','ipamorelin'],['semaglutide','cagrilintide'],['ipamorelin','sermorelin'],['ghk-cu','ahk-cu'],['selank','semax']] as [$a,$b])
                            @php
                                $pa = \App\Models\Peptide::where('slug',$a)->first();
                                $pb = \App\Models\Peptide::where('slug',$b)->first();
                            @endphp
                            @if($pa && $pb)
                                <a href="{{ route('peptides.compare.pair', ['slugA' => $a, 'slugB' => $b]) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary-50 text-primary-700 text-sm font-medium hover:bg-primary-100 transition">
                                    {{ $pa->name }} vs {{ $pb->name }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @else
        @php
            // Field groups for organized comparison
            $groups = [
                'Quick Reference' => [
                    ['Type', $peptideA->type, $peptideB->type, 'icon-tag'],
                    ['Route', $peptideA->route, $peptideB->route, 'icon-route'],
                    ['Typical Dose', $peptideA->typical_dose, $peptideB->typical_dose, 'icon-dose'],
                    ['Frequency', $peptideA->dose_frequency, $peptideB->dose_frequency, 'icon-clock'],
                    ['Cycle', $peptideA->cycle, $peptideB->cycle, 'icon-cycle'],
                    ['Storage', $peptideA->storage, $peptideB->storage, 'icon-storage'],
                ],
                'Pharmacokinetics' => [
                    ['Half-Life', $peptideA->half_life, $peptideB->half_life, 'icon-half'],
                    ['Peak Time', $peptideA->peak_time, $peptideB->peak_time, 'icon-peak'],
                    ['Clearance Time', $peptideA->clearance_time, $peptideB->clearance_time, 'icon-clearance'],
                ],
                'Molecular' => [
                    ['Molecular Weight', $peptideA->molecular_weight ? $peptideA->molecular_weight.' Da' : null, $peptideB->molecular_weight ? $peptideB->molecular_weight.' Da' : null, 'icon-weight'],
                    ['Amino Acid Length', $peptideA->amino_acid_length, $peptideB->amino_acid_length, 'icon-chain'],
                    ['Research Status', ucfirst(str_replace('_', ' ', $peptideA->research_status ?? '-')), ucfirst(str_replace('_', ' ', $peptideB->research_status ?? '-')), 'icon-research'],
                ],
            ];
        @endphp

        {{-- Top peptide cards --}}
        <section class="py-8 md:py-10 bg-surface-50">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach([['p' => $peptideA, 'label' => 'Peptide 1', 'gradient' => 'from-primary-500/15 to-primary-50'], ['p' => $peptideB, 'label' => 'Peptide 2', 'gradient' => 'from-emerald-500/15 to-emerald-50']] as $card)
                        @php $p = $card['p']; @endphp
                        <div class="relative bg-white rounded-2xl border border-surface-200 overflow-hidden">
                            <div class="bg-gradient-to-br {{ $card['gradient'] }} p-6 border-b border-surface-200">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">{{ $card['label'] }}</p>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">
                                    <a href="{{ route('peptides.show', $p->slug) }}" class="hover:text-primary-600 transition-colors">{{ $p->name }}</a>
                                </h2>
                                @if($p->full_name && $p->full_name !== $p->name)
                                    <p class="text-sm text-gray-600 mb-3">{{ $p->full_name }}</p>
                                @endif
                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    @foreach($p->categories->take(3) as $cat)
                                        <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-full" style="background:{{ $cat->color }}20; color:{{ $cat->color }};">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @if($p->overview)
                                <div class="p-6">
                                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-4">{{ \Illuminate\Support\Str::limit(strip_tags($p->overview), 220) }}</p>
                                    <a href="{{ route('peptides.show', $p->slug) }}" class="inline-flex items-center gap-1 mt-3 text-sm font-medium text-primary-600 hover:text-primary-700">
                                        Full {{ $p->name }} guide
                                        <svg aria-hidden="true" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Detailed comparison sections --}}
        <section class="py-8 md:py-12 bg-white">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-8">
                @foreach($groups as $groupName => $rows)
                    @php $hasContent = collect($rows)->contains(fn($r) => !empty($r[1]) || !empty($r[2])); @endphp
                    @if($hasContent)
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-primary-500 rounded-full"></span>
                            {{ $groupName }}
                        </h2>

                        {{-- Desktop: clean 3-column table layout --}}
                        <div class="hidden sm:block bg-white rounded-2xl border border-surface-200 overflow-hidden shadow-sm">
                            {{-- Column headers showing peptide names --}}
                            <div class="grid grid-cols-[180px_1fr_1fr] bg-surface-50 border-b border-surface-200">
                                <div class="px-5 py-3"></div>
                                <div class="px-5 py-3 border-l border-surface-200">
                                    <p class="text-xs font-bold uppercase tracking-wider text-primary-700">{{ $peptideA->name }}</p>
                                </div>
                                <div class="px-5 py-3 border-l border-surface-200">
                                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">{{ $peptideB->name }}</p>
                                </div>
                            </div>
                            {{-- Data rows --}}
                            @php $rowIndex = 0; @endphp
                            @foreach($rows as $row)
                                @if(!empty($row[1]) || !empty($row[2]))
                                <div class="grid grid-cols-[180px_1fr_1fr] {{ $rowIndex % 2 === 0 ? 'bg-white' : 'bg-surface-50/50' }} border-t border-surface-100">
                                    <div class="px-5 py-4 text-sm font-semibold text-gray-600">{{ $row[0] }}</div>
                                    <div class="px-5 py-4 text-sm text-gray-900 border-l border-surface-200 break-words">{{ $row[1] ?: '—' }}</div>
                                    <div class="px-5 py-4 text-sm text-gray-900 border-l border-surface-200 break-words">{{ $row[2] ?: '—' }}</div>
                                </div>
                                @php $rowIndex++; @endphp
                                @endif
                            @endforeach
                        </div>

                        {{-- Mobile: stacked card per row --}}
                        <div class="sm:hidden space-y-3">
                            @foreach($rows as $row)
                                @if(!empty($row[1]) || !empty($row[2]))
                                <div class="bg-white rounded-xl border border-surface-200 overflow-hidden shadow-sm">
                                    <div class="px-4 py-2.5 bg-surface-50 border-b border-surface-200">
                                        <p class="text-xs font-bold uppercase tracking-wider text-gray-600">{{ $row[0] }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 divide-x divide-surface-200">
                                        <div class="p-4">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-primary-700 mb-1">{{ $peptideA->name }}</p>
                                            <p class="text-sm text-gray-900">{{ $row[1] ?: '—' }}</p>
                                        </div>
                                        <div class="p-4">
                                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 mb-1">{{ $peptideB->name }}</p>
                                            <p class="text-sm text-gray-900">{{ $row[2] ?: '—' }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach

                {{-- Mechanism section --}}
                @if($peptideA->mechanism_of_action || $peptideB->mechanism_of_action)
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-primary-500 rounded-full"></span>
                        How Each Works
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="bg-primary-50/40 border border-primary-200 rounded-2xl p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-primary-700 mb-2">{{ $peptideA->name }}</p>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ \Illuminate\Support\Str::limit(strip_tags($peptideA->mechanism_of_action ?? '—'), 350) }}</p>
                        </div>
                        <div class="bg-emerald-50/40 border border-emerald-200 rounded-2xl p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-700 mb-2">{{ $peptideB->name }}</p>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ \Illuminate\Support\Str::limit(strip_tags($peptideB->mechanism_of_action ?? '—'), 350) }}</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Key benefits --}}
                @if((is_array($peptideA->key_benefits) && count($peptideA->key_benefits)) || (is_array($peptideB->key_benefits) && count($peptideB->key_benefits)))
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-primary-500 rounded-full"></span>
                        Key Benefits
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="bg-white border border-surface-200 rounded-2xl p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-primary-700 mb-3">{{ $peptideA->name }}</p>
                            @if(is_array($peptideA->key_benefits) && count($peptideA->key_benefits))
                                <ul class="space-y-2">
                                    @foreach(array_slice($peptideA->key_benefits, 0, 5) as $b)
                                        <li class="flex items-start gap-2 text-sm text-gray-700">
                                            <svg aria-hidden="true" class="w-4 h-4 text-primary-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            {{ $b }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-400">No data available.</p>
                            @endif
                        </div>
                        <div class="bg-white border border-surface-200 rounded-2xl p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-700 mb-3">{{ $peptideB->name }}</p>
                            @if(is_array($peptideB->key_benefits) && count($peptideB->key_benefits))
                                <ul class="space-y-2">
                                    @foreach(array_slice($peptideB->key_benefits, 0, 5) as $b)
                                        <li class="flex items-start gap-2 text-sm text-gray-700">
                                            <svg aria-hidden="true" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            {{ $b }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-400">No data available.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Safety warnings --}}
                @if((is_array($peptideA->safety_warnings) && count($peptideA->safety_warnings)) || (is_array($peptideB->safety_warnings) && count($peptideB->safety_warnings)))
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-red-500 rounded-full"></span>
                        Safety Warnings
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="bg-red-50/30 border border-red-200 rounded-2xl p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-red-700 mb-3">{{ $peptideA->name }}</p>
                            @if(is_array($peptideA->safety_warnings) && count($peptideA->safety_warnings))
                                <ul class="space-y-2">
                                    @foreach(array_slice($peptideA->safety_warnings, 0, 4) as $w)
                                        <li class="flex items-start gap-2 text-sm text-gray-700">
                                            <svg aria-hidden="true" class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            {{ $w }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-400">No data available.</p>
                            @endif
                        </div>
                        <div class="bg-red-50/30 border border-red-200 rounded-2xl p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-red-700 mb-3">{{ $peptideB->name }}</p>
                            @if(is_array($peptideB->safety_warnings) && count($peptideB->safety_warnings))
                                <ul class="space-y-2">
                                    @foreach(array_slice($peptideB->safety_warnings, 0, 4) as $w)
                                        <li class="flex items-start gap-2 text-sm text-gray-700">
                                            <svg aria-hidden="true" class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            {{ $w }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-400">No data available.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </section>

        {{-- Buy CTAs --}}
        <section class="py-8 bg-surface-50">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Where to source each one</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-buy-cta :peptide="$peptideA" context="compare-A" variant="banner" />
                    <x-buy-cta :peptide="$peptideB" context="compare-B" variant="banner" />
                </div>
            </div>
        </section>

        {{-- Other comparisons --}}
        <section class="py-8 bg-white border-t border-surface-200">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <p class="text-sm font-semibold text-gray-700 mb-3">Other popular comparisons:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach([['tirzepatide','semaglutide'],['bpc-157','tb-500'],['cjc-1295','sermorelin'],['retatrutide','tirzepatide'],['mk-677','ipamorelin']] as [$a,$b])
                        @if(($a !== $peptideA->slug || $b !== $peptideB->slug) && ($a !== $peptideB->slug || $b !== $peptideA->slug))
                            @php
                                $pa = \App\Models\Peptide::where('slug',$a)->first();
                                $pb = \App\Models\Peptide::where('slug',$b)->first();
                            @endphp
                            @if($pa && $pb)
                            <a href="{{ route('peptides.compare.pair', ['slugA' => $a, 'slugB' => $b]) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-surface-50 hover:bg-primary-50 text-gray-700 hover:text-primary-700 text-sm border border-surface-200 hover:border-primary-300 transition">
                                {{ $pa->name }} vs {{ $pb->name }}
                            </a>
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @push('scripts')
    <script>
    function comparePicker(initialA, initialB) {
        return {
            slugA: initialA || '',
            slugB: initialB || '',
            navigate() {
                if (this.slugA && this.slugB && this.slugA !== this.slugB) {
                    window.location = '/peptides/compare/' + encodeURIComponent(this.slugA) + '/vs/' + encodeURIComponent(this.slugB);
                }
            },
            swap() {
                if (this.slugA && this.slugB) {
                    window.location = '/peptides/compare/' + encodeURIComponent(this.slugB) + '/vs/' + encodeURIComponent(this.slugA);
                }
            },
        };
    }
    </script>
    @endpush
</x-public-layout>
