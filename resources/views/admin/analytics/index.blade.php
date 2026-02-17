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

    <div x-data="{ tab: 'overview' }">
        {{-- Tab Navigation --}}
        <div class="flex gap-1 mb-6 border-b border-gray-200 overflow-x-auto">
            @foreach(['overview' => 'Overview', 'traffic' => 'Traffic', 'content' => 'Content', 'funnels' => 'Funnels', 'engagement' => 'Engagement'] as $key => $label)
                <button @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'border-brand-gold text-brand-gold font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2.5 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Tab Content --}}
        <div x-show="tab === 'overview'" x-cloak>
            @include('admin.analytics.partials.tab-overview')
        </div>
        <div x-show="tab === 'traffic'" x-cloak>
            @include('admin.analytics.partials.tab-traffic')
        </div>
        <div x-show="tab === 'content'" x-cloak>
            @include('admin.analytics.partials.tab-content')
        </div>
        <div x-show="tab === 'funnels'" x-cloak>
            @include('admin.analytics.partials.tab-funnels')
        </div>
        <div x-show="tab === 'engagement'" x-cloak>
            @include('admin.analytics.partials.tab-engagement')
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const chartColors = {
            gold: '#D4A35A',
            blue: '#3B82F6',
            green: '#10B981',
            red: '#EF4444',
            purple: '#8B5CF6',
            orange: '#F59E0B',
            gray: '#6B7280',
        };

        function renderChart(el, options) {
            if (!el) return;
            return new ApexCharts(el, options).render();
        }

        function showNoData(el) {
            if (!el) return;
            el.textContent = '';
            const p = document.createElement('p');
            p.className = 'text-gray-500 text-center py-8 text-sm';
            p.textContent = 'No data for this period';
            el.appendChild(p);
        }

        // ─── Overview: Daily Trend ───
        const trendData = @json($dailyTrend);
        if (trendData.labels.length > 0) {
            renderChart(document.getElementById('trend-chart'), {
                chart: { type: 'area', height: 250, toolbar: { show: false } },
                series: [
                    { name: 'Sessions', data: trendData.sessions },
                    { name: 'Page Views', data: trendData.pageViews },
                ],
                xaxis: { categories: trendData.labels, labels: { rotate: -45 } },
                colors: [chartColors.gold, chartColors.blue],
                stroke: { curve: 'smooth', width: 2 },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.1 } },
                legend: { position: 'top' },
            });
        } else {
            showNoData(document.getElementById('trend-chart'));
        }

        // ─── Traffic: Sources Bar ───
        const sourcesData = @json($trafficSources);
        if (Object.keys(sourcesData).length > 0) {
            renderChart(document.getElementById('sources-chart'), {
                chart: { type: 'bar', height: 250, toolbar: { show: false } },
                series: [{ name: 'Sessions', data: Object.values(sourcesData) }],
                xaxis: { categories: Object.keys(sourcesData) },
                colors: [chartColors.gold],
                plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
            });
        } else {
            showNoData(document.getElementById('sources-chart'));
        }

        // ─── Traffic: Devices Donut ───
        const deviceData = @json($deviceBreakdown);
        if (Object.keys(deviceData).length > 0) {
            renderChart(document.getElementById('device-chart'), {
                chart: { type: 'donut', height: 220 },
                series: Object.values(deviceData),
                labels: Object.keys(deviceData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                colors: [chartColors.blue, chartColors.gold, chartColors.green],
                legend: { position: 'bottom' },
            });
        } else {
            showNoData(document.getElementById('device-chart'));
        }

        // ─── Traffic: Browser Donut ───
        const browserData = @json($browserBreakdown);
        if (Object.keys(browserData).length > 0) {
            renderChart(document.getElementById('browser-chart'), {
                chart: { type: 'donut', height: 220 },
                series: Object.values(browserData),
                labels: Object.keys(browserData),
                colors: [chartColors.blue, chartColors.green, chartColors.orange, chartColors.purple, chartColors.gray],
                legend: { position: 'bottom' },
            });
        } else {
            showNoData(document.getElementById('browser-chart'));
        }

        // ─── Traffic: OS Donut ───
        const osData = @json($osBreakdown);
        if (Object.keys(osData).length > 0) {
            renderChart(document.getElementById('os-chart'), {
                chart: { type: 'donut', height: 220 },
                series: Object.values(osData),
                labels: Object.keys(osData),
                colors: [chartColors.blue, chartColors.gold, chartColors.green, chartColors.red, chartColors.purple],
                legend: { position: 'bottom' },
            });
        } else {
            showNoData(document.getElementById('os-chart'));
        }

        // ─── Content: Scroll Depth ───
        const scrollData = @json($scrollDepth);
        if (Object.values(scrollData).some(v => v > 0)) {
            renderChart(document.getElementById('scroll-chart'), {
                chart: { type: 'bar', height: 220, toolbar: { show: false } },
                series: [{ name: 'Events', data: Object.values(scrollData) }],
                xaxis: { categories: Object.keys(scrollData) },
                colors: [chartColors.blue],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
            });
        } else {
            showNoData(document.getElementById('scroll-chart'));
        }

        // ─── Content: Avg Time on Page ───
        const timeData = @json($avgTimeOnPage);
        if (timeData.length > 0) {
            renderChart(document.getElementById('time-chart'), {
                chart: { type: 'bar', height: 250, toolbar: { show: false } },
                series: [{ name: 'Avg Seconds', data: timeData.map(r => r.avgTime) }],
                xaxis: { categories: timeData.map(r => { try { return new URL(r.url).pathname; } catch(e) { return r.url; } }) },
                colors: [chartColors.green],
                plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
            });
        } else {
            showNoData(document.getElementById('time-chart'));
        }

        // ─── Engagement: Score Distribution ───
        const engDistData = @json($engagementDist);
        if (Object.values(engDistData).some(v => v > 0)) {
            renderChart(document.getElementById('eng-dist-chart'), {
                chart: { type: 'bar', height: 220, toolbar: { show: false } },
                series: [{ name: 'Sessions', data: Object.values(engDistData) }],
                xaxis: { categories: Object.keys(engDistData) },
                colors: [chartColors.gold],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
            });
        } else {
            showNoData(document.getElementById('eng-dist-chart'));
        }

        // ─── Engagement: Tier Trend ───
        const tierData = @json($engagementTierTrend);
        if (tierData.labels.length > 0) {
            renderChart(document.getElementById('tier-chart'), {
                chart: { type: 'area', height: 250, stacked: true, toolbar: { show: false } },
                series: [
                    { name: 'Hot (40+)', data: tierData.hot },
                    { name: 'Warm (15-39)', data: tierData.warm },
                    { name: 'Cold (<15)', data: tierData.cold },
                ],
                xaxis: { categories: tierData.labels, labels: { rotate: -45 } },
                colors: [chartColors.red, chartColors.orange, chartColors.blue],
                stroke: { curve: 'smooth', width: 1 },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.2 } },
                legend: { position: 'top' },
            });
        } else {
            showNoData(document.getElementById('tier-chart'));
        }
    </script>
    @endpush
</x-admin-layout>
