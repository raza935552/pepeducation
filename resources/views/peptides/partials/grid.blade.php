@if($peptides->isEmpty())
    <div class="bg-white rounded-2xl border border-cream-200 p-12 text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-cream-100 flex items-center justify-center">
            <svg aria-hidden="true" class="w-10 h-10 text-cream-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No peptides found</h3>
        <p class="text-gray-500 mb-6 max-w-md mx-auto">
            We couldn't find any peptides matching your criteria. Try adjusting your filters or search terms.
        </p>
        <a href="{{ route('peptides.index') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-gold-500 hover:bg-gold-600 text-white font-medium rounded-xl transition-colors">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Reset Filters
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($peptides as $peptide)
            @include('peptides.partials.card', ['peptide' => $peptide])
        @endforeach
    </div>

    @if($peptides->hasPages())
        <div class="mt-10 flex justify-center">
            <nav class="flex items-center gap-2" role="navigation" aria-label="Pagination">
                {{-- Previous --}}
                @if($peptides->onFirstPage())
                    <span class="px-4 py-2 rounded-xl bg-cream-100 text-cream-400 cursor-not-allowed">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $peptides->previousPageUrl() }}"
                       class="px-4 py-2 rounded-xl bg-white border border-cream-200 text-gray-600 hover:bg-gold-50 hover:border-gold-300 transition-colors">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach($peptides->getUrlRange(max(1, $peptides->currentPage() - 2), min($peptides->lastPage(), $peptides->currentPage() + 2)) as $page => $url)
                    @if($page == $peptides->currentPage())
                        <span class="px-4 py-2 rounded-xl bg-gold-500 text-white font-medium min-w-[44px] text-center">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="px-4 py-2 rounded-xl bg-white border border-cream-200 text-gray-600 hover:bg-gold-50 hover:border-gold-300 transition-colors min-w-[44px] text-center">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($peptides->hasMorePages())
                    <a href="{{ $peptides->nextPageUrl() }}"
                       class="px-4 py-2 rounded-xl bg-white border border-cream-200 text-gray-600 hover:bg-gold-50 hover:border-gold-300 transition-colors">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <span class="px-4 py-2 rounded-xl bg-cream-100 text-cream-400 cursor-not-allowed">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif
            </nav>
        </div>
    @endif
@endif
