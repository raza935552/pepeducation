@props([
    'peptide'  => null,
    'slug'     => null,
    'context'  => 'page',
    'variant'  => 'card',
    'label'    => null,
])

@php
    $resolvedSlug = $slug;
    if (!$resolvedSlug && $peptide) {
        $resolvedSlug = is_object($peptide) ? ($peptide->slug ?? null) : $peptide;
    }

    $peptideName = is_object($peptide) ? ($peptide->name ?? null) : null;

    if (is_object($peptide)) {
        $hasProduct = \App\Services\BioLinxService::hasProductForPeptide($peptide);
        $url        = \App\Services\BioLinxService::urlForPeptide($peptide, $context);
    } else {
        $hasProduct = \App\Services\BioLinxService::hasProductFor($resolvedSlug);
        $url        = $resolvedSlug
            ? \App\Services\BioLinxService::urlForSlug($resolvedSlug, $context)
            : \App\Services\BioLinxService::homeUrl($context);
    }

    $brand = \App\Services\BioLinxService::name();

    $defaultLabel = $hasProduct
        ? 'Buy '.($peptideName ?? 'This Peptide').' at '.$brand
        : 'Shop Research Peptides at '.$brand;
    $resolvedLabel = $label ?? $defaultLabel;

    $trackingPayload = [
        'destination' => $brand,
        'context'     => $context,
        'peptide'     => $peptideName,
        'slug'        => $resolvedSlug,
        'product'     => $hasProduct,
    ];

    $serverTrackPayload = [
        'peptide_id'  => is_object($peptide) ? ($peptide->id ?? null) : null,
        'context'     => $context,
        'destination' => $brand,
        'target_url'  => $url,
        'has_product' => $hasProduct,
    ];
    $serverTrackJson = json_encode($serverTrackPayload, JSON_UNESCAPED_SLASHES);
@endphp

@if($variant === 'inline')
    <a href="{{ $url }}"
       target="_blank"
       rel="nofollow sponsored noopener"
       data-buy-cta="{{ $context }}"
       onclick="ppTrackBuyClick(this, {{ \Illuminate\Support\Js::from($trackingPayload) }}, {{ \Illuminate\Support\Js::from($serverTrackPayload) }})"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-colors shadow-sm">
        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        {{ $resolvedLabel }}
        <svg aria-hidden="true" class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
    </a>
@elseif($variant === 'banner')
    <div class="rounded-xl border border-primary-200 bg-gradient-to-br from-primary-50 to-white p-5 sm:p-6 my-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-primary-600 mb-1">Available At</p>
                <p class="text-base sm:text-lg font-semibold text-gray-900">
                    {{ $hasProduct ? ($peptideName ?? 'This peptide').' is available at '.$brand : 'Browse research peptides at '.$brand }}
                </p>
            </div>
            <a href="{{ $url }}"
               target="_blank"
               rel="nofollow sponsored noopener"
               data-buy-cta="{{ $context }}"
               onclick="ppTrackBuyClick(this, {{ \Illuminate\Support\Js::from($trackingPayload) }}, {{ \Illuminate\Support\Js::from($serverTrackPayload) }})"
               class="shrink-0 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-colors shadow-sm whitespace-nowrap">
                {{ $hasProduct ? 'View Product' : 'Visit Store' }}
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>
@else
    {{-- Default: 'card' (sidebar) --}}
    <div class="card border-2 border-primary-200 bg-gradient-to-br from-primary-50 to-white">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-primary-600 mb-2">Available At</p>
        <h3 class="text-base font-bold text-gray-900 mb-2">
            {{ $hasProduct ? ($peptideName ?? 'This peptide').' at '.$brand : 'Browse '.$brand }}
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            {{ $hasProduct
                ? 'View this product on '.$brand.'.'
                : 'Browse the catalog on '.$brand.'.' }}
        </p>
        <a href="{{ $url }}"
           target="_blank"
           rel="nofollow sponsored noopener"
           data-buy-cta="{{ $context }}"
           onclick="ppTrackBuyClick(this, {{ \Illuminate\Support\Js::from($trackingPayload) }}, {{ \Illuminate\Support\Js::from($serverTrackPayload) }})"
           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-colors shadow-sm">
            {{ $hasProduct ? 'View Product at '.$brand : 'Visit '.$brand }}
            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
        </a>
    </div>
@endif
