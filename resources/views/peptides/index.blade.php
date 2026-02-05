<x-public-layout title="Browse Peptides">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-brown-800 via-brown-900 to-brown-950 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <circle cx="1" cy="1" r="0.5" fill="currentColor" class="text-gold-400"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)"/>
            </svg>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 lg:pt-24 pb-16 lg:pb-24">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                <!-- Left: Title & Search -->
                <div class="flex-1 max-w-2xl">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                        Peptide <span class="text-gold-400">Database</span>
                    </h1>
                    <p class="text-lg text-cream-300 mb-8">
                        Explore our comprehensive collection of research-backed peptide information
                    </p>

                    <!-- Search Bar -->
                    <form action="{{ route('peptides.index') }}" method="GET" class="relative">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search by name, type, or benefits..."
                                   class="w-full px-6 py-4 pl-14 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-cream-400 focus:outline-none focus:ring-2 focus:ring-gold-400 focus:border-transparent text-lg">
                            <div class="absolute left-5 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-cream-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 px-5 py-2 bg-gold-500 hover:bg-gold-600 text-white font-medium rounded-xl transition-colors">
                                Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right: Stats -->
                <div class="flex gap-6 lg:gap-8">
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-gold-400">{{ $peptides->total() }}</div>
                        <div class="text-sm text-cream-400 mt-1">Peptides</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-gold-400">{{ $categories->count() }}</div>
                        <div class="text-sm text-cream-400 mt-1">Categories</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-gold-400">100%</div>
                        <div class="text-sm text-cream-400 mt-1">Free Access</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Pills (2 Rows, Drag Scrollable) -->
    <section class="bg-cream-50 dark:bg-brown-900 py-4 border-b border-cream-200 dark:border-brown-700">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div x-data="{
                    isDown: false,
                    startX: 0,
                    scrollLeft: 0,
                    preventClick: false,
                    start(e) {
                        this.isDown = true;
                        this.preventClick = false;
                        this.$el.style.cursor = 'grabbing';
                        this.startX = (e.pageX || e.touches[0].pageX) - this.$el.offsetLeft;
                        this.scrollLeft = this.$el.scrollLeft;
                    },
                    end() {
                        this.isDown = false;
                        this.$el.style.cursor = 'grab';
                    },
                    move(e) {
                        if (!this.isDown) return;
                        const x = (e.pageX || e.touches[0].pageX) - this.$el.offsetLeft;
                        const walk = (x - this.startX) * 1.5;
                        if (Math.abs(walk) > 5) this.preventClick = true;
                        this.$el.scrollLeft = this.scrollLeft - walk;
                    },
                    handleClick(e) {
                        if (this.preventClick) e.preventDefault();
                    }
                 }"
                 @mousedown="start($event)"
                 @mouseleave="end()"
                 @mouseup="end()"
                 @mousemove="move($event)"
                 @touchstart.passive="start($event)"
                 @touchend="end()"
                 @touchmove="move($event)"
                 class="overflow-x-auto scrollbar-hide cursor-grab select-none">
                <div class="inline-grid grid-rows-2 grid-flow-col auto-cols-max gap-2">
                    <a href="{{ route('peptides.index', array_filter(['search' => request('search'), 'research' => request('research')])) }}"
                       @click="handleClick($event)"
                       class="px-4 py-2 rounded-full text-sm font-medium transition-all whitespace-nowrap {{ !request('category') ? 'bg-gold-500 text-white' : 'bg-white dark:bg-brown-800 text-gray-600 dark:text-cream-300 hover:bg-gold-50 dark:hover:bg-brown-700 border border-cream-200 dark:border-brown-600' }}">
                        All Peptides
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('peptides.index', array_filter(['category' => $cat->slug, 'search' => request('search'), 'research' => request('research')])) }}"
                           @click="handleClick($event)"
                           class="px-4 py-2 rounded-full text-sm font-medium transition-all inline-flex items-center gap-2 whitespace-nowrap {{ request('category') === $cat->slug ? 'bg-gold-500 text-white' : 'bg-white dark:bg-brown-800 text-gray-600 dark:text-cream-300 hover:bg-gold-50 dark:hover:bg-brown-700 border border-cream-200 dark:border-brown-600' }}">
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $cat->color }}"></span>
                            {{ $cat->name }}
                            <span class="text-xs opacity-60">({{ $cat->peptides_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-8 lg:py-12 bg-cream-50 dark:bg-brown-900 min-h-screen">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Active Filters & Sort -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div class="flex items-center gap-3 flex-wrap">
                    @if(request('search'))
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 text-sm">
                            Search: "{{ request('search') }}"
                            <a href="{{ route('peptides.index', array_filter(['category' => request('category'), 'research' => request('research')])) }}" class="hover:text-gold-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request('research'))
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gold-100 dark:bg-gold-900/30 text-gold-700 dark:text-gold-400 text-sm">
                            {{ ucfirst(request('research')) }} Research
                            <a href="{{ route('peptides.index', array_filter(['category' => request('category'), 'search' => request('search')])) }}" class="hover:text-gold-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if(request()->hasAny(['search', 'category', 'research']))
                        <a href="{{ route('peptides.index') }}" class="text-sm text-gold-600 dark:text-gold-400 hover:underline">
                            Clear all
                        </a>
                    @endif
                </div>

                <!-- Research Filter Dropdown -->
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-500 dark:text-cream-500">Research Level:</span>
                    <div class="flex gap-1">
                        @foreach(['extensive' => 'Extensive', 'well' => 'Well', 'emerging' => 'Emerging', 'limited' => 'Limited'] as $val => $label)
                            <a href="{{ route('peptides.index', array_filter(['research' => request('research') === $val ? null : $val, 'category' => request('category'), 'search' => request('search')])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ request('research') === $val ? 'bg-gold-500 text-white' : 'bg-white dark:bg-brown-800 text-gray-600 dark:text-cream-400 hover:bg-cream-100 dark:hover:bg-brown-700 border border-cream-200 dark:border-brown-600' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Results Count -->
            <div class="mb-6">
                <p class="text-gray-600 dark:text-cream-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-cream-100">{{ $peptides->firstItem() ?? 0 }}-{{ $peptides->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold text-gray-900 dark:text-cream-100">{{ $peptides->total() }}</span> peptides
                </p>
            </div>

            <!-- Peptide Grid -->
            @include('peptides.partials.grid')
        </div>
    </section>
</x-public-layout>
