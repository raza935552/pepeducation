{{-- Traffic Tab: Sources, UTM, devices, browsers, OS, new vs returning, landing pages --}}

@php
    $fmtNum = fn ($n) => number_format((int) $n);
@endphp

{{-- New vs Returning + Device Split (visual hero row) --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- New vs Returning --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">New vs Returning Visitors</h3>
        @if(($newVsReturning['total'] ?? 0) > 0)
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-700 mb-1">New</p>
                    <p class="text-3xl font-bold text-blue-700">{{ $fmtNum($newVsReturning['new']) }}</p>
                    <p class="text-xs text-blue-600 mt-1">{{ $newVsReturning['newPct'] }}% of total</p>
                </div>
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 mb-1">Returning</p>
                    <p class="text-3xl font-bold text-emerald-700">{{ $fmtNum($newVsReturning['returning']) }}</p>
                    <p class="text-xs text-emerald-600 mt-1">{{ $newVsReturning['returningPct'] }}% of total</p>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5 flex overflow-hidden">
                <div class="bg-blue-500 h-2.5" style="width: {{ $newVsReturning['newPct'] }}%"></div>
                <div class="bg-emerald-500 h-2.5" style="width: {{ $newVsReturning['returningPct'] }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-3">
                @if($newVsReturning['returningPct'] >= 30)
                    Healthy retention - {{ $newVsReturning['returningPct'] }}% of visitors come back.
                @else
                    Mostly new visitors. Build retention with email and content.
                @endif
            </p>
        @else
            <p class="text-gray-400 text-sm italic text-center py-8">No visitor data yet</p>
        @endif
    </div>

    {{-- Device Breakdown --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Device Breakdown</h3>
        @if(!empty($deviceBreakdown))
            @php
                $deviceTotal = array_sum($deviceBreakdown) ?: 1;
                $deviceColors = ['mobile' => 'cyan', 'desktop' => 'blue', 'tablet' => 'purple', 'other' => 'gray'];
            @endphp
            <div class="space-y-3">
                @foreach($deviceBreakdown as $deviceName => $count)
                    @php
                        $name = strtolower($deviceName);
                        $color = $deviceColors[$name] ?? 'gray';
                        $pct = round(($count / $deviceTotal) * 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700 capitalize">{{ $deviceName }}</span>
                            <span class="font-bold text-gray-900">{{ $fmtNum($count) }} <span class="text-gray-500 font-normal">({{ $pct }}%)</span></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-{{ $color }}-500 rounded-full h-2" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-sm italic text-center py-8">No device data</p>
        @endif
    </div>
</div>

{{-- Traffic Sources --}}
<div class="card p-5 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Traffic Sources</h3>
    @if(!empty($trafficSources))
        @php
            $srcTotal = array_sum($trafficSources) ?: 1;
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($trafficSources as $sourceName => $sessions)
                @php
                    $pct = round(($sessions / $srcTotal) * 100);
                @endphp
                <div class="p-4 rounded-xl border border-gray-200 hover:border-blue-300 transition">
                    <div class="flex items-center justify-between mb-2 gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                <svg aria-hidden="true" class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900 truncate" title="{{ $sourceName }}">{{ $sourceName }}</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900 shrink-0">{{ $fmtNum($sessions) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-full h-1.5" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $pct }}% of sessions</p>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-400 text-sm italic text-center py-8">No traffic source data yet</p>
    @endif
</div>

{{-- UTM Campaigns --}}
<div class="card p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-700">UTM Campaign Performance</h3>
            <p class="text-xs text-gray-500">Sessions and conversions by source/medium/campaign</p>
        </div>
    </div>
    @if(!empty($utmCampaigns))
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Source</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Medium</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Campaign</th>
                        <th class="text-right py-2 px-3 text-xs font-semibold text-gray-500 w-24">Sessions</th>
                        <th class="text-right py-2 px-3 text-xs font-semibold text-gray-500 w-24">Conversions</th>
                        <th class="text-right py-2 px-3 text-xs font-semibold text-gray-500 w-20">CVR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($utmCampaigns as $utm)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-3 font-medium text-gray-900">{{ $utm['source'] }}</td>
                            <td class="py-2 px-3 text-gray-600">
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ $utm['medium'] }}</span>
                            </td>
                            <td class="py-2 px-3 text-gray-600 truncate max-w-[200px]">{{ $utm['campaign'] }}</td>
                            <td class="py-2 px-3 text-right font-bold">{{ $fmtNum($utm['sessions']) }}</td>
                            <td class="py-2 px-3 text-right font-semibold text-green-700">{{ $fmtNum($utm['conversions']) }}</td>
                            <td class="py-2 px-3 text-right">
                                <span class="{{ $utm['cvr'] > 5 ? 'text-green-600 font-bold' : ($utm['cvr'] > 1 ? 'text-yellow-600 font-semibold' : 'text-gray-400') }}">{{ $utm['cvr'] }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-400 text-sm italic text-center py-8">No UTM campaigns tracked yet</p>
    @endif
</div>

{{-- Top Landing Pages --}}
<div class="card p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-700">Top Landing Pages</h3>
        <p class="text-xs text-gray-500">First page in a session, with bounce rate</p>
    </div>
    @if(!empty($topLandingPages))
        <div class="space-y-2">
            @foreach($topLandingPages as $i => $page)
                @php
                    try { $path = parse_url($page['url'], PHP_URL_PATH) ?: $page['url']; } catch (\Throwable $e) { $path = $page['url']; }
                    $maxV = collect($topLandingPages)->max('sessions') ?: 1;
                    $pct = $page['sessions'] / $maxV * 100;
                @endphp
                <div class="p-3 rounded-lg border border-gray-100 hover:border-gray-300 transition">
                    <div class="flex items-center justify-between gap-3 mb-1">
                        <a href="{{ $page['url'] }}" target="_blank" class="text-sm text-gray-700 hover:text-blue-600 truncate min-w-0 flex-1" title="{{ $path }}">
                            <span class="text-gray-400 mr-1">{{ $i + 1 }}.</span>{{ $path }}
                        </a>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-sm font-bold text-gray-900">{{ $fmtNum($page['sessions']) }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $page['bounceRate'] > 70 ? 'bg-red-100 text-red-700' : ($page['bounceRate'] > 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                {{ $page['bounceRate'] }}% bounce
                            </span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1">
                        <div class="bg-blue-400 rounded-full h-1" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-400 text-sm italic text-center py-8">No landing page data</p>
    @endif
</div>

{{-- Browsers + OS --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Browsers</h3>
        @if(!empty($browserBreakdown))
            @php $browserTotal = array_sum($browserBreakdown) ?: 1; @endphp
            <div class="space-y-2">
                @foreach(array_slice($browserBreakdown, 0, 6, true) as $browserName => $count)
                    @php $pct = round(($count / $browserTotal) * 100); @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ $browserName }}</span>
                            <span class="font-semibold">{{ $fmtNum($count) }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-purple-500 rounded-full h-1.5" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-sm italic text-center py-4">No browser data</p>
        @endif
    </div>

    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Operating Systems</h3>
        @if(!empty($osBreakdown))
            @php $osTotal = array_sum($osBreakdown) ?: 1; @endphp
            <div class="space-y-2">
                @foreach(array_slice($osBreakdown, 0, 6, true) as $osName => $count)
                    @php $pct = round(($count / $osTotal) * 100); @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ $osName }}</span>
                            <span class="font-semibold">{{ $fmtNum($count) }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-cyan-500 rounded-full h-1.5" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-sm italic text-center py-4">No OS data</p>
        @endif
    </div>
</div>
