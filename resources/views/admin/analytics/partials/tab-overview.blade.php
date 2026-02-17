{{-- Overview Tab: KPIs with delta, segments, top events, daily trend, errors --}}

{{-- KPI Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    @php
        $kpis = [
            ['key' => 'sessions', 'label' => 'Sessions', 'icon' => 'users', 'format' => 'number'],
            ['key' => 'uniqueVisitors', 'label' => 'Unique Visitors', 'icon' => 'user', 'format' => 'number'],
            ['key' => 'pageViews', 'label' => 'Page Views', 'icon' => 'eye', 'format' => 'number'],
            ['key' => 'avgEngagement', 'label' => 'Avg Engagement', 'icon' => 'star', 'format' => 'decimal'],
            ['key' => 'newSubscribers', 'label' => 'Subscribers', 'icon' => 'mail', 'format' => 'number'],
            ['key' => 'conversions', 'label' => 'Conversions', 'icon' => 'check', 'format' => 'number'],
        ];
    @endphp

    @foreach($kpis as $kpi)
        @php
            $current = $overview[$kpi['key']] ?? 0;
            $previous = $prevOverview[$kpi['key']] ?? 0;
            $delta = $previous > 0 ? round(($current - $previous) / $previous * 100, 1) : ($current > 0 ? 100 : 0);
        @endphp
        <div class="card p-4">
            <div class="flex items-center gap-3 mb-2">
                @php
                    $icons = [
                        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
                        'user' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                        'eye' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>',
                        'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
                        'mail' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                        'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    ];
                @endphp
                <div class="w-10 h-10 rounded-lg bg-brand-gold/10 flex items-center justify-center">
                    <svg aria-hidden="true" class="w-5 h-5 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $icons[$kpi['icon']] !!}
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xl font-bold">{{ number_format($current) }}</div>
                    <div class="text-xs text-gray-500">{{ $kpi['label'] }}</div>
                </div>
            </div>
            @if($delta != 0)
                <div class="flex items-center gap-1 text-xs {{ $delta > 0 ? 'text-green-600' : 'text-red-500' }}">
                    @if($delta > 0)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    @endif
                    <span>{{ abs($delta) }}% vs previous period</span>
                </div>
            @else
                <div class="text-xs text-gray-400">No change</div>
            @endif
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Funnel Segments --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Funnel Segments</h3>
        @if(!empty($segments))
            @php $segTotal = array_sum($segments); @endphp
            <div class="space-y-3">
                @foreach(['TOF' => 'Top of Funnel', 'MOF' => 'Middle of Funnel', 'BOF' => 'Bottom of Funnel'] as $key => $label)
                    @php
                        $count = $segments[$key] ?? 0;
                        $pct = $segTotal > 0 ? round($count / $segTotal * 100) : 0;
                        $colors = ['TOF' => 'bg-blue-500', 'MOF' => 'bg-yellow-500', 'BOF' => 'bg-green-500'];
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $label }}</span>
                            <span class="font-medium">{{ number_format($count) }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $colors[$key] }} rounded-full h-2" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No segment data</p>
        @endif
    </div>

    {{-- Top Events --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Events</h3>
        @if(!empty($topEvents))
            <div class="space-y-2">
                @foreach($topEvents as $event => $count)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 truncate mr-2">{{ str_replace('_', ' ', $event) }}</span>
                        <span class="font-medium text-gray-900 whitespace-nowrap">{{ number_format($count) }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No events tracked</p>
        @endif
    </div>

    {{-- Recent Errors --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Recent Errors</h3>
        @if($recentErrors->isNotEmpty())
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($recentErrors as $error)
                    <div class="text-xs p-2 bg-red-50 rounded border border-red-100">
                        <div class="font-medium text-red-700 truncate">{{ $error->error_type ?? 'Error' }}</div>
                        <div class="text-red-600 truncate mt-0.5">{{ $error->message ?? '' }}</div>
                        <div class="text-red-400 mt-0.5">{{ $error->created_at?->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-green-600 text-sm text-center py-4">No recent errors</p>
        @endif
    </div>
</div>

{{-- Daily Trend Chart --}}
<div class="card p-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Daily Trend</h3>
    <div id="trend-chart"></div>
</div>
