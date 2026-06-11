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
            <strong>The full funnel:</strong> ad visit → click → <strong>order → revenue</strong>. Only <em>ad traffic</em> is shown (visits carrying a Meta <code>fbclid</code> or <code>utm_source</code>). Orders &amp; revenue are mirrored from Biolinx by the conversion bridge (updates every ~15 min). Visit logging began when this dashboard was installed, so older periods read low until data accrues.
        </div>

        {{-- Funnel summary cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ad Visits</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($totalVisits) }}</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Clicks</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($totalClicksAll) }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">{{ number_format($totalClicks) }} from ads · CTR {{ $overallCtr }}%</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Orders</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($totalOrders) }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">CVR {{ $overallCvr }}%</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Revenue</div>
                <div class="mt-1 text-2xl font-bold text-green-700">${{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">AOV</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">${{ number_format($aov, 2) }}</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Unique</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($uniqueVisitors) }}</div>
                <div class="text-[11px] text-gray-400 mt-0.5">visitors</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 col-span-2">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Emails Captured (from ads)</div>
                <div class="mt-1 flex items-end gap-5">
                    <div>
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($totalOptins) }}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">ad-lander opt-ins</div>
                    </div>
                    <div class="pl-5 border-l border-gray-100">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($totalAdEmails) }}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">fbclid-verified</div>
                    </div>
                </div>
            </div>
        </div>

        @unless($hasRevenue)
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs text-amber-800">
                No orders mirrored from Biolinx yet — the conversion bridge populates revenue as ad-driven orders come in (or after the first <code>pp:push-conversions</code> sync runs on Biolinx).
            </div>
        @endunless

        {{-- Per-lander (with email captures) --}}
        @include('admin.ad-analytics._table', [
            'title' => 'By Lander',
            'colLabel' => 'Lander',
            'rows' => $perLander,
            'empty' => 'No ad visits recorded yet for this period.',
            'showEmails' => true,
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

        {{-- Campaign → Ad drilldown --}}
        @include('admin.ad-analytics._drilldown', ['drilldown' => $drilldown])

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
