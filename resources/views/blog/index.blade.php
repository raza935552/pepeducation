<x-public-layout
    title="Blog"
    description="Expert insights on peptides, research updates, and educational content from PepProfesor."
>
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-br from-brown-800 via-brown-900 to-brown-950 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg aria-hidden="true" class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <circle cx="1" cy="1" r="0.5" fill="currentColor" class="text-gold-400"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)"/>
            </svg>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                    The Peptide <span class="text-gold-400">Blog</span>
                </h1>
                <p class="text-lg text-cream-300 mb-8">
                    Expert insights, research updates, and educational content
                </p>

                {{-- Search --}}
                <form action="{{ route('blog.index') }}" method="GET" class="max-w-xl mx-auto">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search articles..."
                               class="w-full px-6 py-4 pl-14 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 text-white placeholder-cream-400 focus:outline-none focus:ring-2 focus:ring-gold-400 focus:border-transparent text-lg">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2">
                            <svg aria-hidden="true" class="w-5 h-5 text-cream-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 px-5 py-2 bg-gold-500 hover:bg-gold-600 text-white font-medium rounded-xl transition-colors">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Featured Posts --}}
    @if($featuredPosts->isNotEmpty() && !request('search') && !request('category'))
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 -mt-12 relative z-10 mb-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($featuredPosts as $featured)
                    <article class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                        <a href="{{ route('blog.show', $featured->slug) }}" class="block">
                            @if($featured->featured_image)
                                <div class="aspect-[16/9] overflow-hidden">
                                    <img src="{{ $featured->featured_image }}" alt="{{ $featured->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                </div>
                            @else
                                <div class="aspect-[16/9] bg-gradient-to-br from-gold-100 to-cream-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                    </svg>
                                </div>
                            @endif
                        </a>
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gold-100 text-gold-800">Featured</span>
                                @if($featured->categories->isNotEmpty())
                                    <span class="text-xs text-gray-400">&middot;</span>
                                    <span class="text-xs text-gray-500">{{ $featured->categories->first()->name }}</span>
                                @endif
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-brown-700 transition-colors">
                                <a href="{{ route('blog.show', $featured->slug) }}">{{ $featured->title }}</a>
                            </h3>
                            <div class="flex items-center text-xs text-gray-400">
                                <time datetime="{{ $featured->published_at?->toIso8601String() }}">
                                    {{ $featured->published_at?->format('M j, Y') }}
                                </time>
                                @if($featured->reading_time)
                                    <span class="mx-2">&middot;</span>
                                    <span>{{ $featured->reading_time }} min read</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Category Filter Pills --}}
    @if($categories->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mb-8">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('blog.index') }}"
                   class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !request('category') ? 'bg-brown-800 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                    All
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('blog.index', ['category' => $cat->slug]) }}"
                       class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('category') === $cat->slug ? 'bg-brown-800 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                        <span class="w-2 h-2 rounded-full mr-2" style="background-color: {{ $cat->color }}"></span>
                        {{ $cat->name }}
                        <span class="ml-1.5 text-xs opacity-60">{{ $cat->posts_count }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Posts Grid --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-16">
        @if(request('search'))
            <p class="text-gray-500 mb-6">
                {{ $posts->total() }} result{{ $posts->total() !== 1 ? 's' : '' }} for "<strong>{{ request('search') }}</strong>"
                <a href="{{ route('blog.index') }}" class="text-brown-600 hover:underline ml-2">Clear</a>
            </p>
        @endif

        @if($posts->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    @include('blog.partials.card', ['post' => $post])
                @endforeach
            </div>

            @if($posts->hasPages())
                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No articles found</h3>
                <p class="text-gray-500">Check back soon for new content.</p>
            </div>
        @endif
    </section>
</x-public-layout>
