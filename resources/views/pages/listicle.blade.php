<x-public-layout :title="$page->meta_title ?? $page->title">
    @if($page->meta_description)
        <x-slot name="meta_description">{{ $page->meta_description }}</x-slot>
    @endif

    {{-- Hero Section --}}
    <section class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white py-16 lg:py-24">
        <div class="max-w-4xl mx-auto px-4 text-center">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 bg-gold-500/20 text-gold-400 px-4 py-2 rounded-full text-sm font-medium mb-6">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                2026 UPDATED RANKINGS
            </div>

            {{-- Title --}}
            <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold leading-tight mb-6">
                {{ $page->title }}
            </h1>

            {{-- Subtitle --}}
            <p class="text-xl text-gray-300 max-w-2xl mx-auto mb-8">
                We ranked every popular peptide based on research quality, user results, and value. No sponsors. No BS.
            </p>

            {{-- Stats --}}
            <div class="flex flex-wrap justify-center gap-8 text-sm">
                <div class="flex items-center gap-2">
                    <span class="text-gold-400 font-bold">47</span>
                    <span class="text-gray-400">Peptides Reviewed</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gold-400 font-bold">200+</span>
                    <span class="text-gray-400">Studies Analyzed</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gold-400 font-bold">12</span>
                    <span class="text-gray-400">Expert Consultations</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <article class="bg-white">
        <div class="max-w-4xl mx-auto px-4 py-12 lg:py-16">
            {{-- Author/Date --}}
            <div class="flex items-center gap-4 text-sm text-gray-600 mb-10 pb-6 border-b border-gray-200">
                <img src="https://ui-avatars.com/api/?name=Research+Team&background=1a1714&color=C9A227&size=48"
                     alt="Author" class="w-10 h-10 rounded-full">
                <div>
                    <div class="font-medium text-gray-900">Professor Peptides Research Team</div>
                    <div>Last updated: {{ now()->format('F j, Y') }} · 12 min read</div>
                </div>
            </div>

            {{-- Article Body --}}
            <div class="prose prose-lg max-w-none
                        prose-headings:font-bold prose-headings:text-gray-900
                        prose-p:text-gray-700 prose-p:leading-relaxed
                        prose-a:text-gold-600 prose-a:no-underline hover:prose-a:underline
                        prose-strong:text-gray-900">
                {!! $page->html !!}
            </div>
        </div>
    </article>

    {{-- Sticky CTA Bar (Mobile) --}}
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-gray-900 border-t border-gray-700 p-4 z-50">
        <a href="/quiz/product-match"
           class="block w-full bg-gradient-to-r from-gold-500 to-gold-600 text-gray-900 text-center font-bold py-4 rounded-full shadow-lg">
            Find Your Perfect Peptide →
        </a>
    </div>

    {{-- Bottom Padding for Sticky Bar --}}
    <div class="lg:hidden h-24"></div>
</x-public-layout>
