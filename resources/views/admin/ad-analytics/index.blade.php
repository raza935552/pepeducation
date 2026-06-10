<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Ad Analytics</h1>
                <p class="text-sm text-gray-500 mt-0.5">Paid-traffic performance for the bridge landers — visits, click-throughs to Biolinx, and CTR.</p>
            </div>
            {{-- Period filter --}}
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                @foreach (['1h' => '1h', '24h' => '24h', '7d' => '7d', '30d' => '30d', 'all' => 'All'] as $val => $label)
                    <a href="{{ route('admin.ad-analytics', ['period' => $val]) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $period === $val ? 'bg-white text-admin-primary-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Context banner --}}
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
            <strong>How to read this:</strong> only <em>ad traffic</em> is shown (visits carrying a Meta <code>fbclid</code> or <code>utm_source</code>). Visit logging began when this dashboard was installed, so older periods read low until data accrues. <strong>Conversions/revenue are tracked on Biolinx</strong> (order → Attribution panel, by lander) — PP can’t see the store database, so this page is top-of-funnel: visits → clicks → CTR.
        </div>

        {{-- Summary cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ad Visits</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalVisits) }}</div>
                <div class="text-xs text-gray-400 mt-1">lander loads from ads</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Unique Visitors</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($uniqueVisitors) }}</div>
                <div class="text-xs text-gray-400 mt-1">distinct sessions</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Clicks → Biolinx</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalClicks) }}</div>
                <div class="text-xs text-gray-400 mt-1">CTA clicks to the store</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">CTR</div>
                <div class="mt-2 text-3xl font-bold text-admin-primary-600">{{ $overallCtr }}%</div>
                <div class="text-xs text-gray-400 mt-1">clicks ÷ visits</div>
            </div>
        </div>

        {{-- Per-lander --}}
        @include('admin.ad-analytics._table', [
            'title' => 'By Lander',
            'colLabel' => 'Lander',
            'rows' => $perLander,
            'empty' => 'No ad visits recorded yet for this period.',
        ])

        {{-- Per-campaign --}}
        @include('admin.ad-analytics._table', [
            'title' => 'By Campaign',
            'colLabel' => 'Campaign (utm_campaign)',
            'rows' => $perCampaign,
            'empty' => 'No campaign data yet — appears once ads with utm_campaign drive traffic.',
        ])

        {{-- Per-ad --}}
        @include('admin.ad-analytics._table', [
            'title' => 'By Ad',
            'colLabel' => 'Ad (utm_content)',
            'rows' => $perAd,
            'empty' => 'No ad-level data yet.',
        ])

        {{-- Recent ad activity --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900">Recent Ad Visits</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-5 py-2.5 text-left font-semibold">When</th>
                            <th class="px-5 py-2.5 text-left font-semibold">Lander</th>
                            <th class="px-5 py-2.5 text-left font-semibold">Source</th>
                            <th class="px-5 py-2.5 text-left font-semibold">Campaign</th>
                            <th class="px-5 py-2.5 text-left font-semibold">Ad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($recent as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-2.5 text-gray-500 whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($r->created_at)->diffForHumans() }}</td>
                                <td class="px-5 py-2.5 font-medium text-gray-900">{{ $r->lander_slug }}</td>
                                <td class="px-5 py-2.5 text-gray-600">{{ $r->utm_source ?: '—' }}</td>
                                <td class="px-5 py-2.5 text-gray-600">{{ $r->utm_campaign ?: '—' }}</td>
                                <td class="px-5 py-2.5 text-gray-600">{{ $r->utm_content ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-6 text-center text-gray-400">No ad visits yet in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-admin-layout>
