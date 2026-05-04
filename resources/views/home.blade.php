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

    {{-- Trending Searches --}}
    @if(isset($trendingSearches) && $trendingSearches->isNotEmpty())
        <section class="py-8 bg-white border-y border-surface-200">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center gap-3">
                    <p class="text-sm font-semibold text-gray-700">What people are searching:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($trendingSearches as $query)
                            <a href="{{ route('peptides.index', ['search' => $query]) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-primary-50 hover:bg-primary-100 text-primary-700 text-xs font-medium transition-colors">
                                <svg aria-hidden="true" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                {{ $query }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Go Deeper (action CTAs) --}}
    @include('home.partials.go-deeper')

    {{-- Browse by Category --}}
    @include('home.partials.categories')

    {{-- Newsletter CTA --}}
    @include('home.partials.cta')

    {{-- Quick Links --}}
    @include('home.partials.quick-links')
</x-public-layout>
