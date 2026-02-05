@if($peptide->key_benefits && count($peptide->key_benefits))
<div class="card">
    <h2 class="section-heading-lg">
        <span class="section-icon bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-emerald-500/30">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </span>
        Key Benefits
    </h2>

    <div class="space-y-3">
        @foreach($peptide->key_benefits as $index => $benefit)
            <div class="flex items-start gap-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30">
                <span class="shrink-0 flex items-center justify-center w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 text-white text-sm font-bold">
                    {{ $index + 1 }}
                </span>
                <span class="text-gray-700 dark:text-cream-300 pt-0.5">{{ $benefit }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif
