<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">
                Browse by Category
            </h2>
            <p class="text-gray-600 max-w-xl mx-auto">
                Find peptides organized by their primary use case and research focus
            </p>
        </div>

        {{-- Categories Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('peptides.index', ['category' => $category->slug]) }}"
                   class="group relative bg-cream-50 rounded-2xl p-5 hover:bg-cream-100 border border-cream-200 hover:border-gold-300 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-xl bg-gold-100 flex items-center justify-center mb-3 group-hover:bg-gold-200 group-hover:scale-110 transition-all">
                        @include('home.partials.category-icon', ['category' => $category->slug])
                    </div>

                    {{-- Name --}}
                    <h3 class="font-semibold text-gray-900 group-hover:text-gold-600 transition-colors mb-1">
                        {{ $category->name }}
                    </h3>

                    {{-- Count --}}
                    <p class="text-sm text-gray-500">
                        {{ $category->peptides_count }} {{ Str::plural('peptide', $category->peptides_count) }}
                    </p>

                    {{-- Hover Arrow --}}
                    <div class="absolute top-5 right-5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg aria-hidden="true" class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
