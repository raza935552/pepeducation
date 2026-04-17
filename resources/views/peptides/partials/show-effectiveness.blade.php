@if($peptide->effectiveness_ratings && count($peptide->effectiveness_ratings))
<div class="card">
    <h2 class="section-heading-lg">
        <span class="section-icon bg-gradient-to-br from-amber-400 to-amber-600 shadow-amber-500/30">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </span>
        Effectiveness Ratings
    </h2>

    <div class="space-y-4">
        @foreach($peptide->effectiveness_ratings as $category => $rating)
            @php $rating = min(10, max(0, (int) $rating)); @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-medium text-gray-700">{{ Str::headline(str_replace('_', ' ', $category)) }}</span>
                    <span class="text-sm font-bold text-gray-900">{{ $rating }}/10</span>
                </div>
                <div class="h-2.5 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300
                        {{ $rating >= 8 ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' :
                           ($rating >= 5 ? 'bg-gradient-to-r from-amber-400 to-amber-500' :
                                           'bg-gradient-to-r from-amber-400 to-amber-500') }}"
                        style="width: {{ $rating * 10 }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
