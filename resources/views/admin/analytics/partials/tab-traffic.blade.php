{{-- Traffic Tab: Sources, UTM, devices, browsers, OS, new vs returning, landing pages --}}

{{-- New vs Returning + Device Split --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- New vs Returning --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">New vs Returning Visitors</h3>
        @if(($newVsReturning['total'] ?? 0) > 0)
            <div class="flex items-center gap-4 mb-3">
                <div class="flex-1">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($newVsReturning['new']) }}</div>
                    <div class="text-xs text-gray-500">New ({{ $newVsReturning['newPct'] }}%)</div>
                </div>
                <div class="flex-1">
                    <div class="text-2xl font-bold text-brand-gold">{{ number_format($newVsReturning['returning']) }}</div>
                    <div class="text-xs text-gray-500">Returning ({{ $newVsReturning['returningPct'] }}%)</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 flex overflow-hidden">
                <div class="bg-blue-500 h-3" style="width: {{ $newVsReturning['newPct'] }}%"></div>
                <div class="bg-brand-gold h-3" style="width: {{ $newVsReturning['returningPct'] }}%"></div>
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No visitor data</p>
        @endif
    </div>

    {{-- Device Breakdown --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Device Breakdown</h3>
        <div id="device-chart"></div>
    </div>
</div>

{{-- Traffic Sources + Browser/OS --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Traffic Sources --}}
    <div class="card p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Traffic Sources</h3>
        <div id="sources-chart"></div>
    </div>

    {{-- Browser & OS side by side --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="card p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Browsers</h3>
            <div id="browser-chart"></div>
        </div>
        <div class="card p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Operating Systems</h3>
            <div id="os-chart"></div>
        </div>
    </div>
</div>

{{-- UTM Campaigns --}}
<div class="card p-4 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">UTM Campaigns</h3>
    @if(!empty($utmCampaigns))
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Source</th>
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Medium</th>
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Campaign</th>
                        <th class="text-right py-2 px-2 text-gray-500 font-medium">Sessions</th>
                        <th class="text-right py-2 px-2 text-gray-500 font-medium">Conversions</th>
                        <th class="text-right py-2 px-2 text-gray-500 font-medium">CVR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($utmCampaigns as $utm)
                        <tr class="border-b border-gray-100">
                            <td class="py-2 px-2 font-medium">{{ $utm['source'] }}</td>
                            <td class="py-2 px-2 text-gray-600">{{ $utm['medium'] }}</td>
                            <td class="py-2 px-2 text-gray-600 truncate max-w-[150px]">{{ $utm['campaign'] }}</td>
                            <td class="py-2 px-2 text-right">{{ number_format($utm['sessions']) }}</td>
                            <td class="py-2 px-2 text-right">{{ number_format($utm['conversions']) }}</td>
                            <td class="py-2 px-2 text-right {{ $utm['cvr'] > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">{{ $utm['cvr'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 text-sm text-center py-4">No UTM campaigns tracked</p>
    @endif
</div>

{{-- Top Landing Pages --}}
<div class="card p-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Landing Pages</h3>
    @if(!empty($topLandingPages))
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">URL</th>
                        <th class="text-right py-2 px-2 text-gray-500 font-medium">Sessions</th>
                        <th class="text-right py-2 px-2 text-gray-500 font-medium">Bounce Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topLandingPages as $page)
                        <tr class="border-b border-gray-100">
                            <td class="py-2 px-2 text-gray-700 truncate max-w-[300px]" title="{{ $page['url'] }}">
                                @php try { $path = parse_url($page['url'], PHP_URL_PATH) ?: $page['url']; } catch (\Throwable $e) { $path = $page['url']; } @endphp
                                {{ $path }}
                            </td>
                            <td class="py-2 px-2 text-right font-medium">{{ number_format($page['sessions']) }}</td>
                            <td class="py-2 px-2 text-right {{ $page['bounceRate'] > 70 ? 'text-red-500' : ($page['bounceRate'] > 50 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $page['bounceRate'] }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 text-sm text-center py-4">No landing page data</p>
    @endif
</div>
