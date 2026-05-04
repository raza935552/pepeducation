{{-- Funnels Tab: Conversion funnel, CTA, quiz, popup, lead magnets, outbound --}}

@php
    $fmtNum = fn ($n) => number_format((int) $n);
@endphp

{{-- Conversion Funnel --}}
<div class="card p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-700">Conversion Funnel</h3>
            <p class="text-xs text-gray-500">Step-by-step drop-off from first session to conversion</p>
        </div>
    </div>
    @if(!empty($conversionFunnel) && ($conversionFunnel[0]['count'] ?? 0) > 0)
        @php $maxCount = $conversionFunnel[0]['count']; @endphp
        <div class="space-y-3">
            @foreach($conversionFunnel as $i => $step)
                @php
                    $width = $maxCount > 0 ? max(round($step['count'] / $maxCount * 100), 4) : 4;
                    $colors = ['bg-blue-500', 'bg-indigo-500', 'bg-purple-500', 'bg-cyan-500', 'bg-green-500'];
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-700">{{ $step['label'] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-900">{{ $fmtNum($step['count']) }}</span>
                            @if($step['dropoff'] > 0)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-red-50 text-red-600 text-xs font-medium">-{{ $step['dropoff'] }}%</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-7 overflow-hidden">
                        <div class="{{ $colors[$i] ?? 'bg-gray-500' }} rounded-full h-7 flex items-center transition-all duration-300" style="width: {{ $width }}%">
                            @if($width > 12)
                                <span class="text-white text-xs font-semibold px-3">
                                    {{ $maxCount > 0 ? round($step['count'] / $maxCount * 100) : 0 }}%
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-400 text-sm italic text-center py-8">No funnel data yet</p>
    @endif
</div>

