{{-- Engagement Tab: Bounce rate, score distribution, tier trend, rage clicks, top engaged pages --}}

{{-- Bounce Rate + Score Summary --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Bounce Rate --}}
    <div class="card p-4 flex flex-col items-center justify-center">
        <div class="text-4xl font-bold {{ $bounceRate > 70 ? 'text-red-500' : ($bounceRate > 50 ? 'text-yellow-600' : 'text-green-600') }}">
            {{ $bounceRate }}%
        </div>
        <div class="text-sm text-gray-500 mt-1">Bounce Rate</div>
        <div class="text-xs text-gray-400 mt-0.5">
            @if($bounceRate > 70)
                High - consider improving landing pages
            @elseif($bounceRate > 50)
                Average - room for improvement
            @else
                Good - visitors are engaging
            @endif
        </div>
    </div>

    {{-- Engagement Score Distribution --}}
    <div class="card p-4 col-span-1 md:col-span-2">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Engagement Score Distribution</h3>
        <div id="eng-dist-chart"></div>
    </div>
</div>

{{-- Engagement Tier Trend --}}
<div class="card p-4 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Engagement Tier Trend</h3>
    <div class="flex gap-4 mb-2 text-xs">
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-500 inline-block"></span> Hot (40+)</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-orange-400 inline-block"></span> Warm (15-39)</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-500 inline-block"></span> Cold (&lt;15)</span>
    </div>
    <div id="tier-chart"></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Rage Click Hotspots --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Rage Click Hotspots</h3>
        @if(!empty($rageClicks))
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-2 text-gray-500 font-medium">Page</th>
                            <th class="text-right py-2 px-2 text-gray-500 font-medium">Count</th>
                            <th class="text-right py-2 px-2 text-gray-500 font-medium">Last</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rageClicks as $click)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 px-2 text-gray-700 truncate max-w-[200px]" title="{{ $click['url'] }}">
                                    @php try { $path = parse_url($click['url'], PHP_URL_PATH) ?: $click['url']; } catch (\Throwable $e) { $path = $click['url']; } @endphp
                                    {{ $path }}
                                </td>
                                <td class="py-2 px-2 text-right font-medium text-red-600">{{ number_format($click['count']) }}</td>
                                <td class="py-2 px-2 text-right text-gray-500 text-xs whitespace-nowrap">{{ $click['lastOccurred'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-green-600 text-sm text-center py-4">No rage clicks detected</p>
        @endif
    </div>

    {{-- Top Engaged Pages --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Engaged Pages</h3>
        @if(!empty($topEngagedPages))
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-2 text-gray-500 font-medium">Page</th>
                            <th class="text-right py-2 px-2 text-gray-500 font-medium">Avg Score</th>
                            <th class="text-right py-2 px-2 text-gray-500 font-medium">Sessions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topEngagedPages as $page)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 px-2 text-gray-700 truncate max-w-[200px]" title="{{ $page['url'] }}">
                                    @php try { $path = parse_url($page['url'], PHP_URL_PATH) ?: $page['url']; } catch (\Throwable $e) { $path = $page['url']; } @endphp
                                    {{ $path }}
                                </td>
                                <td class="py-2 px-2 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $page['avgScore'] >= 40 ? 'bg-red-100 text-red-700' :
                                           ($page['avgScore'] >= 15 ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ $page['avgScore'] }}
                                    </span>
                                </td>
                                <td class="py-2 px-2 text-right text-gray-600">{{ number_format($page['sessions']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No engagement data</p>
        @endif
    </div>
</div>
