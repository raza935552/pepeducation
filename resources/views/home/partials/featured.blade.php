{{-- Featured Peptides Section --}}
<section class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-12 lg:mb-16">
            <div>
                <h2 class="text-3xl sm:text-4xl font-bold text-heading mb-3">
                    Featured Peptides
                </h2>
                <div class="w-12 h-1 bg-primary-500 rounded-full mb-3"></div>
                <p class="text-body/70 text-lg">Most studied compounds in our database</p>
            </div>
            <a href="{{ route('peptides.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-50 text-primary-600 hover:bg-primary-100 font-semibold transition-all shrink-0">
                Browse All
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        {{-- Peptide Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @foreach($featuredPeptides as $peptide)
                @include('home.partials.peptide-card', ['peptide' => $peptide])
            @endforeach
        </div>
    </div>
</section>
