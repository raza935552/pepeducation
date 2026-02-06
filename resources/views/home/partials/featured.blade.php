<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-10">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                    Featured Peptides
                </h2>
                <p class="text-gray-600">Most studied compounds in our database</p>
            </div>
            <a href="{{ route('peptides.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gray-900 hover:bg-gray-800 text-white font-medium text-sm transition-all duration-300 hover:shadow-lg">
                Browse All
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        {{-- Peptide Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredPeptides as $peptide)
                @include('home.partials.peptide-card', ['peptide' => $peptide])
            @endforeach
        </div>
    </div>
</section>
