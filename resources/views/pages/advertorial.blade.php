<x-public-layout :title="$page->meta_title ?? $page->title" :description="$page->meta_description">

    {{-- Advertorial Notice --}}
    <div class="bg-gray-100 border-b border-gray-200 py-2">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="text-xs text-gray-500 uppercase tracking-wider">Advertisement</span>
        </div>
    </div>

    {{-- News-style Masthead --}}
    <header class="bg-white border-b border-gray-200 py-4">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <div class="text-2xl font-serif font-bold text-gray-900 tracking-tight">
                HEALTH & WELLNESS DAILY
            </div>
            <div class="text-xs text-gray-500 mt-1">
                Independent Health Research & News
            </div>
        </div>
    </header>

    {{-- Article Content --}}
    <article class="bg-white">
        <div class="max-w-3xl mx-auto px-4 py-8 lg:py-12">
            {{-- Category Tag --}}
            <div class="mb-4">
                <span class="inline-block bg-red-600 text-white text-xs font-bold px-3 py-1 rounded">
                    BREAKING RESEARCH
                </span>
            </div>

            {{-- Headline --}}
            <h1 class="text-3xl lg:text-4xl xl:text-5xl font-serif font-bold text-gray-900 leading-tight mb-6">
                {{ $page->title }}
            </h1>

            {{-- Byline --}}
            <div class="flex items-center gap-4 text-sm text-gray-600 mb-8 pb-8 border-b border-gray-200">
                <img src="https://ui-avatars.com/api/?name=Dr+Sarah+Chen&background=9A7B4F&color=fff&size=48"
                     alt="Author" width="40" height="40" class="w-10 h-10 rounded-full">
                <div>
                    <div class="font-medium text-gray-900">Dr. Sarah Chen, PhD</div>
                    <div>Health Science Editor · {{ now()->format('F j, Y') }} · 8 min read</div>
                </div>
            </div>

            {{-- Article Body --}}
            <div class="prose prose-lg max-w-none
                        prose-headings:font-serif prose-headings:text-gray-900
                        prose-p:text-gray-700 prose-p:leading-relaxed
                        prose-a:text-gold-600 prose-a:no-underline hover:prose-a:underline
                        prose-blockquote:border-gold-500 prose-blockquote:bg-cream-50 prose-blockquote:py-4 prose-blockquote:px-6 prose-blockquote:rounded-r-lg
                        prose-strong:text-gray-900">
                {!! $page->sanitizedHtml() !!}
            </div>
        </div>
    </article>

    {{-- Sticky CTA Bar (Mobile) --}}
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 z-50">
        <a href="{{ route('quiz.show', 'product-match') }}"
           class="block w-full bg-gradient-to-r from-gold-500 to-caramel-500 text-white text-center font-bold py-4 rounded-full shadow-lg">
            Take The Quiz &rarr; Find Your Peptide
        </a>
    </div>

    {{-- Bottom Padding for Sticky Bar --}}
    <div class="lg:hidden h-24"></div>
</x-public-layout>
