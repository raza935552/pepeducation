{{-- Overview Tab: KPIs with deltas, segments, top events, daily trend, content highlights --}}

@php
    $fmtNum = fn ($n) => number_format((int) $n);
    $fmtDelta = function ($current, $previous) {
        if ($previous == 0) {
            return ['delta' => $current > 0 ? null : 0, 'pct' => null, 'positive' => $current > 0];
        }
        $pct = round(($current - $previous) / $previous * 100, 1);
        return ['delta' => $current - $previous, 'pct' => $pct, 'positive' => $pct >= 0];
    };
@endphp

{{-- Hero KPI Row: 4 large headline metrics --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $heroKpis = [
            [
                'label' => 'Sessions',
                'value' => $overview['sessions'] ?? 0,
                'prev'  => $prevOverview['sessions'] ?? 0,
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
                'color' => 'blue',
            ],
            [
                'label' => 'Page Views',
                'value' => $overview['pageViews'] ?? 0,
                'prev'  => $prevOverview['pageViews'] ?? 0,
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
                'color' => 'purple',
            ],
            [
                'label' => 'Subscribers',
                'value' => $overview['newSubscribers'] ?? 0,
                'prev'  => $prevOverview['newSubscribers'] ?? 0,
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                'color' => 'emerald',
            ],
            [
                'label' => 'Conversions',
                'value' => $overview['conversions'] ?? 0,
                'prev'  => $prevOverview['conversions'] ?? 0,
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'color' => 'cyan',
            ],
        ];
    @endphp

    @foreach($heroKpis as $kpi)
        @php $d = $fmtDelta($kpi['value'], $kpi['prev']); @endphp
        <div class="card p-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 -mr-8 -mt-8 rounded-full bg-{{ $kpi['color'] }}-100/40"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-{{ $kpi['color'] }}-100 flex items-center justify-center">
                        <svg aria-hidden="true" class="w-5 h-5 text-{{ $kpi['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $kpi['icon'] !!}
                        </svg>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $kpi['label'] }}</p>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $fmtNum($kpi['value']) }}</p>
                @if($d['pct'] !== null)
                    <div class="flex items-center gap-1 mt-2 text-xs">
                        @if($d['pct'] >= 0)
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">
                                <svg aria-hidden="true" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                {{ $d['pct'] }}%
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">
                                <svg aria-hidden="true" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                {{ abs($d['pct']) }}%
                            </span>
                        @endif
                        <span class="text-gray-500">vs prev</span>
                    </div>
                @else
                    <p class="text-xs text-gray-400 mt-2">No prior data</p>
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- Secondary KPI Row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $secondaryKpis = [
            ['label' => 'Unique Visitors', 'value' => $overview['uniqueVisitors'] ?? 0, 'prev' => $prevOverview['uniqueVisitors'] ?? 0],
            ['label' => 'Avg Engagement', 'value' => $overview['avgEngagement'] ?? 0, 'prev' => $prevOverview['avgEngagement'] ?? 0, 'decimal' => true],
            ['label' => 'Bounce Rate', 'value' => $bounceRate ?? 0, 'suffix' => '%', 'inverse' => true],
            ['label' => 'Buy Clicks', 'value' => collect($enhancedBlogPerformance ?? [])->sum('buy_clicks') + collect($enhancedPeptidePerformance ?? [])->sum('buy_clicks')],
        ];
    @endphp

    @foreach($secondaryKpis as $kpi)
        <div class="card p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">{{ $kpi['label'] }}</p>
            <p class="text-xl font-bold text-gray-900">
                {{ ($kpi['decimal'] ?? false) ? number_format($kpi['value'], 1) : $fmtNum($kpi['value']) }}{{ $kpi['suffix'] ?? '' }}
            </p>
            @isset($kpi['prev'])
                @php $d = $fmtDelta($kpi['value'], $kpi['prev']); @endphp
                @if($d['pct'] !== null)
                    <p class="text-xs mt-1 {{ ($kpi['inverse'] ?? false) ? ($d['pct'] >= 0 ? 'text-red-600' : 'text-green-600') : ($d['pct'] >= 0 ? 'text-green-600' : 'text-red-600') }}">
                        {{ $d['pct'] >= 0 ? '+' : '' }}{{ $d['pct'] }}% vs prev
                    </p>
                @endif
            @endisset
        </div>
    @endforeach
</div>

{{-- Daily Trend Chart --}}
<div class="card p-5 mb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="text-base font-semibold text-gray-900">Daily Trend</h3>
            <p class="text-xs text-gray-500">Sessions and page views over the selected period</p>
        </div>
        <span class="text-xs text-gray-400">{{ $period }}</span>
    </div>
    <div id="trend-chart"></div>
</div>

