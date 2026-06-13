{{-- Home: Find peptides by goal — deep links to each "best for" roundup --}}
<section class="py-14 lg:py-18 bg-white border-t border-surface-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-heading">Find Peptides by Goal</h2>
                <p class="text-body/70 mt-1">Ranked, research-backed roundups for the goals people research most.</p>
            </div>
            <a href="{{ route('peptide-goals.index') }}" class="text-sm font-semibold text-primary-600 hover:underline inline-flex items-center gap-1 shrink-0">All goals
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach(config('peptide_goals') as $g)
                <a href="{{ route('peptide-goals.show', $g['slug']) }}" class="group flex items-center gap-3 rounded-xl border border-surface-200 bg-surface-50 p-4 hover:border-primary-300 hover:shadow-sm transition-all">
                    <span class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0" style="background-color: {{ $g['accent'] }}1a;">{{ $g['emoji'] }}</span>
                    <span class="text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors leading-tight">{{ $g['h1'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
