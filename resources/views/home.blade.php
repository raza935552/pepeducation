<x-public-layout
    :title="\App\Models\Setting::getValue('seo_pages', 'home_title', 'Peptide Guide: Benefits, Dosing Protocols & Side Effects')"
    :description="\App\Models\Setting::getValue('seo_pages', 'home_description', 'Explore ' . ($stats['peptides'] ?? '68') . '+ peptides with research-backed guides on benefits, dosing protocols, side effects, and safety.')"
>

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
