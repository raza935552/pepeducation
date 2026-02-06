<x-public-layout :title="$page->meta_title ?? $page->title" :description="$page->meta_description" :image="$page->featured_image ? url($page->featured_image) : null">

    {{-- Hero --}}
    <section class="bg-gradient-to-b from-cream-100 to-cream-50 py-16 lg:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                {{ $page->title }}
            </h1>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-16 lg:py-24">
        <div class="{{ $page->template === 'full-width' ? 'max-w-7xl' : 'max-w-4xl' }} mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl p-8 lg:p-12 border border-cream-200">
                <div class="prose prose-lg max-w-none editorjs-content">
                    {!! $page->sanitizedHtml() !!}
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
