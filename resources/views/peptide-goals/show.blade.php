@php $accent = $config['accent']; @endphp
<x-public-layout :title="$config['seo_title']" :description="$config['seo_description']">

    @push('head')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'ItemList',
                    'name' => $config['h1'],
                    'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
                    'itemListElement' => $picks->map(fn ($p, $i) => [
                        '@type' => 'ListItem',
                        'position' => $i + 1,
                        'name' => $p['peptide']->name,
                        'url' => route('peptides.show', $p['peptide']->slug),
                    ])->values()->all(),
                ],
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => collect($config['faqs'])->map(fn ($f) => [
                        '@type' => 'Question', 'name' => $f['q'],
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
                    ])->values()->all(),
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Best Peptides', 'item' => route('peptide-goals.index')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $config['h1'], 'item' => route('peptide-goals.show', $config['slug'])],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush

    {{-- Hero --}}
    <section class="py-12" style="background: linear-gradient(135deg, {{ $accent }}14, #ffffff);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('peptide-goals.index') }}" class="hover:text-gray-700">Best Peptides</a>
                <span class="mx-1.5">/</span><span class="text-gray-700">{{ $config['short'] }}</span>
            </nav>
            <div class="flex items-center gap-4 mb-4">
                <span class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background-color: {{ $accent }}1f;">{{ $config['emoji'] }}</span>
                <h1 class="text-2xl md:text-4xl font-bold text-gray-900">{{ $config['h1'] }}</h1>
            </div>
            <p class="text-gray-700 leading-relaxed">{{ $config['intro'] }}</p>
        </div>
    </section>

    {{-- Ranked picks --}}
    <section class="py-12 bg-cream-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @foreach($picks as $i => $pick)
                @php $pep = $pick['peptide']; $injectable = stripos($pep->route ?? '', 'inject') !== false; @endphp
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 sm:p-6">
                    <div class="flex gap-4">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white shrink-0" style="background-color: {{ $accent }};">{{ $i + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-baseline gap-x-2 gap-y-0.5 mb-1">
                                <h2 class="text-lg font-bold text-gray-900">{{ $pep->name }}</h2>
                                @if($pep->abbreviation)<span class="text-sm text-gray-400">{{ $pep->abbreviation }}</span>@endif
                                @if($pep->typical_dose)<span class="text-xs text-gray-400">· {{ $pep->typical_dose }}</span>@endif
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed mb-3">{{ $pick['why'] }}</p>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-sm">
                                <a href="{{ route('peptides.show', $pep->slug) }}" class="font-medium text-primary-600 hover:underline inline-flex items-center gap-1">Read the guide
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
                                @if($injectable)
                                    <a href="{{ route('calculators.show', $pep->slug.'-dosage') }}" class="font-medium text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">💉 Dosage calculator</a>
                                @endif
                                <a href="{{ \App\Services\BioLinxService::urlForPeptide($pep, 'best-for-'.$config['slug']) }}" target="_blank" rel="nofollow sponsored noopener" class="font-medium hover:underline inline-flex items-center gap-1" style="color: {{ $accent }};">Shop at BioLinx ↗</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $config['h1'] }} — FAQ</h2>
            <div class="space-y-3" x-data="{ open: 0 }">
                @foreach($config['faqs'] as $i => $faq)
                    <div class="bg-surface-50 rounded-xl border border-gray-200 overflow-hidden">
                        <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}" class="w-full flex items-center justify-between gap-4 text-left px-5 py-4 font-semibold text-gray-900">
                            <span>{{ $faq['q'] }}</span>
                            <svg class="w-5 h-5 text-gray-400 shrink-0 transition-transform" :class="open === {{ $i }} && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open === {{ $i }}" x-transition.opacity class="px-5 pb-4 -mt-1 text-gray-600 leading-relaxed">{{ $faq['a'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Email capture --}}
    @include('calculators.partials._email-capture', ['source' => 'goal:'.$config['slug']])

    {{-- Other goals --}}
    <section class="py-12 bg-surface-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-gray-900 mb-5">Explore other research goals</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($others as $g)
                    <a href="{{ route('peptide-goals.show', $g['slug']) }}" class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 hover:border-primary-300 hover:shadow-sm transition-all">
                        <span class="text-lg">{{ $g['emoji'] }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ $g['short'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Disclaimer --}}
    <section class="pb-14 bg-surface-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 flex gap-3">
                <svg aria-hidden="true" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <p class="text-amber-700 text-sm">Rankings reflect the strength and depth of published research, not a recommendation to use any compound. Everything here is for educational and research purposes only — not medical advice. Most compounds are research chemicals not approved for human use; always consult a qualified professional.</p>
            </div>
        </div>
    </section>
</x-public-layout>
