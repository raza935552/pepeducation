<div class="sticky top-24 space-y-6">
    <!-- Search -->
    <div class="card">
        <h3 class="font-semibold text-gray-900 dark:text-cream-100 mb-3">Search</h3>
        <form action="{{ route('peptides.index') }}" method="GET">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('research'))
                <input type="hidden" name="research" value="{{ request('research') }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search peptides..."
                       class="input w-full pr-10">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gold-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Categories -->
    <div class="card" x-data="{ showAll: false }">
        <h3 class="font-semibold text-gray-900 dark:text-cream-100 mb-3">Categories</h3>
        <div class="space-y-1">
            <a href="{{ route('peptides.index', array_filter(['search' => request('search'), 'research' => request('research')])) }}"
               class="block px-3 py-2 rounded-lg text-sm transition-colors {{ !request('category') ? 'bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400' : 'text-gray-600 dark:text-cream-400 hover:bg-cream-100 dark:hover:bg-brown-700' }}">
                All Categories
            </a>
            @foreach($categories->take(8) as $cat)
                <a href="{{ route('peptides.index', array_filter(['category' => $cat->slug, 'search' => request('search'), 'research' => request('research')])) }}"
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors {{ request('category') === $cat->slug ? 'bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400' : 'text-gray-600 dark:text-cream-400 hover:bg-cream-100 dark:hover:bg-brown-700' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $cat->color }}"></span>
                        {{ $cat->name }}
                    </span>
                    <span class="text-xs text-cream-500">{{ $cat->peptides_count }}</span>
                </a>
            @endforeach

            @if($categories->count() > 8)
                <template x-if="showAll">
                    <div class="space-y-1">
                        @foreach($categories->skip(8) as $cat)
                            <a href="{{ route('peptides.index', array_filter(['category' => $cat->slug, 'search' => request('search'), 'research' => request('research')])) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors {{ request('category') === $cat->slug ? 'bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400' : 'text-gray-600 dark:text-cream-400 hover:bg-cream-100 dark:hover:bg-brown-700' }}">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $cat->color }}"></span>
                                    {{ $cat->name }}
                                </span>
                                <span class="text-xs text-cream-500">{{ $cat->peptides_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </template>
                <button @click="showAll = !showAll" class="w-full text-left px-3 py-2 text-sm text-gold-600 dark:text-gold-400 hover:underline">
                    <span x-text="showAll ? 'Show less' : 'Show {{ $categories->count() - 8 }} more'"></span>
                </button>
            @endif
        </div>
    </div>

    <!-- Research Status -->
    <div class="card">
        <h3 class="font-semibold text-gray-900 dark:text-cream-100 mb-3">Research Level</h3>
        <div class="space-y-1">
            @foreach(['extensive' => 'Extensively Studied', 'well' => 'Well Researched', 'emerging' => 'Emerging', 'limited' => 'Limited'] as $val => $label)
                <a href="{{ route('peptides.index', array_filter(['research' => request('research') === $val ? null : $val, 'category' => request('category'), 'search' => request('search')])) }}"
                   class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request('research') === $val ? 'bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400' : 'text-gray-600 dark:text-cream-400 hover:bg-cream-100 dark:hover:bg-brown-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    @if(request()->hasAny(['search', 'category', 'research']))
        <a href="{{ route('peptides.index') }}" class="btn btn-ghost w-full">Clear All Filters</a>
    @endif
</div>
