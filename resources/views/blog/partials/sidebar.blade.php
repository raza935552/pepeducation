<aside class="space-y-8">
    {{-- Categories --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Categories</h3>
        <ul class="space-y-2">
            @foreach($categories as $cat)
                <li>
                    <a href="{{ route('blog.category', $cat) }}"
                       class="flex items-center justify-between py-1.5 text-sm {{ isset($category) && $category->id === $cat->id ? 'text-brown-700 font-semibold' : 'text-gray-600 hover:text-brown-700' }} transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $cat->color ?? '#6b7280' }}"></span>
                            {{ $cat->name }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $cat->posts_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Popular Posts --}}
    @if(isset($popularPosts) && $popularPosts->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Popular Posts</h3>
            <ul class="space-y-4">
                @foreach($popularPosts as $popular)
                    <li>
                        <a href="{{ route('blog.show', $popular->slug) }}" class="group flex gap-3">
                            @if($popular->featured_image)
                                <img src="{{ $popular->featured_image }}" alt=""
                                     class="w-16 h-16 rounded-lg object-cover flex-shrink-0" loading="lazy">
                            @else
                                <div class="w-16 h-16 rounded-lg bg-cream-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-brown-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-brown-700 transition-colors">
                                    {{ $popular->title }}
                                </h4>
                                <p class="text-xs text-gray-400 mt-1">{{ number_format($popular->views_count) }} views</p>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tags Cloud --}}
    @if(isset($post) && $post->tags->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tags</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($post->tags as $postTag)
                    <a href="{{ route('blog.tag', $postTag) }}"
                       class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-cream-100 text-brown-700 hover:bg-cream-200 transition-colors">
                        #{{ $postTag->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</aside>
