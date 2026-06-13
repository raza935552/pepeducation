@php
    $widget = 'calculators.widgets.'.$config['slug'];
    $accent = $config['accent'];
@endphp

<x-public-layout :title="$config['seo_title']" :description="$config['seo_description']">

    @push('head')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebApplication',
                    'name' => $config['name'],
                    'description' => $config['seo_description'],
                    'url' => route('calculators.show', $config['slug']),
                    'applicationCategory' => 'HealthApplication',
                    'operatingSystem' => 'Web',
                    'browserRequirements' => 'Requires JavaScript',
                    'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
                    'publisher' => ['@type' => 'Organization', 'name' => config('app.name'), 'url' => url('/')],
                ],
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => collect($config['faqs'])->map(fn ($f) => [
                        '@type' => 'Question',
                        'name' => $f['q'],
                        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
                    ])->values()->all(),
                ],
                [
                    '@type' => 'HowTo',
                    'name' => 'How to use the '.$config['name'],
                    'step' => collect($config['how_to'])->values()->map(fn ($s, $i) => [
                        '@type' => 'HowToStep',
                        'position' => $i + 1,
                        'name' => $s['title'],
                        'text' => $s['body'],
                    ])->all(),
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Calculators', 'item' => route('calculators.index')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $config['name'], 'item' => route('calculators.show', $config['slug'])],
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
                <a href="{{ route('calculators.index') }}" class="hover:text-gray-700">Calculators</a>
                <span class="mx-1.5">/</span>
                <span class="text-gray-700">{{ $config['short'] }}</span>
            </nav>
            <div class="flex items-center gap-4">
                <span class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shrink-0"
                      style="background-color: {{ $accent }}1f;">{{ $config['emoji'] }}</span>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $config['name'] }}</h1>
                    <p class="text-gray-600">{{ $config['tagline'] }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Calculator widget --}}
    <section class="py-10 bg-cream-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @includeIf($widget)
        </div>
    </section>

    {{-- Intro + How-to guide --}}
    <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-gray-700 leading-relaxed mb-10">{{ $config['intro'] }}</p>

            <h2 class="text-2xl font-bold text-gray-900 mb-6">How to use the {{ $config['name'] }}</h2>
            <div class="space-y-4">
                @foreach($config['how_to'] as $i => $step)
                    <div class="bg-surface-50 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-900 mb-1.5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full text-white text-sm flex items-center justify-center shrink-0"
                                  style="background-color: {{ $accent }};">{{ $i + 1 }}</span>
                            {{ $step['title'] }}
                        </h3>
                        <p class="text-gray-600 ml-8">{{ $step['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-12 bg-surface-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Frequently asked questions</h2>
            <div class="space-y-3" x-data="{ open: 0 }">
                @foreach($config['faqs'] as $i => $faq)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                                class="w-full flex items-center justify-between gap-4 text-left px-5 py-4 font-semibold text-gray-900">
                            <span>{{ $faq['q'] }}</span>
                            <svg class="w-5 h-5 text-gray-400 shrink-0 transition-transform" :class="open === {{ $i }} && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open === {{ $i }}" x-transition.opacity class="px-5 pb-4 -mt-1 text-gray-600 leading-relaxed">{{ $faq['a'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Related peptide guides + Biolinx bridge --}}
    @if($related->isNotEmpty())
        <section class="py-12 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Related peptide guides</h2>
                <p class="text-gray-600 mb-6">Research-backed deep-dives on the compounds this tool relates to.</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($related as $pep)
                        <a href="{{ route('peptides.show', $pep->slug) }}"
                           class="block rounded-xl border border-gray-200 p-4 text-center hover:border-primary-300 hover:shadow-sm transition-all">
                            <span class="block font-semibold text-gray-900 text-sm">{{ $pep->name }}</span>
                            @if($pep->abbreviation)<span class="block text-xs text-gray-400 mt-0.5">{{ $pep->abbreviation }}</span>@endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Embed this calculator --}}
    @include('calculators.partials._embed-snippet')

    {{-- Email capture --}}
    @include('calculators.partials._email-capture', ['source' => 'calculator:'.$config['slug']])

    {{-- Disclaimer --}}
    <section class="pb-14 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 flex gap-3">
                <svg aria-hidden="true" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="text-amber-700 text-sm">
                    This calculator is for educational and research purposes only. It performs arithmetic and illustrates standard schedules — it is not medical advice, a diagnosis, or a recommendation to use any substance. Always consult a qualified healthcare professional and verify all calculations independently.
                </p>
            </div>
        </div>
    </section>
</x-public-layout>
