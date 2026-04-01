{{-- Browse by Category Section (polished) --}}
<section class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Section Header --}}
        <div class="text-center mb-14">
            <h2 class="text-3xl sm:text-4xl font-bold text-heading mb-4">
                Browse by Category
            </h2>
            <div class="w-12 h-1 bg-primary-500 rounded-full mx-auto mb-5"></div>
            <p class="text-body/70 text-lg max-w-xl mx-auto">
                Find peptides organized by their primary use case and research focus
            </p>
        </div>

        {{-- Categories Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5 lg:gap-6">
            @foreach($categories as $category)
                <a href="{{ route('peptides.index', ['category' => $category->slug]) }}"
                   class="group relative bg-white rounded-2xl p-6 lg:p-7 border border-surface-200 border-t-2 border-t-primary-500/60 hover:border-primary-300 hover:border-t-primary-500 transition-all duration-300 hover:-translate-y-1 hover:scale-[1.02] hover:shadow-xl">
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center mb-5 group-hover:shadow-md group-hover:shadow-primary-500/10 group-hover:scale-110 transition-all">
                        @include('home.partials.category-icon', ['category' => $category->slug])
                    </div>

                    {{-- Name --}}
                    <h3 class="font-bold text-heading group-hover:text-primary-600 transition-colors mb-2">
                        {{ $category->name }}
                    </h3>

                    {{-- Count --}}
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-600">
                        <span class="text-sm font-bold">{{ $category->peptides_count }}</span>
                        <span class="text-xs font-medium">{{ Str::plural('peptide', $category->peptides_count) }}</span>
                    </div>

                    {{-- Hover Arrow --}}
                    <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 translate-x-0 group-hover:translate-x-1 transition-all duration-300">
                        <svg aria-hidden="true" class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
