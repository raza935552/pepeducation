<x-public-layout
    :title="\App\Models\Setting::getValue('seo_pages', 'calculators_title', 'Peptide & Health Calculators')"
    :description="\App\Models\Setting::getValue('seo_pages', 'calculators_description', 'Free peptide and health calculators — reconstitution, BMI, GLP-1 titration, TRT, Melanotan, fitness (BMR/TDEE) and a multi-peptide protocol planner. Fast, accurate, research-focused.')"
>
    @push('head')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Peptide & Health Calculators',
            'description' => 'A suite of free peptide and health calculators for research and education.',
            'url' => route('calculators.index'),
            'hasPart' => collect($calculators)->map(fn ($c) => [
                '@type' => 'WebApplication',
                'name' => $c['name'],
                'applicationCategory' => 'HealthApplication',
                'operatingSystem' => 'Web',
                'url' => route('calculators.show', $c['slug']),
                'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
            ])->values()->all(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush

    {{-- Hero --}}
    <section class="bg-surface-100 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-primary-600 mb-3">Free Tools</p>
            <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">
                Peptide &amp; Health <span class="text-primary-500">Calculators</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Accurate, instant tools for reconstitution, dosing math, body metrics and protocol planning —
                built for research and education.
            </p>
        </div>
    </section>

    {{-- Card grid --}}
    <section class="py-12 bg-cream-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($calculators as $calc)
                    <a href="{{ route('calculators.show', $calc['slug']) }}"
                       class="group bg-white rounded-2xl border border-gray-200 p-6 flex flex-col hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"
                                  style="background-color: {{ $calc['accent'] }}1a;">
                                {{ $calc['emoji'] }}
                            </span>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 leading-tight">{{ $calc['name'] }}</h2>
                                <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">{{ $calc['category'] }}</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 flex-1">{{ $calc['tagline'] }}</p>
                        <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 group-hover:gap-2.5 transition-all"
                              style="color: {{ $calc['accent'] }};">
                            Use Calculator
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Per-peptide dosage calculators --}}
            @if(!empty($dosagePeptides) && $dosagePeptides->count())
                <div class="mt-14" x-data="{ q: '' }">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-5">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Dosage calculator for every peptide</h2>
                            <p class="text-gray-600">Pre-filled reconstitution &amp; syringe-unit math for {{ $dosagePeptides->count() }} injectable peptides.</p>
                        </div>
                        <input type="text" x-model="q" placeholder="Search peptides…"
                               class="rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:w-64">
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
                        @foreach($dosagePeptides as $pep)
                            <a href="{{ route('calculators.show', $pep->slug.'-dosage') }}"
                               x-show="q === '' || '{{ Str::lower($pep->name.' '.$pep->abbreviation) }}'.includes(q.toLowerCase())"
                               class="flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-2.5 bg-white hover:border-primary-300 hover:shadow-sm transition-all">
                                <span class="text-sm">💉</span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-medium text-gray-900 truncate">{{ $pep->name }}</span>
                                    <span class="block text-[11px] text-gray-400">Dosage calculator</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Disclaimer --}}
            <div class="mt-12 max-w-3xl mx-auto bg-amber-50 border border-amber-200 rounded-xl p-5 flex gap-3">
                <svg aria-hidden="true" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-amber-700 text-sm">
                    All calculators are for educational and research purposes only. They perform arithmetic and illustrate standard schedules — they are not medical advice. Always consult a qualified healthcare professional and verify every calculation independently.
                </p>
            </div>
        </div>
    </section>
</x-public-layout>
