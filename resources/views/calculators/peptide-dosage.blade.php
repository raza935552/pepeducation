@php
    $accent = '#2563EB';
    $td  = trim((string) $peptide->typical_dose);
    $fq  = trim((string) $peptide->dose_frequency);
    $hl  = trim((string) $peptide->half_life);
    $abbr = $peptide->abbreviation;
    $name = $peptide->name;

    $seoTitle = "$name Dosage Calculator — Reconstitution & Syringe Units";
    $seoDesc  = "Free $name dosage calculator. Reconstitute your $name vial with bacteriostatic water and get the exact concentration and insulin-syringe units to draw"
        . ($td ? " for a $td dose." : '.');

    // Generated, peptide-specific FAQ (also emitted as FAQPage schema).
    $faqs = array_values(array_filter([
        ['q' => "How do I reconstitute $name?",
         'a' => "Add bacteriostatic water to your lyophilized $name vial and let it dissolve gently — do not shake. Concentration (mcg/mL) = vial amount in mg × 1000 ÷ water volume in mL. Enter your vial size, water volume and dose above and the calculator returns the exact units to draw on a U-100 insulin syringe."],
        $td ? ['q' => "What is the typical $name dosage?",
         'a' => "A commonly referenced research dose for $name is $td" . ($fq ? ", used $fq" : '') . ". This is reference information only — the calculator pre-fills this so you can convert it into syringe units, and you can adjust it to whatever protocol you are studying."] : null,
        ['q' => "How many units of $name should I draw?",
         'a' => "It depends on how you reconstitute the vial. After adding water, divide your target dose (in mcg) by the concentration per unit. The calculator above does this automatically — change the water volume to make your dose land on an easy-to-read number of units."],
        $hl ? ['q' => "What is the half-life of $name?",
         'a' => "$name has an approximate half-life of $hl. Half-life affects how often a compound is dosed to maintain stable levels — shorter half-lives are typically dosed more frequently."] : null,
        ['q' => "What syringe is used for $name?",
         'a' => "A standard U-100 insulin syringe is used, marked in 100 units per mL (1 unit = 0.01 mL). The calculator gives your dose in those units so you can draw it directly."],
    ]));
@endphp

<x-public-layout :title="$seoTitle" :description="$seoDesc">

    @push('head')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebApplication',
                    'name' => "$name Dosage Calculator",
                    'description' => $seoDesc,
                    'url' => route('calculators.show', $peptide->slug.'-dosage'),
                    'applicationCategory' => 'HealthApplication',
                    'operatingSystem' => 'Web',
                    'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
                    'publisher' => ['@type' => 'Organization', 'name' => config('app.name'), 'url' => url('/')],
                ],
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
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Calculators', 'item' => route('calculators.index')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => "$name Dosage Calculator", 'item' => route('calculators.show', $peptide->slug.'-dosage')],
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
                <span class="text-gray-700">{{ $name }} Dosage</span>
            </nav>
            <div class="flex items-center gap-4">
                <span class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background-color: {{ $accent }}1f;">💉</span>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $name }} Dosage Calculator</h1>
                    <p class="text-gray-600">Reconstitute {{ $name }}{{ $abbr ? " ($abbr)" : '' }} and get the exact syringe units to draw.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Quick facts --}}
    @if($td || $fq || $hl || $peptide->route)
        <section class="bg-white border-b border-gray-100">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                @if($td)<div><p class="text-xs uppercase tracking-wide text-gray-400">Typical dose</p><p class="font-semibold text-gray-900">{{ $td }}</p></div>@endif
                @if($fq)<div><p class="text-xs uppercase tracking-wide text-gray-400">Frequency</p><p class="font-semibold text-gray-900">{{ $fq }}</p></div>@endif
                @if($peptide->route)<div><p class="text-xs uppercase tracking-wide text-gray-400">Route</p><p class="font-semibold text-gray-900">{{ $peptide->route }}</p></div>@endif
                @if($hl)<div><p class="text-xs uppercase tracking-wide text-gray-400">Half-life</p><p class="font-semibold text-gray-900">{{ $hl }}</p></div>@endif
            </div>
        </section>
    @endif

    {{-- Calculator (seeded for this peptide) --}}
    <section class="py-10 bg-cream-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('calculators.widgets.reconstitution', ['seed' => $seed])
            <p class="text-xs text-gray-400 mt-3 text-center">Pre-filled with a common starting point for {{ $name }} — adjust the vial size, water and dose to match your protocol.</p>
        </div>
    </section>

    {{-- Intro + how-to --}}
    <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-gray-700 leading-relaxed mb-8">
                This calculator handles the reconstitution math for {{ $name }} so every dose is accurate and repeatable. Enter how much {{ $name }} is in your vial, how much bacteriostatic water you are adding, and your target dose — it returns the concentration and the exact number of units to draw on a U-100 insulin syringe.{{ $td ? " It comes pre-filled with a commonly referenced $name dose of $td, which you can change to whatever you are studying." : '' }}
            </p>

            <h2 class="text-2xl font-bold text-gray-900 mb-6">How to calculate your {{ $name }} dose</h2>
            <div class="space-y-4">
                @foreach([
                    ['Enter the vial amount', "Type the milligrams of $name in your vial (commonly printed on the label)."],
                    ['Add bacteriostatic water', "Enter the water volume you will add. More water makes small doses easier to measure precisely."],
                    ['Set your target dose', "Enter your $name dose in mcg or mg. The calculator converts it to a single concentration."],
                    ['Draw the units', "Read off the exact units to draw on a U-100 insulin syringe, plus the volume in mL."],
                ] as $i => $step)
                    <div class="bg-surface-50 rounded-xl p-5">
                        <h3 class="font-semibold text-gray-900 mb-1.5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full text-white text-sm flex items-center justify-center shrink-0" style="background-color: {{ $accent }};">{{ $i + 1 }}</span>
                            {{ $step[0] }}
                        </h3>
                        <p class="text-gray-600 ml-8">{{ $step[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="py-12 bg-surface-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $name }} dosage — frequently asked questions</h2>
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

    {{-- Peptide guide + Biolinx bridge --}}
    <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-5 items-stretch">
            <a href="{{ route('peptides.show', $peptide->slug) }}" class="block rounded-2xl border border-gray-200 p-6 hover:border-primary-300 hover:shadow-sm transition-all">
                <p class="text-xs font-semibold uppercase tracking-wide text-primary-600 mb-1">Research guide</p>
                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $name }} — full research profile</h3>
                <p class="text-sm text-gray-600">Mechanism, study summaries, benefits and the honest caveats.</p>
                <span class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-primary-600">Read the guide <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
            </a>
            <div class="flex">
                <x-buy-cta :peptide="$peptide" context="dosage-calculator" variant="card" class="w-full" />
            </div>
        </div>
    </section>

    {{-- Email capture --}}
    @include('calculators.partials._email-capture', ['source' => 'calculator:'.$peptide->slug.'-dosage'])

    {{-- Disclaimer --}}
    <section class="pb-14 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 flex gap-3">
                <svg aria-hidden="true" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <p class="text-amber-700 text-sm">This calculator is for educational and research purposes only. It performs arithmetic — it is not medical advice, a dosing recommendation, or a recommendation to use {{ $name }}. Always consult a qualified healthcare professional and verify all calculations independently.</p>
            </div>
        </div>
    </section>
</x-public-layout>
