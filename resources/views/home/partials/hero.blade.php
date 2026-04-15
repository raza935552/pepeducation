{{-- Hero Section: Search-first, Google-style --}}
<section class="relative bg-dark-900 overflow-hidden">
    {{-- Gradient mesh background --}}
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-b from-dark-950 via-dark-900 to-dark-800"></div>
        {{-- Primary radial glow --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[700px] bg-primary-500/10 rounded-full blur-3xl"></div>
        {{-- Secondary accent glow --}}
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-secondary-500/5 rounded-full blur-3xl"></div>
        {{-- Extra mesh dot pattern overlay --}}
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, rgb(var(--primary-400)) 1px, transparent 1px); background-size: 32px 32px;"></div>
        {{-- Diagonal gradient streak --}}
        <div class="absolute top-1/4 -left-32 w-[600px] h-[2px] bg-gradient-to-r from-transparent via-primary-500/20 to-transparent rotate-[30deg]"></div>
        <div class="absolute bottom-1/3 -right-32 w-[500px] h-[2px] bg-gradient-to-r from-transparent via-secondary-500/10 to-transparent -rotate-[20deg]"></div>
    </div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-36 text-center">
        {{-- Trust badge --}}
        <div class="mb-10">
            <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-primary-500/30 bg-primary-500/10 text-primary-300 text-sm font-medium shadow-lg shadow-primary-500/10 backdrop-blur-sm">
                <svg aria-hidden="true" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Trusted by researchers worldwide
            </span>
        </div>

        {{-- Main heading --}}
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-white leading-[1.08] mb-6" style="text-shadow: 0 0 80px rgb(var(--primary-500) / 0.3), 0 2px 4px rgba(0,0,0,0.3);">
            The <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-primary-300">Peptide</span> Encyclopedia
        </h1>

        {{-- Subtitle --}}
        <p class="text-lg sm:text-xl text-dark-300 mb-12 max-w-2xl mx-auto leading-relaxed">
            Research-backed guides on {{ $stats['peptides'] ?? '68' }}+ peptides. Benefits, dosing protocols, side effects, and safety profiles.
        </p>

        {{-- Big search bar with pulse animation --}}
        <div class="max-w-2xl mx-auto mb-10">
            <button type="button"
                    @click="$dispatch('open-search')"
                    class="w-full flex items-center gap-4 px-7 py-5 sm:py-6 bg-white rounded-full shadow-2xl shadow-black/20 hover:shadow-primary-500/25 transition-all duration-300 group cursor-text ring-2 ring-primary-500/0 hover:ring-primary-500/30 animate-search-glow"
                    x-data
            >
                <svg aria-hidden="true" class="w-7 h-7 text-primary-500/60 group-hover:text-primary-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span class="text-gray-400 text-lg sm:text-xl text-left">Search peptides, protocols, guides...</span>
                <kbd class="hidden sm:inline-flex items-center gap-1 px-3 py-1.5 ml-auto text-xs text-gray-400 bg-gray-100 rounded-lg border border-gray-200 font-mono">
                    <span class="text-sm">⌘</span>K
                </kbd>
            </button>
        </div>

        {{-- Quick-link pills --}}
        <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 mb-16">
            <a href="{{ route('peptides.show', 'bpc-157') }}"
               class="px-5 py-2.5 rounded-full text-sm font-medium bg-dark-700/80 text-dark-200 border border-dark-600 hover:bg-primary-500/20 hover:border-primary-500/40 hover:text-primary-300 transition-all duration-200">
                BPC-157
            </a>
            <a href="{{ route('peptides.index') }}"
               class="px-5 py-2.5 rounded-full text-sm font-medium bg-dark-700/80 text-dark-200 border border-dark-600 hover:bg-primary-500/20 hover:border-primary-500/40 hover:text-primary-300 transition-all duration-200">
                Peptide Database
            </a>
            <a href="{{ route('peptides.show', 'semaglutide') }}"
               class="px-5 py-2.5 rounded-full text-sm font-medium bg-dark-700/80 text-dark-200 border border-dark-600 hover:bg-primary-500/20 hover:border-primary-500/40 hover:text-primary-300 transition-all duration-200">
                Semaglutide
            </a>
            <a href="{{ route('peptides.show', 'ghk-cu') }}"
               class="px-5 py-2.5 rounded-full text-sm font-medium bg-dark-700/80 text-dark-200 border border-dark-600 hover:bg-primary-500/20 hover:border-primary-500/40 hover:text-primary-300 transition-all duration-200">
                GHK-Cu
            </a>
            <a href="{{ route('peptides.index') }}"
               class="px-5 py-2.5 rounded-full text-sm font-medium bg-primary-500/20 text-primary-300 border border-primary-500/30 hover:bg-primary-500/30 hover:border-primary-500/50 transition-all duration-200">
                All Categories
            </a>
        </div>

        {{-- Stats row --}}
        <div class="flex items-center justify-center gap-10 sm:gap-14 lg:gap-20">
            <div class="text-center">
                <div class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-1">{{ $stats['peptides'] ?? '70' }}+</div>
                <div class="text-sm sm:text-base text-dark-400 font-medium tracking-wide uppercase">Peptides</div>
            </div>
            <div class="w-px h-14 bg-dark-700/60"></div>
            <div class="text-center">
                <div class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-1">500+</div>
                <div class="text-sm sm:text-base text-dark-400 font-medium tracking-wide uppercase">Studies</div>
            </div>
            <div class="w-px h-14 bg-dark-700/60"></div>
            <div class="text-center">
                <div class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-1">{{ $stats['categories'] ?? '20' }}+</div>
                <div class="text-sm sm:text-base text-dark-400 font-medium tracking-wide uppercase">Categories</div>
            </div>
        </div>
    </div>
</section>
