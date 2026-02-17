{{-- Funnels Tab: Conversion funnel, CTA, quiz, popup, lead magnets, outbound --}}

{{-- Conversion Funnel --}}
<div class="card p-4 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Conversion Funnel</h3>
    @if(!empty($conversionFunnel) && ($conversionFunnel[0]['count'] ?? 0) > 0)
        @php $maxCount = $conversionFunnel[0]['count']; @endphp
        <div class="space-y-3">
            @foreach($conversionFunnel as $i => $step)
                @php
                    $width = $maxCount > 0 ? max(round($step['count'] / $maxCount * 100), 4) : 4;
                    $colors = ['bg-blue-500', 'bg-indigo-500', 'bg-purple-500', 'bg-brand-gold', 'bg-green-500'];
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $step['label'] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold">{{ number_format($step['count']) }}</span>
                            @if($step['dropoff'] > 0)
                                <span class="text-xs text-red-500">-{{ $step['dropoff'] }}%</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-6">
                        <div class="{{ $colors[$i] ?? 'bg-gray-500' }} rounded-full h-6 flex items-center transition-all" style="width: {{ $width }}%">
                            @if($width > 15)
                                <span class="text-white text-xs font-medium px-2">
                                    {{ $maxCount > 0 ? round($step['count'] / $maxCount * 100) : 0 }}%
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-sm text-center py-4">No funnel data</p>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- CTA Performance --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">CTA Performance</h3>
        @if(($ctaStats['total'] ?? 0) > 0)
            <div class="mb-3">
                <span class="text-2xl font-bold">{{ number_format($ctaStats['total']) }}</span>
                <span class="text-sm text-gray-500 ml-1">total clicks</span>
            </div>

            @if(!empty($ctaStats['topCTAs']))
                <div class="space-y-2 mb-4">
                    @foreach(array_slice($ctaStats['topCTAs'], 0, 5) as $cta)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 truncate mr-2">{{ $cta['name'] }}</span>
                            <span class="font-medium whitespace-nowrap">{{ number_format($cta['clicks']) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(!empty($ctaStats['byType']))
                <div class="border-t border-gray-100 pt-3 mt-3">
                    <div class="text-xs font-medium text-gray-500 mb-2">By Type</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ctaStats['byType'] as $type => $count)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">
                                {{ $type }}: {{ number_format($count) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($ctaStats['byPosition']))
                <div class="border-t border-gray-100 pt-3 mt-3">
                    <div class="text-xs font-medium text-gray-500 mb-2">By Position</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ctaStats['byPosition'] as $pos => $count)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">
                                {{ $pos }}: {{ number_format($count) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <p class="text-gray-500 text-sm text-center py-4">No CTA clicks tracked</p>
        @endif
    </div>

    {{-- Quiz Performance --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Quiz Performance</h3>
        @if(($quizStats['total'] ?? 0) > 0)
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <div class="text-2xl font-bold">{{ number_format($quizStats['total']) }}</div>
                    <div class="text-xs text-gray-500">Started</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ number_format($quizStats['completed']) }}</div>
                    <div class="text-xs text-gray-500">
                        Completed
                        ({{ $quizStats['total'] > 0 ? round($quizStats['completed'] / $quizStats['total'] * 100) : 0 }}%)
                    </div>
                </div>
            </div>

            @if(!empty($quizStats['byQuiz']))
                <div class="border-t border-gray-100 pt-3">
                    <div class="text-xs font-medium text-gray-500 mb-2">By Quiz</div>
                    <div class="space-y-2">
                        @foreach($quizStats['byQuiz'] as $name => $count)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 truncate mr-2">{{ $name }}</span>
                                <span class="font-medium">{{ number_format($count) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <p class="text-gray-500 text-sm text-center py-4">No quiz data</p>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Popup Performance --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Popup Performance</h3>
        @if(($popupStats['impressions'] ?? 0) > 0)
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <div class="text-xl font-bold">{{ number_format($popupStats['impressions']) }}</div>
                    <div class="text-xs text-gray-500">Impressions</div>
                </div>
                <div>
                    <div class="text-xl font-bold text-green-600">{{ number_format($popupStats['conversions']) }}</div>
                    <div class="text-xs text-gray-500">
                        Conversions
                        ({{ $popupStats['impressions'] > 0 ? round($popupStats['conversions'] / $popupStats['impressions'] * 100, 1) : 0 }}%)
                    </div>
                </div>
            </div>
            @if(!empty($popupStats['byPopup']))
                <div class="space-y-1 border-t border-gray-100 pt-2">
                    @foreach($popupStats['byPopup'] as $name => $count)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600 truncate mr-2">{{ $name }}</span>
                            <span class="font-medium">{{ number_format($count) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <p class="text-gray-500 text-sm text-center py-4">No popup data</p>
        @endif
    </div>

    {{-- Lead Magnet Downloads --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Lead Magnet Downloads</h3>
        @if(!empty($leadMagnetStats))
            <div class="space-y-2">
                @foreach($leadMagnetStats as $name => $count)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 truncate mr-2">{{ $name }}</span>
                        <span class="font-medium">{{ number_format($count) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No downloads</p>
        @endif
    </div>

    {{-- Outbound Links --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Outbound Links</h3>
        @if(!empty($outboundStats))
            <div class="space-y-2">
                @foreach($outboundStats as $name => $count)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 truncate mr-2">{{ $name }}</span>
                        <span class="font-medium">{{ number_format($count) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No outbound clicks</p>
        @endif
    </div>
</div>
