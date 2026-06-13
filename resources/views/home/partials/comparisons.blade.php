{{-- Home: Popular comparisons — deep links to each "X vs Y" page --}}
@php
    $pairs = collect(config('peptide_comparisons', []))->keys()->map(fn ($k) => explode('__', $k));
    $names = \App\Models\Peptide::whereIn('slug', $pairs->flatten()->unique())->pluck('name', 'slug');
    $comparisons = $pairs->map(fn ($p) => isset($names[$p[0]], $names[$p[1]])
        ? ['url' => route('peptides.compare.pair', ['slugA' => $p[0], 'slugB' => $p[1]]), 'label' => $names[$p[0]].' vs '.$names[$p[1]]]
        : null)->filter()->values();
@endphp

@if($comparisons->isNotEmpty())
<section class="py-14 lg:py-18 bg-surface-50 border-t border-surface-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-heading">Popular Peptide Comparisons</h2>
                <p class="text-body/70 mt-1">Side-by-side, with a verdict on which to choose for your research.</p>
            </div>
            <a href="{{ route('peptides.compare') }}" class="text-sm font-semibold text-primary-600 hover:underline inline-flex items-center gap-1 shrink-0">Compare any two
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>
        <div class="flex flex-wrap gap-2.5">
            @foreach($comparisons as $c)
                <a href="{{ $c['url'] }}" class="inline-flex items-center gap-2 rounded-full border border-surface-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:border-primary-300 hover:text-primary-600 transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    {{ $c['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
