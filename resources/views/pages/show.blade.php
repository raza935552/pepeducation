<x-public-layout :title="$page->meta_title ?? $page->title">
    @if($page->meta_description)
        <x-slot name="meta_description">{{ $page->meta_description }}</x-slot>
    @endif

    {{-- Hero --}}
    <section class="bg-gradient-to-b from-cream-100 to-cream-50 dark:from-brown-900 dark:to-brown-900 py-16 lg:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-cream-100 mb-6">
                {{ $page->title }}
            </h1>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-16 lg:py-24">
        <div class="{{ $page->template === 'full-width' ? 'max-w-7xl' : 'max-w-4xl' }} mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-brown-800 rounded-2xl p-8 lg:p-12 border border-cream-200 dark:border-brown-700">
                <div class="prose prose-lg dark:prose-invert max-w-none editorjs-content">
                    {!! $page->html !!}
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
