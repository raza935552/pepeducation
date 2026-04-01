{{-- Browse by Category Section (polished) --}}
<section class="py-16 lg:py-24 bg-surface-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-heading mb-3">
                Browse by Category
            </h2>
            <p class="text-body/70 text-lg max-w-xl mx-auto">
                Find peptides organized by their primary use case and research focus
            </p>
        </div>

        {{-- Categories Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-5">
            @foreach($categories as $category)
                <a href="{{ route('peptides.index', ['category' => $category->slug]) }}"
                   class="group relative bg-white rounded-2xl p-6 lg:p-7 border border-surface-200 hover:border-primary-300 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    {{-- Icon --}}
                    <div class="w-11 h-11 rounded-xl bg-primary-50 flex items-center justify-center mb-4 group-hover:bg-primary-100 group-hover:scale-110 transition-all">
                        @include('home.partials.category-icon', ['category' => $category->slug])
                    </div>

                    {{-- Name --}}
                    <h3 class="font-semibold text-heading group-hover:text-primary-600 transition-colors mb-1">
                        {{ $category->name }}
                    </h3>

                    {{-- Count --}}
                    <p class="text-sm text-body/60">
                        {{ $category->peptides_count }} {{ Str::plural('peptide', $category->peptides_count) }}
                    </p>

                    {{-- Hover Arrow --}}
                    <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg aria-hidden="true" class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
