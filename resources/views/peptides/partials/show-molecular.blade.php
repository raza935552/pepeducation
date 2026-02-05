@if($peptide->molecular_weight || $peptide->amino_acid_length || $peptide->half_life)
<div class="card mt-6">
    <h3 class="section-heading">
        <span class="section-icon-sm bg-gradient-to-br from-caramel-400 to-caramel-600 shadow-caramel-500/30">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
        </span>
        Molecular Info
    </h3>

    <dl class="space-y-3">
        @if($peptide->molecular_weight)
        <div class="flex items-center justify-between py-2 border-b border-cream-200 dark:border-brown-700">
            <dt class="text-sm text-gray-500 dark:text-cream-500">Molecular Weight</dt>
            <dd class="text-gray-900 dark:text-cream-100 font-semibold">{{ number_format($peptide->molecular_weight, 2) }} Da</dd>
        </div>
        @endif

        @if($peptide->amino_acid_length)
        <div class="flex items-center justify-between py-2 border-b border-cream-200 dark:border-brown-700">
            <dt class="text-sm text-gray-500 dark:text-cream-500">Amino Acids</dt>
            <dd class="text-gray-900 dark:text-cream-100 font-semibold">{{ $peptide->amino_acid_length }}</dd>
        </div>
        @endif

        @if($peptide->peak_time)
        <div class="flex items-center justify-between py-2 border-b border-cream-200 dark:border-brown-700">
            <dt class="text-sm text-gray-500 dark:text-cream-500">Peak Time</dt>
            <dd class="text-gray-900 dark:text-cream-100 font-semibold">{{ $peptide->peak_time }}</dd>
        </div>
        @endif

        @if($peptide->half_life)
        <div class="flex items-center justify-between py-2 border-b border-cream-200 dark:border-brown-700">
            <dt class="text-sm text-gray-500 dark:text-cream-500">Half Life</dt>
            <dd class="text-gray-900 dark:text-cream-100 font-semibold">{{ $peptide->half_life }}</dd>
        </div>
        @endif

        @if($peptide->clearance_time)
        <div class="flex items-center justify-between py-2">
            <dt class="text-sm text-gray-500 dark:text-cream-500">Clearance Time</dt>
            <dd class="text-gray-900 dark:text-cream-100 font-semibold">{{ $peptide->clearance_time }}</dd>
        </div>
        @endif
    </dl>
</div>
@endif
