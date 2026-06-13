@php
    $name = $peptide->name;
    $abbr = $peptide->abbreviation;
    $brand = \App\Services\BioLinxService::name();
    $buyUrl = \App\Services\BioLinxService::urlForPeptide($peptide, 'where-to-buy-'.$peptide->slug);
    $injectable = stripos($peptide->route ?? '', 'inject') !== false;
    $accent = '#2563EB';

    $seoTitle = "Where to Buy $name (2026) — Research-Grade Source & Buying Guide";
    $seoDesc  = "Where to buy $name for research in 2026: how to check purity and COAs, what to avoid, and a vetted research-grade source. Educational buying guide — research use only.";

    $faqs = [
        ['q' => "Is $name legal to buy?", 'a' => "$name is sold for laboratory and research use only and is not approved for human consumption. Whether you can legally purchase it depends on your jurisdiction — always check local law. It is not a prescription product when sold for research."],
        ['q' => "How do I know if $name is real and pure?", 'a' => "Insist on a recent, batch-specific Certificate of Analysis (COA) from third-party HPLC/MS testing showing identity and a purity figure (typically ≥98%). A reputable source publishes a COA for every batch — if there is no COA, do not buy."],
        ['q' => "Do I need a prescription to buy $name?", 'a' => "No — when sold strictly for research it is not dispensed as a prescription medicine. That also means it carries no medical oversight, which is exactly why third-party testing and a COA matter."],
        ['q' => "How much does $name cost?", 'a' => "Price varies by vial size and supplier. The most meaningful comparison is price-per-verified-milligram with a COA — the cheapest vial is rarely the best value if purity is unproven."],
    ];
@endphp

<x-public-layout :title="$seoTitle" :description="$seoDesc">

    @push('head')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => collect($faqs)->map(fn ($f) => [
                        '@type' => 'Question', 'name' => $f['q'],
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
                    ])->values()->all(),
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Where to Buy', 'item' => route('where-to-buy')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => "Where to Buy $name", 'item' => route('where-to-buy.show', $peptide->slug)],
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
                <a href="{{ route('where-to-buy') }}" class="hover:text-gray-700">Where to Buy</a>
                <span class="mx-1.5">/</span><span class="text-gray-700">{{ $name }}</span>
            </nav>
            <h1 class="text-2xl md:text-4xl font-bold text-gray-900 mb-3">Where to Buy {{ $name }}{{ $abbr ? " ($abbr)" : '' }}</h1>
            <p class="text-gray-700 leading-relaxed">Sourcing research peptides safely comes down to one thing: proof of what is in the vial. Below is what to check before buying {{ $name }}, and a vetted research-grade source. For research and educational purposes only.</p>
        </div>
    </section>

    {{-- Recommended source --}}
    <section class="py-8 bg-cream-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-primary-200 bg-gradient-to-br from-primary-50 to-white p-6 sm:p-8">
                <p class="text-xs font-semibold uppercase tracking-wider text-primary-600 mb-1">Recommended research source</p>
                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $name }} at {{ $brand }}</h2>
                <ul class="text-sm text-gray-700 space-y-1.5 mb-5">
                    <li class="flex gap-2"><span class="text-green-500">✓</span> Batch-specific Certificate of Analysis (COA) for every product</li>
                    <li class="flex gap-2"><span class="text-green-500">✓</span> Third-party tested for identity and purity</li>
                    <li class="flex gap-2"><span class="text-green-500">✓</span> Properly lyophilized and shipped for stability</li>
                </ul>
                <a href="{{ $buyUrl }}" target="_blank" rel="nofollow sponsored noopener"
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg text-white font-semibold text-sm hover:opacity-90 transition-opacity" style="background-color: {{ $accent }};">
                    View {{ $name }} at {{ $brand }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- What to look for --}}
    <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">What to check before buying {{ $name }}</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach([
                    ['A batch-specific COA', "Demand a recent Certificate of Analysis tied to your exact batch — HPLC/MS identity and a purity figure (≥98% is the common benchmark). No COA, no sale."],
                    ['Third-party testing', "Independent lab testing matters more than a vendor's own claim. Reputable sources publish the testing lab and date."],
                    ['Correct form & storage', $injectable ? "$name should arrive as a sealed, lyophilized (freeze-dried) powder. Reconstitute with bacteriostatic water and store cold." : "Confirm the product form matches how $name is used, and follow the storage guidance to preserve potency."],
                    ['Transparent sourcing', "A real research vendor is upfront about purity, batch numbers and shipping — and does not make human-use or treatment claims."],
                ] as $item)
                    <div class="rounded-xl border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-1.5">{{ $item[0] }}</h3>
                        <p class="text-sm text-gray-600">{{ $item[1] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex flex-wrap gap-x-5 gap-y-2 text-sm">
                <a href="{{ route('peptides.show', $peptide->slug) }}" class="font-medium text-primary-600 hover:underline inline-flex items-center gap-1">Read the {{ $name }} research guide
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
                @if($injectable)
                    <a href="{{ route('calculators.show', $peptide->slug.'-dosage') }}" class="font-medium text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">💉 {{ $name }} dosage calculator</a>
                @endif
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-12 bg-surface-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Buying {{ $name }} — FAQ</h2>
            <div class="space-y-3" x-data="{ open: 0 }">
                @foreach($faqs as $i => $faq)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
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

    {{-- Disclaimer --}}
    <section class="pb-14 bg-surface-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 flex gap-3">
                <svg aria-hidden="true" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <p class="text-amber-700 text-sm">{{ $name }} is a research compound sold for laboratory and educational use only — not for human consumption, and not approved as a medicine. This page is informational and is not medical advice or a recommendation to use any substance. Always verify legality in your jurisdiction.</p>
            </div>
        </div>
    </section>
</x-public-layout>
