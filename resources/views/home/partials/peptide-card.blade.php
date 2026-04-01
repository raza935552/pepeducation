{{-- Peptide Card (cleaner design) --}}
<a href="{{ route('peptides.show', $peptide) }}"
   class="group block bg-white rounded-xl border border-surface-200 p-6 hover:shadow-xl hover:border-primary-300 transition-all duration-300 hover:-translate-y-1">
    {{-- Header --}}
    <div class="flex items-start gap-4 mb-4">
        {{-- Abbreviation Badge --}}
        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg shadow-primary-500/20 shrink-0">
            <span class="text-base font-bold text-white tracking-wide">
                {{ strtoupper(substr($peptide->abbreviation ?? $peptide->name, 0, 3)) }}
            </span>
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="text-lg font-bold text-heading group-hover:text-primary-600 transition-colors truncate">
                {{ $peptide->name }}
            </h3>
            <p class="text-sm text-body/60 truncate mt-0.5">
                {{ $peptide->type ?? Str::limit($peptide->overview, 40) }}
            </p>
        </div>
    </div>

    {{-- Categories --}}
    <div class="flex flex-wrap gap-1.5 mb-5">
        @foreach($peptide->categories->take(3) as $category)
            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                {{ $category->name }}
            </span>
        @endforeach
    </div>

    {{-- Footer --}}
    <div class="flex items-center justify-end pt-4 border-t border-surface-200">
        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-500 group-hover:gap-2.5 transition-all">
            Learn More
            <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </span>
    </div>
</a>
