{{-- Engagement Tab: Bounce rate, score distribution, tier trend, rage clicks, top engaged pages --}}

@php
    $fmtNum = fn ($n) => number_format((int) $n);

    $bounceColor = $bounceRate > 70 ? 'red' : ($bounceRate > 50 ? 'yellow' : 'green');
    $bounceMessage = $bounceRate > 70
        ? 'High bounce. Audit landing pages and content match.'
        : ($bounceRate > 50
            ? 'Average. Improving copy and load speed could help.'
            : 'Healthy. Visitors are engaging with the site.');
@endphp

{{-- Hero engagement metrics --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    {{-- Bounce Rate --}}
    <div class="card p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-lg bg-{{ $bounceColor }}-100 flex items-center justify-center">
                <svg aria-hidden="true" class="w-5 h-5 text-{{ $bounceColor }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Bounce Rate</p>
        </div>
        <p class="text-4xl font-bold text-{{ $bounceColor }}-600 mb-1">{{ $bounceRate }}%</p>
        <p class="text-xs text-gray-500">{{ $bounceMessage }}</p>
    </div>

    {{-- Engagement Score Distribution --}}
    <div class="card p-5 col-span-1 md:col-span-2">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Engagement Score Distribution</h3>
        <p class="text-xs text-gray-500 mb-3">How visitors are distributed across engagement scores. Higher = more interactive sessions.</p>
        <div id="eng-dist-chart"></div>
    </div>
</div>

{{-- Engagement Tier Trend --}}
<div class="card p-5 mb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="text-sm font-semibold text-gray-700">Engagement Tier Trend</h3>
            <p class="text-xs text-gray-500">Daily breakdown of Hot, Warm, and Cold visitor tiers</p>
        </div>
        <div class="flex gap-3 text-xs">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-500"></span> Hot (40+)</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-orange-400"></span> Warm (15-39)</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-400"></span> Cold (&lt;15)</span>
        </div>
    </div>
    <div id="tier-chart"></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Top Engaged Pages --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Top Engaged Pages</h3>
            <p class="text-xs text-gray-500">Highest avg engagement score</p>
        </div>
        @if(!empty($topEngagedPages))
            <div class="space-y-2">
                @foreach($topEngagedPages as $page)
                    @php
                        try { $path = parse_url($page['url'], PHP_URL_PATH) ?: $page['url']; } catch (\Throwable $e) { $path = $page['url']; }
                        $tierColor = $page['avgScore'] >= 40 ? 'red' : ($page['avgScore'] >= 15 ? 'orange' : 'blue');
                    @endphp
                    <div class="p-3 rounded-lg border border-gray-100 hover:border-gray-300 transition">
                        <div class="flex items-center justify-between gap-3 mb-1">
                            <a href="{{ $page['url'] }}" target="_blank" class="text-sm text-gray-700 hover:text-blue-600 truncate min-w-0 flex-1" title="{{ $path }}">
                                {{ $path }}
                            </a>
                            <div class="shrink-0 flex items-center gap-2">
                                <span class="text-xs text-gray-500">{{ $fmtNum($page['sessions']) }} sessions</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $tierColor }}-100 text-{{ $tierColor }}-700">
                                    {{ $page['avgScore'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-sm italic text-center py-8">No engagement data yet</p>
        @endif
    </div>

    {{-- Rage Click Hotspots --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <svg aria-hidden="true" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Rage Click Hotspots
            </h3>
            <p class="text-xs text-gray-500">Pages where users click repeatedly out of frustration</p>
        </div>
        @if(!empty($rageClicks))
            <div class="space-y-2">
                @foreach($rageClicks as $click)
                    @php
                        try { $path = parse_url($click['url'], PHP_URL_PATH) ?: $click['url']; } catch (\Throwable $e) { $path = $click['url']; }
                    @endphp
                    <div class="p-3 rounded-lg bg-red-50 border border-red-100">
                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ $click['url'] }}" target="_blank" class="text-sm text-gray-800 hover:text-red-700 truncate min-w-0 flex-1" title="{{ $path }}">
                                {{ $path }}
                            </a>
                            <div class="shrink-0 flex items-center gap-2">
                                <span class="text-xs text-red-500 whitespace-nowrap">{{ $click['lastOccurred'] }}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-200 text-red-800">{{ $fmtNum($click['count']) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 rounded-lg bg-green-50 border border-green-100 text-center">
                <svg aria-hidden="true" class="w-10 h-10 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <p class="text-sm text-green-700 font-medium">No rage clicks detected</p>
                <p class="text-xs text-green-600 mt-1">Users are interacting smoothly with the site.</p>
            </div>
        @endif
    </div>
</div>
