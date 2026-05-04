@php
    $popularPeptides = collect();
    $recentPosts     = collect();
    $lastViewedPeptide = null;
    try {
        $popularPeptides = \App\Models\Peptide::published()
            ->whereIn('slug', ['semaglutide', 'tirzepatide', 'bpc-157', 'retatrutide', 'tb-500', 'mk-677', 'ghk-cu', 'ipamorelin'])
            ->select('id', 'name', 'slug')
            ->get();
        $recentPosts = \App\Models\BlogPost::published()
            ->latest('published_at')
            ->limit(4)
            ->get(['title', 'slug', 'featured_image', 'published_at', 'reading_time']);

        // Personalization: show last-viewed peptide if cookie is set
        $lastSlug = request()->cookie('pp_last_peptide');
        if ($lastSlug) {
            $lastViewedPeptide = \App\Models\Peptide::published()->where('slug', $lastSlug)->first(['id', 'name', 'slug']);
        }
    } catch (\Throwable $e) {
        // Continue with empty collections if DB unavailable
    }
@endphp

<x-public-layout title="Page Not Found" description="The page you were looking for doesn't exist or has been moved.">
    <section class="bg-dark-900 text-white py-16 md:py-24">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-7xl md:text-9xl font-extrabold text-primary-400/80 leading-none mb-2">404</p>
            <h1 class="text-2xl md:text-4xl font-bold mb-4">This page took a detour</h1>
            <p class="text-base md:text-lg text-surface-300 max-w-xl mx-auto mb-8">
                The page you were looking for doesn't exist or has been moved. Try one of the popular pages below, or use the search.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 hover:bg-primary-600 text-white font-semibold transition">
                    Go Home
                </a>
                <a href="{{ route('peptides.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold transition">
                    Browse 68+ Peptides
                </a>
                <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold transition">
                    Read the Blog
                </a>
            </div>

            <form action="{{ route('peptides.index') }}" method="GET" class="mt-8 max-w-md mx-auto">
                <div class="relative">
                    <input type="search" name="search" placeholder="Search peptides..."
                        class="w-full px-5 py-3 pl-12 rounded-full bg-white/10 border border-white/20 text-white placeholder-surface-400 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent">
                    <svg aria-hidden="true" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>

            @if($lastViewedPeptide)
                <p class="mt-6 text-sm text-surface-400">
                    Or pick up where you left off:
                    <a href="{{ route('peptides.show', $lastViewedPeptide->slug) }}" class="text-primary-300 hover:text-primary-200 underline ml-1">
                        Back to {{ $lastViewedPeptide->name }}
                    </a>
                </p>
            @endif
        </div>
    </section>

    @if($popularPeptides->isNotEmpty())
    <section class="py-12 bg-surface-50">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Popular Peptides</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach($popularPeptides as $peptide)
                    <a href="{{ route('peptides.show', $peptide->slug) }}" class="block p-4 bg-white rounded-xl border border-surface-200 hover:border-primary-300 hover:shadow-md transition text-center">
                        <p class="font-semibold text-gray-900 text-sm">{{ $peptide->name }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if($recentPosts->isNotEmpty())
    <section class="py-12 bg-white">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Latest Articles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($recentPosts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="group block bg-white rounded-2xl overflow-hidden border border-surface-200 hover:border-primary-300 hover:shadow-lg transition-all">
                        @if($post->featured_image)
                            <div class="aspect-[16/9] bg-surface-100 overflow-hidden">
                                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" loading="lazy" decoding="async" width="1200" height="630" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-sm font-bold text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2 mb-2">
                                {{ $post->title }}
                            </h3>
                            <p class="text-xs text-gray-400">{{ $post->published_at?->format('M j, Y') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</x-public-layout>
