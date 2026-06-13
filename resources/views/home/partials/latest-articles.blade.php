{{-- Home: Latest research articles — freshness + internal links to the blog --}}
@if(isset($recentPosts) && $recentPosts->isNotEmpty())
<section class="py-14 lg:py-18 bg-surface-50 border-t border-surface-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-heading">Latest from the Research Blog</h2>
                <p class="text-body/70 mt-1">Deep-dives, protocols and the latest peptide research, explained.</p>
            </div>
            <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-primary-600 hover:underline inline-flex items-center gap-1 shrink-0">All articles
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($recentPosts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" class="group bg-white rounded-2xl border border-surface-200 overflow-hidden flex flex-col hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    @if($post->featured_image)
                        <div class="aspect-[16/9] bg-surface-100 overflow-hidden">
                            <img src="{{ \Illuminate\Support\Str::startsWith($post->featured_image, ['http://','https://']) ? $post->featured_image : url($post->featured_image) }}" alt="{{ $post->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    @endif
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-bold text-gray-900 group-hover:text-primary-600 transition-colors leading-snug mb-2">{{ $post->title }}</h3>
                        @if($post->excerpt)<p class="text-sm text-body/70 line-clamp-2 flex-1">{{ $post->excerpt }}</p>@endif
                        <span class="mt-3 text-xs text-gray-400">
                            @if($post->published_at){{ $post->published_at->format('M j, Y') }}@endif
                            @if($post->reading_time) · {{ $post->reading_time }} min read @endif
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
