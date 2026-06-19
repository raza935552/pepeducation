<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Content → Revenue</h1>
                <p class="text-sm text-gray-500 mt-0.5">Which landers drive actual Biolinx revenue — views → clicks → orders → $, across all traffic. Revenue/view is the "what sells" signal.</p>
            </div>
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                @foreach (['today' => 'Today', '7d' => '7d', '30d' => '30d', '90d' => '90d', 'all' => 'All'] as $val => $label)
                    <a href="{{ route('admin.content-revenue', ['period' => $val]) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $period === $val ? 'bg-white text-admin-primary-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <p class="text-[11px] text-gray-400">🕒 All times Eastern (ET). Revenue is mirrored from Biolinx orders attributed to each lander; figures are directional while volume is low.</p>

        {{-- Totals --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @php($cards = [
                ['Lander Views', number_format($totals['views']), 'all traffic'],
                ['CTA Clicks → Biolinx', number_format($totals['clicks']), $totals['views'] ? round($totals['clicks']/$totals['views']*100,1).'% CTR' : '—'],
                ['Orders', number_format($totals['orders']), 'attributed'],
                ['Revenue', '$'.number_format($totals['revenue'], 2), 'from landers'],
            ])
            @foreach($cards as [$label, $val, $sub])
                <div class="bg-white rounded-xl border border-gray-200 p-4">
                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $label }}</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">{{ $val }}</div>
                    <div class="text-[11px] text-gray-400 mt-0.5">{{ $sub }}</div>
                </div>
            @endforeach
        </div>

        {{-- Per-lander table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="text-left px-4 py-2">Lander</th>
                        <th class="text-right px-3">Views</th>
                        <th class="text-right px-3">Clicks</th>
                        <th class="text-right px-3">CTR</th>
                        <th class="text-right px-3">Orders</th>
                        <th class="text-right px-3">Rev/View</th>
                        <th class="text-right px-4">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rows as $r)
                        <tr class="{{ $r['revenue'] > 0 ? 'bg-green-50/40' : '' }}">
                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-900">{{ $r['title'] }}</div>
                                <div class="text-[11px] text-gray-400">/lp/{{ $r['slug'] }}@if($r['ad_views']) · {{ number_format($r['ad_views']) }} from ads @endif</div>
                            </td>
                            <td class="px-3 text-right">{{ number_format($r['views']) }}</td>
                            <td class="px-3 text-right">{{ number_format($r['clicks']) }}</td>
                            <td class="px-3 text-right text-gray-500">{{ $r['ctr'] }}%</td>
                            <td class="px-3 text-right">{{ number_format($r['orders']) }}</td>
                            <td class="px-3 text-right {{ $r['rev_per_view'] > 0 ? 'font-semibold text-gray-800' : 'text-gray-400' }}">${{ number_format($r['rev_per_view'], 2) }}</td>
                            <td class="px-4 text-right font-semibold {{ $r['revenue'] > 0 ? 'text-green-700' : 'text-gray-400' }}">${{ number_format($r['revenue'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">No lander data in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="text-[11px] text-gray-400">Blog-post / calculator → revenue attribution (multi-touch) is a separate, fuzzier build — this view covers landers, which carry the direct CTA to Biolinx.</p>
    </div>
</x-admin-layout>
