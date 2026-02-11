<x-public-layout
    :title="$post->meta_title ?? $post->title"
    :description="$post->meta_description ?? $post->excerpt"
    :image="$post->featured_image ? url($post->featured_image) : null"
    :canonical="route('blog.show', $post->slug)"
>
    @push('head')
        <meta property="og:type" content="article">
        <meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
        <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
        @foreach($post->categories as $cat)
            <meta property="article:section" content="{{ $cat->name }}">
        @endforeach
        @foreach($post->tags as $tag)
            <meta property="article:tag" content="{{ $tag->name }}">
        @endforeach
        @include('blog.partials.schema-article', ['post' => $post])
    @endpush

    {{-- Article Hero --}}
    <section class="bg-gradient-to-br from-brown-800 via-brown-900 to-brown-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="max-w-4xl mx-auto">
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-sm text-cream-400 mb-6">
                    <a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Blog</a>
                    @if($post->categories->isNotEmpty())
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        <a href="{{ route('blog.category', $post->categories->first()) }}" class="hover:text-white transition-colors">
                            {{ $post->categories->first()->name }}
                        </a>
                    @endif
                </nav>

                {{-- Categories --}}
                @if($post->categories->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($post->categories as $category)
                            <a href="{{ route('blog.category', $category) }}"
                               class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/10 text-cream-200 hover:bg-white/20 transition-colors">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Title --}}
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">
                    {{ $post->title }}
                </h1>

                {{-- Meta --}}
                <div class="flex flex-wrap items-center gap-4 text-sm text-cream-400">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gold-500/20 flex items-center justify-center">
                            <span class="text-xs font-bold text-gold-400">{{ strtoupper(substr($post->author?->name ?? 'P', 0, 1)) }}</span>
                        </div>
                        <span>{{ $post->author?->name ?? 'PepProfesor' }}</span>
                    </div>
                    <span>&middot;</span>
                    <time datetime="{{ $post->published_at?->toIso8601String() }}">
                        {{ $post->published_at?->format('F j, Y') }}
                    </time>
                    @if($post->reading_time)
                        <span>&middot;</span>
                        <span>{{ $post->reading_time }} min read</span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Image --}}
    @if($post->featured_image)
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 -mt-8 relative z-10 mb-8">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                 class="w-full rounded-2xl shadow-lg object-cover max-h-[500px]">
        </div>
    @endif

    {{-- Content + Sidebar --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Article Content --}}
            <article class="lg:col-span-3">
                {{-- GrapeJS Rendered Content --}}
                <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-headings:font-bold prose-p:text-gray-600 prose-a:text-brown-600 prose-a:underline hover:prose-a:text-brown-800 prose-img:rounded-xl prose-img:shadow-sm">
                    {!! $post->sanitizedHtml() !!}
                </div>

                {{-- Related Peptides --}}
                @if($post->peptides->isNotEmpty())
                    <section class="mt-12 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Related Peptides</h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($post->peptides as $peptide)
                                <a href="{{ route('peptides.show', $peptide) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cream-50 border border-cream-200 text-brown-700 hover:bg-cream-100 transition-colors">
                                    <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.482 48.482 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                                    </svg>
                                    {{ $peptide->name }}
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Tags --}}
                @if($post->tags->isNotEmpty())
                    <section class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-wrap items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            @foreach($post->tags as $tag)
                                <a href="{{ route('blog.tag', $tag) }}"
                                   class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-cream-100 text-brown-700 hover:bg-cream-200 transition-colors">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Share --}}
                <section class="mt-8 pt-6 border-t border-gray-200">
                    @include('blog.partials.share-buttons', ['post' => $post])
                </section>

                {{-- Related Posts --}}
                @include('blog.partials.related-posts', ['relatedPosts' => $relatedPosts])
            </article>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                @include('blog.partials.sidebar')
            </div>
        </div>
    </section>
</x-public-layout>
