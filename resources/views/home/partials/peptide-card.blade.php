{{-- Peptide Card (polished design) --}}
<a href="{{ route('peptides.show', $peptide) }}"
   class="group block bg-white rounded-2xl border border-surface-200 border-t-2 border-t-primary-500 p-6 lg:p-7 hover:shadow-xl hover:border-primary-200 hover:border-t-primary-500 transition-all duration-300 hover:-translate-y-1 hover:scale-[1.02]">
    {{-- Header --}}
    <div class="flex items-start gap-4 mb-5">
        {{-- Abbreviation Badge --}}
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg shadow-primary-500/25 shrink-0">
            <span class="text-lg font-bold text-white tracking-wide">
                {{ strtoupper(substr($peptide->abbreviation ?? $peptide->name, 0, 3)) }}
            </span>
        </div>
        <div class="min-w-0 flex-1 pt-1">
            <h3 class="text-lg font-bold text-heading group-hover:text-primary-600 transition-colors truncate">
                {{ $peptide->name }}
            </h3>
            <p class="text-sm text-body/60 truncate mt-1">
                {{ $peptide->type ?? Str::limit($peptide->overview, 40) }}
            </p>
        </div>
    </div>

    {{-- Categories --}}
    <div class="flex flex-wrap gap-1.5 mb-6">
        @foreach($peptide->categories->take(3) as $category)
            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-600">
                {{ $category->name }}
            </span>
        @endforeach
    </div>

    {{-- Footer --}}
    <div class="flex items-center justify-between pt-4 border-t border-surface-200">
        <span class="text-sm text-body/50 group-hover:text-body/70 transition-colors">View details</span>
        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-primary-50 text-sm font-semibold text-primary-600 group-hover:bg-primary-500 group-hover:text-white group-hover:gap-2.5 transition-all duration-300">
            Learn More
            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </span>
    </div>
</a>
