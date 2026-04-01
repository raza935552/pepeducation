{{-- Hero Section: Search-first, Google-style --}}
<section class="relative bg-dark-900 overflow-hidden">
    {{-- Subtle gradient overlay --}}
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-b from-dark-950 via-dark-900 to-dark-800"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-primary-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-secondary-500/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32 text-center">
        {{-- Trust badge --}}
        <div class="mb-8">
            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-primary-500/30 bg-primary-500/10 text-primary-300 text-sm font-medium shadow-lg shadow-primary-500/5">
                <svg aria-hidden="true" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Trusted by researchers worldwide
            </span>
        </div>

        {{-- Main heading --}}
        <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold text-white leading-[1.1] mb-6">
            Your Peptide <span class="text-primary-500">Research</span> Hub
        </h1>

        {{-- Subtitle --}}
        <p class="text-lg sm:text-xl text-dark-300 mb-10 max-w-2xl mx-auto leading-relaxed">
            Learn, track, compare, and optimize your peptide protocols — all in one place.
        </p>

        {{-- Big search bar --}}
        <div class="max-w-2xl mx-auto mb-8">
            <button type="button"
                    @click="$dispatch('open-search')"
                    class="w-full flex items-center gap-3 px-6 py-4 sm:py-5 bg-white rounded-full shadow-2xl shadow-black/20 hover:shadow-primary-500/20 transition-all duration-300 group cursor-text"
                    x-data
            >
                <svg aria-hidden="true" class="w-6 h-6 text-gray-400 group-hover:text-primary-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span class="text-gray-400 text-base sm:text-lg text-left">Search peptides, protocols, guides...</span>
                <kbd class="hidden sm:inline-flex items-center gap-1 px-2 py-1 ml-auto text-xs text-gray-400 bg-gray-100 rounded border border-gray-200 font-mono">
                    <span class="text-sm">⌘</span>K
                </kbd>
            </button>
        </div>

        {{-- Quick-link pills --}}
        <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 mb-14">
            <a href="{{ route('peptides.show', 'bpc-157') }}"
               class="px-4 py-2 rounded-full text-sm font-medium bg-dark-700/80 text-dark-200 border border-dark-600 hover:bg-primary-500/20 hover:border-primary-500/40 hover:text-primary-300 transition-all duration-200">
                BPC-157
            </a>
            <a href="{{ route('peptides.index') }}"
               class="px-4 py-2 rounded-full text-sm font-medium bg-dark-700/80 text-dark-200 border border-dark-600 hover:bg-primary-500/20 hover:border-primary-500/40 hover:text-primary-300 transition-all duration-200">
                Peptide Database
            </a>
            <a href="{{ route('peptides.show', 'semaglutide') }}"
               class="px-4 py-2 rounded-full text-sm font-medium bg-dark-700/80 text-dark-200 border border-dark-600 hover:bg-primary-500/20 hover:border-primary-500/40 hover:text-primary-300 transition-all duration-200">
                Semaglutide
            </a>
            <a href="{{ route('peptides.show', 'ghk-cu') }}"
               class="px-4 py-2 rounded-full text-sm font-medium bg-dark-700/80 text-dark-200 border border-dark-600 hover:bg-primary-500/20 hover:border-primary-500/40 hover:text-primary-300 transition-all duration-200">
                GHK-Cu
            </a>
            <a href="{{ route('peptides.index') }}"
               class="px-4 py-2 rounded-full text-sm font-medium bg-primary-500/20 text-primary-300 border border-primary-500/30 hover:bg-primary-500/30 hover:border-primary-500/50 transition-all duration-200">
                All Categories
            </a>
        </div>

        {{-- Stats row --}}
        <div class="flex items-center justify-center gap-8 sm:gap-12 lg:gap-16">
            <div class="text-center">
                <div class="text-2xl sm:text-3xl font-bold text-primary-400">{{ $stats['peptides'] ?? '70' }}+</div>
                <div class="text-sm text-dark-400 mt-1">Peptides</div>
            </div>
            <div class="w-px h-10 bg-dark-700"></div>
            <div class="text-center">
                <div class="text-2xl sm:text-3xl font-bold text-primary-400">500+</div>
                <div class="text-sm text-dark-400 mt-1">Studies</div>
            </div>
            <div class="w-px h-10 bg-dark-700"></div>
            <div class="text-center">
                <div class="text-2xl sm:text-3xl font-bold text-primary-400">{{ $stats['categories'] ?? '20' }}+</div>
                <div class="text-sm text-dark-400 mt-1">Categories</div>
            </div>
        </div>
    </div>
</section>
