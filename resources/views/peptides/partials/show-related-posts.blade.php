@if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
<section class="mt-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Articles about {{ $peptide->name }}</h2>
        <a href="{{ route('blog.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">All articles ></a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($relatedPosts as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="group block bg-white rounded-2xl overflow-hidden border border-surface-200 hover:border-primary-300 hover:shadow-lg transition-all">
                @if($post->featured_image)
                    <div class="aspect-[16/9] bg-surface-100 overflow-hidden">
                        <img src="{{ \Illuminate\Support\Str::startsWith($post->featured_image, ['http://','https://','/']) ? $post->featured_image : asset($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy" decoding="async" width="1200" height="630">
                    </div>
                @endif
                <div class="p-4">
                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2 mb-2">
                        {{ $post->title }}
                    </h3>
                    <p class="text-xs text-gray-400">{{ $post->published_at?->format('M j, Y') }}{{ $post->reading_time ? ' &middot; ' . $post->reading_time . ' min' : '' }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif
