{{-- Home: Most researched peptides — deep links to the top guides --}}
@php
    $popularSlugs = ['semaglutide', 'tirzepatide', 'retatrutide', 'bpc-157', 'tb-500', 'ipamorelin', 'cjc-1295-dac', 'ghk-cu', 'mots-c', 'pt-141', 'mk-677', 'nad-plus'];
    $popularPeptides = \App\Models\Peptide::published()->whereIn('slug', $popularSlugs)
        ->get(['name', 'slug', 'abbreviation'])
        ->sortBy(fn ($p) => array_search($p->slug, $popularSlugs))
        ->values();
@endphp

@if($popularPeptides->isNotEmpty())
<section class="py-14 lg:py-18 bg-white border-t border-surface-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-heading">Most Researched Peptides</h2>
                <p class="text-body/70 mt-1">The compounds people search for most — with research-backed guides.</p>
            </div>
            <a href="{{ route('peptides.index') }}" class="text-sm font-semibold text-primary-600 hover:underline inline-flex items-center gap-1 shrink-0">Browse all {{ $stats['peptides'] ?? '68' }}+ peptides
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($popularPeptides as $pep)
                <a href="{{ route('peptides.show', $pep->slug) }}" class="group rounded-xl border border-surface-200 bg-surface-50 p-4 text-center hover:border-primary-300 hover:shadow-sm transition-all">
                    <span class="block font-semibold text-gray-900 group-hover:text-primary-600 transition-colors text-sm leading-tight">{{ $pep->name }}</span>
                    @if($pep->abbreviation)<span class="block text-xs text-gray-400 mt-0.5">{{ $pep->abbreviation }}</span>@endif
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
