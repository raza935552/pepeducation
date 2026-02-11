<article class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300">
    <a href="{{ route('blog.show', $post->slug) }}" class="block">
        @if($post->featured_image)
            <div class="aspect-[16/9] overflow-hidden">
                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
            </div>
        @else
            <div class="aspect-[16/9] bg-gradient-to-br from-brown-100 to-cream-100 flex items-center justify-center">
                <svg class="w-12 h-12 text-brown-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                </svg>
            </div>
        @endif
    </a>

    <div class="p-5">
        {{-- Categories --}}
        @if($post->categories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($post->categories->take(2) as $category)
                    <a href="{{ route('blog.category', $category) }}"
                       class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium hover:opacity-80 transition-opacity"
                       style="background-color: {{ $category->color ?? '#e5e7eb' }}15; color: {{ $category->color ?? '#6b7280' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Title --}}
        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-brown-700 transition-colors">
            <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
        </h3>

        {{-- Excerpt --}}
        @if($post->excerpt)
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $post->excerpt }}</p>
        @endif

        {{-- Meta --}}
        <div class="flex items-center justify-between text-xs text-gray-400">
            <div class="flex items-center gap-3">
                <span>{{ $post->author?->name ?? 'PepProfesor' }}</span>
                <span>&middot;</span>
                <time datetime="{{ $post->published_at?->toIso8601String() }}">
                    {{ $post->published_at?->format('M j, Y') }}
                </time>
            </div>
            @if($post->reading_time)
                <span>{{ $post->reading_time }} min read</span>
            @endif
        </div>
    </div>
</article>
