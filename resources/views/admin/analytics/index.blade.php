<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Analytics Dashboard</span>
            <form class="flex gap-2">
                <select name="period" onchange="this.form.submit()"
                    class="rounded-lg border-gray-300 text-sm focus:border-brand-gold focus:ring-brand-gold">
                    <option value="24h" {{ $period === '24h' ? 'selected' : '' }}>Last 24 Hours</option>
                    <option value="7d" {{ $period === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30d" {{ $period === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90d" {{ $period === '90d' ? 'selected' : '' }}>Last 90 Days</option>
                </select>
            </form>
        </div>
    </x-slot>

    <!-- Overview Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        @include('admin.analytics.partials.stat-card', ['label' => 'Sessions', 'value' => number_format($overview['sessions']), 'icon' => 'users'])
        @include('admin.analytics.partials.stat-card', ['label' => 'Unique Visitors', 'value' => number_format($overview['uniqueVisitors']), 'icon' => 'user'])
        @include('admin.analytics.partials.stat-card', ['label' => 'Page Views', 'value' => number_format($overview['pageViews']), 'icon' => 'eye'])
        @include('admin.analytics.partials.stat-card', ['label' => 'Avg Engagement', 'value' => $overview['avgEngagement'], 'icon' => 'star'])
        @include('admin.analytics.partials.stat-card', ['label' => 'New Subscribers', 'value' => number_format($overview['newSubscribers']), 'icon' => 'mail'])
        @include('admin.analytics.partials.stat-card', ['label' => 'Conversions', 'value' => number_format($overview['conversions']), 'icon' => 'check'])
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Segment Distribution -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Funnel Segments</h3>
            @if(count($segments) > 0)
                <div class="space-y-3">
                    @foreach(['TOF' => 'Top of Funnel', 'MOF' => 'Middle of Funnel', 'BOF' => 'Bottom of Funnel'] as $key => $label)
                        @php $count = $segments[$key] ?? 0; $total = array_sum($segments); $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ $label }}</span>
                                <span class="font-medium">{{ number_format($count) }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $key === 'TOF' ? 'bg-blue-500' : ($key === 'MOF' ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No segment data yet</p>
            @endif
        </div>

        <!-- Top Events -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Top Events</h3>
            @if(count($topEvents) > 0)
                <div class="space-y-2">
                    @foreach($topEvents as $event => $count)
                        <div class="flex justify-between items-center py-1 border-b border-gray-100 last:border-0">
                            <span class="text-sm font-mono">{{ $event }}</span>
                            <span class="text-sm font-medium">{{ number_format($count) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No events tracked yet</p>
            @endif
        </div>
    </div>

    <!-- CTA Performance -->
    <div class="card p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">CTA Click Performance</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-brand-gold">{{ number_format($ctaStats['total']) }}</div>
                <div class="text-xs text-gray-500">Total Clicks</div>
            </div>
            @foreach($ctaStats['byType'] as $type => $count)
                @if($loop->index < 3)
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-500">{{ number_format($count) }}</div>
                    <div class="text-xs text-gray-500 capitalize">{{ $type }}</div>
                </div>
                @endif
            @endforeach
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Top CTAs -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Top CTAs</h4>
                @if(count($ctaStats['topCTAs']) > 0)
                    <div class="space-y-1">
                        @foreach(array_slice($ctaStats['topCTAs'], 0, 5) as $cta)
                            <div class="flex justify-between text-sm py-1 border-b border-gray-100 last:border-0">
                                <span class="truncate" title="{{ $cta['name'] }}">{{ Str::limit($cta['name'], 25) }}</span>
                                <span class="font-medium ml-2">{{ $cta['clicks'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-xs">No CTA clicks yet</p>
                @endif
            </div>
            <!-- By Position -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">By Position</h4>
                @if(count($ctaStats['byPosition']) > 0)
                    <div class="space-y-1">
                        @foreach($ctaStats['byPosition'] as $pos => $count)
                            <div class="flex justify-between text-sm py-1 border-b border-gray-100 last:border-0">
                                <span class="capitalize">{{ $pos }}</span>
                                <span class="font-medium">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-xs">No data</p>
                @endif
            </div>
            <!-- By Source Page -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Top Source Pages</h4>
                @if(count($ctaStats['bySourcePage']) > 0)
                    <div class="space-y-1">
                        @foreach(array_slice($ctaStats['bySourcePage'], 0, 5, true) as $page => $count)
                            <div class="flex justify-between text-sm py-1 border-b border-gray-100 last:border-0">
                                <span class="truncate" title="{{ $page }}">{{ Str::limit(parse_url($page, PHP_URL_PATH) ?: $page, 20) }}</span>
                                <span class="font-medium ml-2">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-xs">No data</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Quiz Stats -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Quiz Performance</h3>
            <div class="flex justify-between mb-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-brand-gold">{{ number_format($quizStats['total']) }}</div>
                    <div class="text-xs text-gray-500">Started</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-500">{{ number_format($quizStats['completed']) }}</div>
                    <div class="text-xs text-gray-500">Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $quizStats['total'] > 0 ? round($quizStats['completed'] / $quizStats['total'] * 100) : 0 }}%</div>
                    <div class="text-xs text-gray-500">Rate</div>
                </div>
            </div>
            @if(count($quizStats['byQuiz']) > 0)
                <div class="border-t pt-3 space-y-2">
                    @foreach($quizStats['byQuiz'] as $quiz => $count)
                        <div class="flex justify-between text-sm">
                            <span class="truncate">{{ $quiz }}</span>
                            <span class="font-medium">{{ number_format($count) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Popup Stats -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Popup Performance</h3>
            <div class="flex justify-between mb-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-500">{{ number_format($popupStats['impressions']) }}</div>
                    <div class="text-xs text-gray-500">Impressions</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-500">{{ number_format($popupStats['conversions']) }}</div>
                    <div class="text-xs text-gray-500">Conversions</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $popupStats['impressions'] > 0 ? round($popupStats['conversions'] / $popupStats['impressions'] * 100, 1) : 0 }}%</div>
                    <div class="text-xs text-gray-500">CVR</div>
                </div>
            </div>
            @if(count($popupStats['byPopup']) > 0)
                <div class="border-t pt-3 space-y-2">
                    @foreach($popupStats['byPopup'] as $popup => $count)
                        <div class="flex justify-between text-sm">
                            <span class="truncate">{{ $popup }}</span>
                            <span class="font-medium">{{ number_format($count) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Lead Magnet Stats -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Lead Magnet Downloads</h3>
            @if(count($leadMagnetStats) > 0)
                <div class="space-y-2">
                    @foreach($leadMagnetStats as $magnet => $count)
                        <div class="flex justify-between items-center py-1 border-b border-gray-100 last:border-0">
                            <span class="text-sm truncate">{{ $magnet }}</span>
                            <span class="text-sm font-medium">{{ number_format($count) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No downloads yet</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Outbound Links -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Top Outbound Links</h3>
            @if(count($outboundStats) > 0)
                <div class="space-y-2">
                    @foreach($outboundStats as $link => $count)
                        <div class="flex justify-between items-center py-1 border-b border-gray-100 last:border-0">
                            <span class="text-sm">{{ $link }}</span>
                            <span class="text-sm font-medium">{{ number_format($count) }} clicks</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No outbound clicks yet</p>
            @endif
        </div>

        <!-- Recent Errors -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Errors</h3>
            @if($recentErrors->count() > 0)
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($recentErrors as $error)
                        <div class="p-2 bg-red-50 rounded text-sm">
                            <div class="font-medium text-red-800 truncate">{{ $error->message }}</div>
                            <div class="text-xs text-red-600">{{ $error->url }} - {{ $error->created_at->diffForHumans() }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No errors logged</p>
            @endif
        </div>
    </div>

    <!-- Daily Trend Chart -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold mb-4">Daily Trend</h3>
        <div class="h-64" id="trend-chart"></div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const chartData = @json($dailyTrend);
        if (chartData.labels.length > 0) {
            new ApexCharts(document.getElementById('trend-chart'), {
                chart: { type: 'area', height: 250, toolbar: { show: false } },
                series: [
                    { name: 'Sessions', data: chartData.sessions },
                    { name: 'Page Views', data: chartData.pageViews }
                ],
                xaxis: { categories: chartData.labels, labels: { rotate: -45 } },
                colors: ['#D4A35A', '#3B82F6'],
                stroke: { curve: 'smooth', width: 2 },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.1 } },
                legend: { position: 'top' }
            }).render();
        } else {
            document.getElementById('trend-chart').innerHTML = '<p class="text-gray-500 text-center py-8">No data for this period</p>';
        }
    </script>
    @endpush
</x-admin-layout>
