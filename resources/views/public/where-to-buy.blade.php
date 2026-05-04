@php
    $brand = \App\Services\BioLinxService::name();
    $brandHome = \App\Services\BioLinxService::homeUrl('where-to-buy-page');
    $defaultMap = config('biolinx.product_map', []);
    $available = $peptides->filter(fn ($p) => \App\Services\BioLinxService::hasProductForPeptide($p));
    $catalog = $peptides->reject(fn ($p) => \App\Services\BioLinxService::hasProductForPeptide($p));
@endphp

<x-public-layout
    title="Where to Buy Research Peptides"
    description="Where to find research-grade peptides at {{ $brand }}, including direct product pages for the most popular peptides."
    :canonical="route('where-to-buy')"
>
    <section class="bg-dark-900 text-white py-12 md:py-16">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-5xl font-bold mb-4">Where to Buy Research Peptides</h1>
            <p class="text-base md:text-lg text-surface-300 max-w-2xl mb-6">
                {{ $brand }} is the partner store we link to from across this site. Browse the current catalog or jump straight to a specific peptide product page below.
            </p>
            <a href="{{ $brandHome }}"
               target="_blank"
               rel="nofollow sponsored noopener"
               data-buy-cta="where-to-buy-hero"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 hover:bg-primary-600 text-white font-semibold transition">
                Visit {{ $brand }}
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </section>

    <section class="py-10 md:py-14 bg-white">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Direct Product Pages</h2>
            <p class="text-sm text-gray-500 mb-6">{{ $available->count() }} peptides have a direct product page on {{ $brand }}. Click through to view that specific product.</p>

            @if($available->isEmpty())
                <p class="text-gray-500">No direct product mappings configured yet.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($available as $peptide)
                        <a href="{{ \App\Services\BioLinxService::urlForPeptide($peptide, 'where-to-buy-grid') }}"
                           target="_blank"
                           rel="nofollow sponsored noopener"
                           data-buy-cta="where-to-buy-grid"
                           class="group flex items-center justify-between p-4 rounded-xl border border-surface-200 hover:border-primary-300 hover:shadow-md transition">
                            <div>
                                <p class="font-semibold text-gray-900 group-hover:text-primary-600">{{ $peptide->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">View product</p>
                            </div>
                            <svg aria-hidden="true" class="w-4 h-4 text-gray-400 group-hover:text-primary-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if($catalog->isNotEmpty())
    <section class="py-10 md:py-14 bg-surface-50">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Other Peptides on This Site</h2>
            <p class="text-sm text-gray-500 mb-6">{{ $catalog->count() }} peptides do not have a direct product mapping but may still be available. Visit {{ $brand }} to browse the full catalog.</p>

            <div class="rounded-2xl bg-white border border-surface-200 p-6">
                <div class="flex flex-wrap gap-2">
                    @foreach($catalog as $peptide)
                        <a href="{{ route('peptides.show', $peptide->slug) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-surface-50 border border-surface-200 text-gray-700 hover:border-primary-300 hover:text-primary-600 text-sm transition">
                            {{ $peptide->name }}
                        </a>
                    @endforeach
                </div>
                <div class="mt-6 pt-6 border-t border-surface-200">
                    <a href="{{ $brandHome }}"
                       target="_blank"
                       rel="nofollow sponsored noopener"
                       data-buy-cta="where-to-buy-catalog"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700">
                        Browse the full {{ $brand }} catalog
                        <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif
</x-public-layout>
