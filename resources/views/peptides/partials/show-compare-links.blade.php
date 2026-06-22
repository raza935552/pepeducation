{{-- Head-to-head compare links — surfaces the indexable /peptides/compare/A/vs/B
     pages so they get crawled + internally linked (captures "X vs Y" searches). --}}
@if(isset($relatedPeptides) && $relatedPeptides->isNotEmpty())
<div class="card">
    <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center gap-2">
        <svg aria-hidden="true" class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
        </svg>
        Compare {{ $peptide->name }}
    </h3>
    <div class="flex flex-wrap gap-2">
        @foreach($relatedPeptides->take(4) as $other)
            <a href="{{ route('peptides.compare.pair', ['slugA' => $peptide->slug, 'slugB' => $other->slug]) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-surface-100 text-gray-700 hover:bg-primary-50 hover:text-primary-700 border border-surface-200 transition-colors">
                vs {{ $other->name }}
            </a>
        @endforeach
    </div>
</div>
@endif
