<div class="mt-12 pt-8 border-t border-cream-200 dark:border-brown-700">
    <div class="flex items-center justify-between mb-6">
        <h2 class="section-heading-lg !mb-0">
            <span class="section-icon bg-gradient-to-br from-gold-400 to-caramel-600 shadow-gold-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                </svg>
            </span>
            Related Peptides
        </h2>
        <a href="{{ route('peptides.index') }}" class="text-sm font-medium text-gold-600 dark:text-gold-400 hover:text-gold-700 dark:hover:text-gold-300 flex items-center gap-1 group">
            View all
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($relatedPeptides as $related)
            <a href="{{ route('peptides.show', $related) }}"
               class="card group hover:border-gold-300 dark:hover:border-gold-700 hover:shadow-lg transition-all">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs text-gray-500 dark:text-cream-500">{{ $related->type }}</span>
                    @if($related->abbreviation)
                        <span class="px-1.5 py-0.5 rounded text-xs font-mono bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400">
                            {{ $related->abbreviation }}
                        </span>
                    @endif
                </div>
                <h3 class="font-bold text-gray-900 dark:text-cream-100 group-hover:text-gold-600 dark:group-hover:text-gold-400 transition-colors mb-2">
                    {{ $related->name }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-cream-500 line-clamp-2 mb-3">
                    {{ Str::limit($related->overview, 80) }}
                </p>
                <span class="text-sm font-medium text-gold-600 dark:text-gold-400 flex items-center gap-1">
                    Learn more
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
        @endforeach
    </div>
</div>
