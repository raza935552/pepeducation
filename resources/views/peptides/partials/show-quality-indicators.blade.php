@if($peptide->quality_indicators && count($peptide->quality_indicators))
<div class="card">
    <h2 class="section-heading-lg">
        <span class="section-icon bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-emerald-500/30">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </span>
        Research Indicators
    </h2>

    <div class="space-y-3">
        @foreach($peptide->quality_indicators as $indicator)
            @php
                $status = $indicator['status'] ?? '✓';
                $statusConfig = match($status) {
                    '✓' => ['bg' => 'bg-emerald-50 border-emerald-100', 'icon_bg' => 'bg-emerald-100', 'icon_text' => 'text-emerald-600'],
                    '!' => ['bg' => 'bg-amber-50 border-amber-100', 'icon_bg' => 'bg-amber-100', 'icon_text' => 'text-amber-600'],
                    '✗' => ['bg' => 'bg-amber-50 border-amber-100', 'icon_bg' => 'bg-amber-100', 'icon_text' => 'text-amber-600'],
                    default => ['bg' => 'bg-emerald-50 border-emerald-100', 'icon_bg' => 'bg-emerald-100', 'icon_text' => 'text-emerald-600'],
                };
            @endphp
            <div class="flex items-start gap-3 p-4 rounded-xl {{ $statusConfig['bg'] }} border">
                <span class="shrink-0 mt-0.5 flex items-center justify-center w-6 h-6 rounded-full {{ $statusConfig['icon_bg'] }}">
                    <span class="{{ $statusConfig['icon_text'] }} text-sm font-bold">{{ $status }}</span>
                </span>
                <div>
                    @if(!empty($indicator['title']))
                        <p class="font-medium text-gray-900">{{ $indicator['title'] }}</p>
                    @endif
                    @if(!empty($indicator['description']))
                        <p class="text-sm text-gray-600 mt-0.5">{{ $indicator['description'] }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
