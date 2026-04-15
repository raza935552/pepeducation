@if($peptide->compatible_peptides && count($peptide->compatible_peptides))
<div class="card">
    <h2 class="section-heading-lg">
        <span class="section-icon bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-emerald-500/30">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
        </span>
        Compatible Peptides
    </h2>

    <div class="space-y-2">
        @foreach($peptide->compatible_peptides as $compat)
            <div class="flex items-center justify-between p-3 rounded-xl
                {{ ($compat['relationship'] ?? '') === 'Avoid' ? 'bg-red-50 border border-red-100' : 'bg-emerald-50 border border-emerald-100' }}">
                <span class="font-medium text-gray-900">{{ $compat['name'] ?? '' }}</span>
                @if(!empty($compat['relationship']))
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ match($compat['relationship']) {
                            'Synergistic' => 'bg-emerald-100 text-emerald-700',
                            'Complementary' => 'bg-blue-100 text-blue-700',
                            'Stacking' => 'bg-purple-100 text-purple-700',
                            'Being Studied' => 'bg-amber-100 text-amber-700',
                            'Avoid' => 'bg-red-100 text-red-700',
                            default => 'bg-emerald-100 text-emerald-700',
                        } }}">
                        {{ $compat['relationship'] }}
                    </span>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif
