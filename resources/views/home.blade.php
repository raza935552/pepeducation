<x-public-layout>
    <x-slot name="title">Home</x-slot>

    @push('head')
        @include('partials.schema-website')
    @endpush

    {{-- Hero Section --}}
    @include('home.partials.hero')

    {{-- Featured CTA (Caramel) --}}
    @include('home.partials.featured-cta')

    {{-- Featured Peptides --}}
    @include('home.partials.featured')

    {{-- How It Works --}}
    @include('home.partials.how-it-works')

    {{-- Categories Grid --}}
    @include('home.partials.categories')

    {{-- Newsletter CTA --}}
    @include('home.partials.cta')
</x-public-layout>
