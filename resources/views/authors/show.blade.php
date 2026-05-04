<x-public-layout
    :title="$user->name . ' - Author'"
    :description="$user->bio ? \Illuminate\Support\Str::limit(strip_tags($user->bio), 155) : 'Articles and research by ' . $user->name . ' on Professor Peptides.'"
    :canonical="route('author.show', $user->slug)"
>
    @push('head')
    @php
        $authorSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type'    => 'Person',
            'name'     => $user->name,
            'url'      => route('author.show', $user->slug),
            'description' => $user->bio,
            'jobTitle' => $user->credentials,
            'knowsAbout' => $user->expertiseList() ?: null,
            'sameAs' => array_values(array_filter([$user->twitter_url, $user->linkedin_url])),
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);
        if (isset($authorSchema['sameAs']) && empty($authorSchema['sameAs'])) {
            unset($authorSchema['sameAs']);
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode($authorSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    @endpush

    {{-- Hero --}}
    <section class="bg-gradient-to-br from-dark-900 via-dark-800 to-primary-900/30 text-white py-16">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-sm text-surface-400 mb-6">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Blog</a>
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-white">{{ $user->name }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row gap-6 sm:gap-8 sm:items-start">
                {{-- Avatar --}}
                <div class="shrink-0">
                    <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-gradient-to-br from-primary-500/30 to-primary-700/30 border-2 border-primary-400/40 flex items-center justify-center overflow-hidden">
                        @if($user->avatar)
                            <img src="{{ \Illuminate\Support\Str::startsWith($user->avatar, ['http://','https://']) ? $user->avatar : asset($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" loading="lazy" decoding="async">
                        @else
                            <span class="text-4xl sm:text-5xl font-bold text-primary-300">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Bio block --}}
                <div class="flex-1">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $user->name }}</h1>
                    @if($user->credentials)
                        <p class="text-base sm:text-lg text-primary-300 font-medium mb-4">{{ $user->credentials }}</p>
                    @endif

                    @if($user->bio)
                        <p class="text-surface-300 leading-relaxed mb-4">{{ $user->bio }}</p>
                    @endif

                    @if($user->expertiseList())
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($user->expertiseList() as $area)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/10 text-surface-200 border border-white/10">
                                    {{ $area }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if($user->twitter_url || $user->linkedin_url)
                        <div class="flex gap-3 mt-4">
                            @if($user->twitter_url)
                                <a href="{{ $user->twitter_url }}" target="_blank" rel="noopener" class="text-surface-400 hover:text-primary-400 transition-colors text-sm">Twitter</a>
                            @endif
                            @if($user->linkedin_url)
                                <a href="{{ $user->linkedin_url }}" target="_blank" rel="noopener" class="text-surface-400 hover:text-primary-400 transition-colors text-sm">LinkedIn</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Posts --}}
    <section class="py-12 bg-surface-50">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Articles by {{ $user->name }}</h2>
            <p class="text-sm text-gray-500 mb-8">{{ $posts->total() }} {{ \Illuminate\Support\Str::plural('article', $posts->total()) }} published</p>

            @if($posts->isEmpty())
                <div class="text-center py-16 bg-white rounded-2xl border border-surface-200">
                    <p class="text-gray-500">No articles published yet.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($posts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="group block bg-white rounded-2xl overflow-hidden border border-surface-200 hover:border-primary-300 hover:shadow-lg transition-all">
                            @if($post->featured_image)
                                <div class="aspect-[16/9] bg-surface-100 overflow-hidden">
                                    <img src="{{ \Illuminate\Support\Str::startsWith($post->featured_image, ['http://','https://','/']) ? $post->featured_image : asset($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy" decoding="async" width="1200" height="630">
                                </div>
                            @endif
                            <div class="p-5">
                                @if($post->categories->isNotEmpty())
                                    <span class="inline-block text-[11px] font-semibold uppercase tracking-wider text-primary-600 mb-2">
                                        {{ $post->categories->first()->name }}
                                    </span>
                                @endif
                                <h3 class="text-base font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors line-clamp-2">
                                    {{ $post->title }}
                                </h3>
                                @if($post->excerpt)
                                    <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $post->excerpt }}</p>
                                @endif
                                <p class="text-xs text-gray-400">{{ $post->published_at?->format('M j, Y') }}{{ $post->reading_time ? ' &middot; ' . $post->reading_time . ' min read' : '' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>
</x-public-layout>
