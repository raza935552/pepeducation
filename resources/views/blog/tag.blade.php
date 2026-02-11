<x-public-layout
    title="#{{ $tag->name }} - Blog"
    description="Articles tagged with {{ $tag->name }}"
>
    {{-- Header --}}
    <section class="bg-gradient-to-br from-brown-800 via-brown-900 to-brown-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="max-w-3xl">
                <nav class="flex items-center gap-2 text-sm text-cream-400 mb-4">
                    <a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Blog</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-white">Tag</span>
                </nav>
                <h1 class="text-3xl md:text-4xl font-bold text-white">
                    #{{ $tag->name }}
                </h1>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Posts --}}
            <div class="lg:col-span-3">
                @if($posts->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No articles with this tag yet</h3>
                        <p class="text-gray-500">Check back soon for new content.</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                @include('blog.partials.sidebar')
            </div>
        </div>
    </section>
</x-public-layout>
