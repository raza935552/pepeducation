<x-public-layout>
    <x-slot name="title">Home</x-slot>

    @push('head')
        @include('partials.schema-website')
    @endpush

    {{-- Hero Section (search-first) --}}
    @include('home.partials.hero')

    {{-- Feature Cards (what you can do here) --}}
    @include('home.partials.features')

    {{-- Featured Peptides --}}
    @include('home.partials.featured')

    {{-- Go Deeper (action CTAs) --}}
    @include('home.partials.go-deeper')

    {{-- Browse by Category --}}
    @include('home.partials.categories')

    {{-- Newsletter CTA --}}
    @include('home.partials.cta')

    {{-- Quick Links --}}
    @include('home.partials.quick-links')
</x-public-layout>