{{-- Three-column row: Top Pages, Funnel Segments, Quick Wins --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Top Performing Pages --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Top Pages This Period</h3>
            <a href="?tab=content" class="text-xs text-blue-600 hover:underline">View all ></a>
        </div>
        @php $pickPages = collect($topPages ?? [])->take(8); @endphp
        @if($pickPages->isNotEmpty())
            <div class="space-y-2">
                @foreach($pickPages as $i => $page)
                    @php
                        try { $path = parse_url($page['url'], PHP_URL_PATH) ?: $page['url']; } catch (\Throwable $e) { $path = $page['url']; }
                        $maxV = $pickPages->max('views') ?: 1;
                        $pct = $page['views'] / $maxV * 100;
                    @endphp
                    <div class="group">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <a href="{{ $page['url'] }}" target="_blank" class="text-xs text-gray-700 hover:text-blue-600 truncate min-w-0 flex-1" title="{{ $path }}">
                                <span class="text-gray-400 mr-1">{{ $i + 1 }}.</span>{{ $path }}
                            </a>
                            <span class="text-xs font-bold text-gray-900 shrink-0">{{ $fmtNum($page['views']) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1">
                            <div class="bg-blue-500 rounded-full h-1" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 italic text-center py-8">No page data yet</p>
        @endif
    </div>

    {{-- Funnel Segments --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Funnel Distribution</h3>
        @if(!empty($segments))
            @php $segTotal = array_sum($segments); @endphp
            <div class="space-y-4">
                @foreach(['TOF' => ['Top of Funnel', 'blue'], 'MOF' => ['Middle of Funnel', 'yellow'], 'BOF' => ['Bottom of Funnel', 'green']] as $key => [$label, $color])
                    @php
                        $count = $segments[$key] ?? 0;
                        $pct = $segTotal > 0 ? round($count / $segTotal * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="font-medium text-gray-700">{{ $label }}</span>
                            <span class="font-bold text-gray-900">{{ $fmtNum($count) }} <span class="text-gray-500 font-normal">({{ $pct }}%)</span></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-{{ $color }}-500 rounded-full h-2.5 transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500">
                Total tracked: {{ $fmtNum($segTotal) }} subscribers
            </div>
        @else
            <p class="text-sm text-gray-400 italic text-center py-8">No segment data</p>
        @endif
    </div>

    {{-- Top Events --}}
    <div class="card p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Top User Events</h3>
        @if(!empty($topEvents))
            @php
                $maxEvent = max($topEvents);
                $eventList = collect($topEvents)->take(10);
            @endphp
            <div class="space-y-2">
                @foreach($eventList as $event => $count)
                    @php $pct = $maxEvent > 0 ? ($count / $maxEvent) * 100 : 0; @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-0.5">
                            <span class="text-gray-700 font-mono truncate">{{ str_replace('_', ' ', $event) }}</span>
                            <span class="font-semibold text-gray-900">{{ $fmtNum($count) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1">
                            <div class="bg-purple-500 rounded-full h-1" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 italic text-center py-8">No events tracked</p>
        @endif
    </div>
</div>

{{-- Author + Category Performance Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Author Performance --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-700">Author Performance</h3>
                <p class="text-xs text-gray-500">Views and BioLinx click-through by author</p>
            </div>
        </div>
        @if(!empty($authorPerformance))
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="text-left py-2 px-2 text-xs font-semibold text-gray-500">Author</th>
                            <th class="text-right py-2 px-2 text-xs font-semibold text-gray-500 w-16">Posts</th>
                            <th class="text-right py-2 px-2 text-xs font-semibold text-gray-500 w-20">Views</th>
                            <th class="text-right py-2 px-2 text-xs font-semibold text-gray-500 w-16">Clicks</th>
                            <th class="text-right py-2 px-2 text-xs font-semibold text-gray-500 w-16">CTR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($authorPerformance as $author)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 px-2">
                                    <a href="{{ url('/author/'.$author['slug']) }}" target="_blank" class="text-blue-600 hover:underline font-medium">{{ $author['name'] }}</a>
                                </td>
                                <td class="py-2 px-2 text-right">{{ $author['posts'] }}</td>
                                <td class="py-2 px-2 text-right font-bold">{{ $fmtNum($author['views']) }}</td>
                                <td class="py-2 px-2 text-right text-cyan-700 font-semibold">{{ $author['clicks'] }}</td>
                                <td class="py-2 px-2 text-right">
                                    <span class="{{ $author['ctr'] >= 1 ? 'text-green-600 font-semibold' : 'text-gray-500' }}">{{ $author['ctr'] }}%</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-400 italic text-center py-8">No author data yet</p>
        @endif
    </div>

    {{-- Category Performance --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-700">Category Performance</h3>
                <p class="text-xs text-gray-500">Which content categories drive views and clicks</p>
            </div>
        </div>
        @if(!empty($categoryPerformance))
            <div class="space-y-2.5">
                @php $maxCatViews = collect($categoryPerformance)->max('views') ?: 1; @endphp
                @foreach(array_slice($categoryPerformance, 0, 8) as $cat)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-gray-700 truncate mr-2">{{ $cat['name'] }}</span>
                            <span class="shrink-0 text-gray-500">
                                <span class="font-bold text-gray-900">{{ $fmtNum($cat['views']) }}</span> views
                                <span class="text-gray-300">|</span>
                                <span class="font-bold text-cyan-700">{{ $cat['clicks'] }}</span> clicks
                                <span class="text-gray-300">|</span>
                                {{ $cat['posts'] }} posts
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-full h-2" style="width: {{ ($cat['views'] / $maxCatViews) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-400 italic text-center py-8">No category data yet</p>
        @endif
    </div>
</div>

{{-- Errors --}}
@if(($recentErrors ?? collect())->isNotEmpty())
<div class="card p-5">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <svg aria-hidden="true" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <h3 class="text-sm font-semibold text-gray-700">Recent Errors</h3>
        </div>
        <span class="text-xs text-gray-400">{{ $recentErrors->count() }} recent</span>
    </div>
    <div class="space-y-2 max-h-72 overflow-y-auto">
        @foreach($recentErrors as $error)
            <div class="text-xs p-3 bg-red-50 rounded-lg border border-red-100">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <span class="font-bold text-red-700">{{ $error->error_type ?? 'Error' }}</span>
                    <span class="text-red-400 shrink-0">{{ $error->created_at?->diffForHumans() }}</span>
                </div>
                <div class="text-red-600 font-mono text-xs">{{ \Illuminate\Support\Str::limit($error->message ?? '', 200) }}</div>
            </div>
        @endforeach
    </div>
</div>
@endif