{{-- CTA + Quiz --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- CTA Performance --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">CTA Performance</h3>
            <span class="text-xs text-gray-400">Click-throughs</span>
        </div>
        @if(($ctaStats['total'] ?? 0) > 0)
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 mb-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700 mb-1">Total CTA Clicks</p>
                <p class="text-3xl font-bold text-blue-700">{{ $fmtNum($ctaStats['total']) }}</p>
            </div>

            @if(!empty($ctaStats['topCTAs']))
                <div class="mb-4">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Top CTAs</p>
                    @php $maxCta = collect($ctaStats['topCTAs'])->max('clicks') ?: 1; @endphp
                    <div class="space-y-2">
                        @foreach(array_slice($ctaStats['topCTAs'], 0, 5) as $cta)
                            @php $pct = ($cta['clicks'] / $maxCta) * 100; @endphp
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-700 truncate">{{ $cta['name'] }}</span>
                                    <span class="font-bold text-gray-900">{{ $fmtNum($cta['clicks']) }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-blue-500 rounded-full h-1.5" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($ctaStats['byType']) || !empty($ctaStats['byPosition']))
                <div class="border-t border-gray-100 pt-3 grid grid-cols-2 gap-3">
                    @if(!empty($ctaStats['byType']))
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1.5">By Type</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($ctaStats['byType'] as $type => $count)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">
                                        {{ $type }}: <span class="font-semibold ml-1">{{ $fmtNum($count) }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if(!empty($ctaStats['byPosition']))
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1.5">By Position</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($ctaStats['byPosition'] as $pos => $count)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">
                                        {{ $pos }}: <span class="font-semibold ml-1">{{ $fmtNum($count) }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @else
            <p class="text-gray-400 text-sm italic text-center py-8">No CTA clicks tracked yet</p>
        @endif
    </div>

    {{-- Quiz Performance --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Quiz Performance</h3>
            <span class="text-xs text-gray-400">Started vs completed</span>
        </div>
        @if(($quizStats['total'] ?? 0) > 0)
            @php $completionRate = $quizStats['total'] > 0 ? round($quizStats['completed'] / $quizStats['total'] * 100) : 0; @endphp
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="p-4 rounded-xl bg-purple-50 border border-purple-100">
                    <p class="text-xs font-semibold uppercase tracking-wider text-purple-700 mb-1">Started</p>
                    <p class="text-2xl font-bold text-purple-700">{{ $fmtNum($quizStats['total']) }}</p>
                </div>
                <div class="p-4 rounded-xl bg-green-50 border border-green-100">
                    <p class="text-xs font-semibold uppercase tracking-wider text-green-700 mb-1">Completed</p>
                    <p class="text-2xl font-bold text-green-700">{{ $fmtNum($quizStats['completed']) }}</p>
                    <p class="text-xs text-green-600 mt-1">{{ $completionRate }}% rate</p>
                </div>
            </div>

            @if(!empty($quizStats['byQuiz']))
                <div class="border-t border-gray-100 pt-3">
                    <p class="text-xs font-semibold text-gray-600 mb-2">By Quiz</p>
                    <div class="space-y-1.5">
                        @foreach($quizStats['byQuiz'] as $name => $count)
                            <div class="flex items-center justify-between text-sm py-1">
                                <span class="text-gray-700 truncate">{{ $name }}</span>
                                <span class="font-bold text-gray-900">{{ $fmtNum($count) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <p class="text-gray-400 text-sm italic text-center py-8">No quiz data yet</p>
        @endif
    </div>
</div>

{{-- Popup + Lead Magnets + Outbound --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Popup Performance --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Popup Performance</h3>
        @if(($popupStats['impressions'] ?? 0) > 0)
            @php $cvr = $popupStats['impressions'] > 0 ? round($popupStats['conversions'] / $popupStats['impressions'] * 100, 1) : 0; @endphp
            <div class="grid grid-cols-2 gap-2 mb-3">
                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <p class="text-xs text-gray-500">Impressions</p>
                    <p class="text-xl font-bold text-gray-900">{{ $fmtNum($popupStats['impressions']) }}</p>
                </div>
                <div class="p-3 rounded-lg bg-green-50 border border-green-100">
                    <p class="text-xs text-green-700">Conversions</p>
                    <p class="text-xl font-bold text-green-700">{{ $fmtNum($popupStats['conversions']) }}</p>
                    <p class="text-xs text-green-600">{{ $cvr }}% CVR</p>
                </div>
            </div>
            @if(!empty($popupStats['byPopup']))
                <div class="border-t border-gray-100 pt-2 space-y-1">
                    @foreach($popupStats['byPopup'] as $name => $count)
                        <div class="flex items-center justify-between text-xs py-1">
                            <span class="text-gray-700 truncate">{{ $name }}</span>
                            <span class="font-bold">{{ $fmtNum($count) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <p class="text-gray-400 text-sm italic text-center py-8">No popup data</p>
        @endif
    </div>

    {{-- Lead Magnet Downloads --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Lead Magnet Downloads</h3>
        @if(!empty($leadMagnetStats))
            <div class="space-y-2">
                @foreach($leadMagnetStats as $name => $count)
                    <div class="flex items-center justify-between text-sm py-1.5 border-b border-gray-50 last:border-0">
                        <span class="text-gray-700 truncate mr-2">{{ $name }}</span>
                        <span class="font-bold text-gray-900">{{ $fmtNum($count) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-sm italic text-center py-8">No downloads yet</p>
        @endif
    </div>

    {{-- Outbound Links --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Outbound Links</h3>
        @if(!empty($outboundStats))
            <div class="space-y-2">
                @foreach($outboundStats as $name => $count)
                    <div class="flex items-center justify-between text-sm py-1.5 border-b border-gray-50 last:border-0">
                        <span class="text-gray-700 truncate mr-2">{{ $name }}</span>
                        <span class="font-bold text-cyan-700">{{ $fmtNum($count) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-sm italic text-center py-8">No outbound clicks tracked</p>
        @endif
    </div>
</div>
