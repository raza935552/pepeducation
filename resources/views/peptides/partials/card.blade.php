<a href="{{ route('peptides.show', $peptide) }}"
   class="group relative bg-white dark:bg-brown-800 rounded-2xl border border-cream-200 dark:border-brown-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:shadow-gold-500/10 hover:-translate-y-1 hover:border-gold-300 dark:hover:border-gold-700 flex flex-col">

    <!-- Top Accent Bar -->
    <div class="h-1 w-full bg-gradient-to-r from-gold-400 via-gold-500 to-caramel-500 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>

    <div class="p-5 flex flex-col flex-1">
        <!-- Header -->
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex-1 min-w-0">
                @if($peptide->abbreviation)
                    <span class="inline-block px-2 py-0.5 rounded text-xs font-mono bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 mb-2">
                        {{ $peptide->abbreviation }}
                    </span>
                @endif
                <h3 class="text-lg font-bold text-gray-900 dark:text-cream-100 group-hover:text-gold-600 dark:group-hover:text-gold-400 transition-colors truncate">
                    {{ $peptide->name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-cream-500">{{ $peptide->type }}</p>
            </div>

            <!-- Research Badge -->
            @php $badge = $peptide->research_status_badge; @endphp
            <div class="shrink-0 flex flex-col items-center gap-1">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center
                    {{ $badge['color'] === 'blue' ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                    {{ $badge['color'] === 'green' ? 'bg-emerald-100 dark:bg-emerald-900/30' : '' }}
                    {{ $badge['color'] === 'yellow' ? 'bg-gold-100 dark:bg-gold-900/30' : '' }}
                    {{ $badge['color'] === 'gray' ? 'bg-cream-200 dark:bg-brown-700' : '' }}">
                    <svg class="w-5 h-5 {{ $badge['color'] === 'blue' ? 'text-blue-600 dark:text-blue-400' : '' }}{{ $badge['color'] === 'green' ? 'text-emerald-600 dark:text-emerald-400' : '' }}{{ $badge['color'] === 'yellow' ? 'text-gold-600 dark:text-gold-400' : '' }}{{ $badge['color'] === 'gray' ? 'text-gray-500 dark:text-cream-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-medium uppercase tracking-wide text-gray-500 dark:text-cream-500">
                    {{ $badge['label'] }}
                </span>
            </div>
        </div>

        <!-- Overview -->
        <p class="text-sm text-gray-600 dark:text-cream-400 line-clamp-2 mb-4 flex-1">
            {{ Str::limit($peptide->overview, 120) }}
        </p>

        <!-- Categories -->
        <div class="flex flex-wrap gap-1.5 mb-4">
            @foreach($peptide->categories->take(3) as $category)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium transition-colors"
                      style="background-color: {{ $category->color }}15; color: {{ $category->color }}">
                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $category->color }}"></span>
                    {{ $category->name }}
                </span>
            @endforeach
            @if($peptide->categories->count() > 3)
                <span class="px-2.5 py-1 text-xs text-cream-500 dark:text-cream-600">+{{ $peptide->categories->count() - 3 }}</span>
            @endif
        </div>

        <!-- Quick Info -->
        <div class="pt-4 border-t border-cream-100 dark:border-brown-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-cream-500">
                    @if($peptide->typical_dose)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            {{ $peptide->typical_dose }}
                        </span>
                    @endif
                    @if($peptide->route)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            {{ $peptide->route }}
                        </span>
                    @endif
                </div>

                <!-- Arrow -->
                <div class="w-8 h-8 rounded-full bg-cream-100 dark:bg-brown-700 flex items-center justify-center group-hover:bg-gold-500 transition-colors">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</a>
