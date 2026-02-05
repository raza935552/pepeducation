<a href="{{ route('peptides.show', $peptide) }}"
   class="group block bg-cream-50 dark:bg-brown-900 rounded-2xl border border-cream-200 dark:border-brown-700 p-6 hover:shadow-xl hover:shadow-cream-300/50 dark:hover:shadow-brown-900/50 hover:border-cream-300 dark:hover:border-brown-600 transition-all duration-300 hover:-translate-y-1">
    {{-- Header with badge and bookmark --}}
    <div class="flex items-start justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            {{-- Abbreviation Badge --}}
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-gold-500 to-caramel-500 flex items-center justify-center shadow-lg shadow-gold-500/20">
                <span class="text-sm font-bold text-white">
                    {{ strtoupper(substr($peptide->abbreviation ?? $peptide->name, 0, 3)) }}
                </span>
            </div>
            <div class="min-w-0">
                <h3 class="text-lg font-bold text-gray-900 dark:text-cream-100 group-hover:text-gold-500 dark:group-hover:text-gold-400 transition-colors truncate">
                    {{ $peptide->name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-cream-400 truncate">
                    {{ $peptide->type ?? Str::limit($peptide->overview, 30) }}
                </p>
            </div>
        </div>
        {{-- Bookmark icon --}}
        <button type="button" class="shrink-0 p-2 rounded-lg text-gray-400 hover:text-gold-500 hover:bg-gold-50 dark:hover:bg-gold-900/30 transition-colors" onclick="event.preventDefault();">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
            </svg>
        </button>
    </div>

    {{-- Categories --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach($peptide->categories->take(3) as $category)
            <span class="px-3 py-1 rounded-full text-xs font-medium bg-cream-200 dark:bg-brown-700 text-gray-700 dark:text-cream-300">
                {{ $category->name }}
            </span>
        @endforeach
    </div>

    {{-- Common Research Uses --}}
    @if($peptide->benefits && count($peptide->benefits) > 0)
        <div class="mb-4">
            <p class="text-xs font-semibold text-gray-400 dark:text-cream-500 uppercase tracking-wider mb-2">
                Common Research Uses
            </p>
            <p class="text-sm text-gray-600 dark:text-cream-300 line-clamp-2">
                {{ implode(', ', array_slice($peptide->benefits, 0, 4)) }}
            </p>
        </div>
    @endif

    {{-- Footer --}}
    <div class="flex items-center justify-between pt-4 border-t border-cream-200 dark:border-brown-700">
        {{-- Research Status Badge --}}
        @php
            $researchLevel = $peptide->research_level ?? 'moderate';
            $badgeClasses = match($researchLevel) {
                'extensive' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                'well-researched' => 'bg-gold-100 text-gold-700 dark:bg-gold-900/30 dark:text-gold-400',
                default => 'bg-cream-200 text-gray-600 dark:bg-brown-700 dark:text-cream-400'
            };
            $badgeText = match($researchLevel) {
                'extensive' => 'Extensively Studied',
                'well-researched' => 'Well Researched',
                default => 'Limited Research'
            };
        @endphp
        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badgeClasses }}">
            {{ $badgeText }}
        </span>

        {{-- Learn More --}}
        <span class="inline-flex items-center gap-1 text-sm font-medium text-gold-500 dark:text-gold-400 group-hover:gap-2 transition-all">
            Learn More
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </span>
    </div>
</a>
