<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Full Funnel — PP → Biolinx</h1>
                <p class="text-sm text-gray-500 mt-0.5">The whole closed loop: lander view → CTA click → Biolinx visit → add-to-cart → checkout → order. See exactly where it leaks.</p>
            </div>
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                @foreach (['today' => 'Today', '7d' => '7d', '30d' => '30d', '90d' => '90d', 'all' => 'All'] as $val => $label)
                    <a href="{{ route('admin.funnel', ['period' => $val]) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $period === $val ? 'bg-white text-admin-primary-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @unless($bridgeOk)
            <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 text-sm">
                ⚠️ Couldn't reach Biolinx just now — showing PP-side steps only (views &amp; clicks). The Biolinx steps will fill in on the next load.
            </div>
        @endunless

        <p class="text-[11px] text-gray-400">🕒 All times Eastern (ET). PP steps from this site; Biolinx steps fetched live from Biolinx (cached ~2 min). Test traffic excluded.</p>

        {{-- Funnel steps --}}
        @php($top = $steps[0]['value'] ?: 0)
        <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-3">
            @foreach($steps as $i => $s)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-900">{{ $s['label'] }}</span>
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded {{ $s['site'] === 'PP' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $s['site'] }}</span>
                        </div>
                        <div class="text-gray-500">
                            <span class="font-semibold text-gray-900">{{ number_format($s['value']) }}</span>
                            <span class="text-xs">({{ $s['pct_of_top'] }}% of top)</span>
                            @if($s['step_conv'] !== null)
                                <span class="text-xs {{ $s['step_conv'] < 50 ? 'text-red-500' : 'text-gray-400' }}">· {{ $s['step_conv'] }}% from prev</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-100 rounded-full h-5 overflow-hidden">
                        <div class="{{ $s['site'] === 'PP' ? 'bg-indigo-500' : 'bg-emerald-500' }} h-5 rounded-full transition-all"
                             style="width: {{ $top ? max(1, round($s['value'] / $top * 100)) : 0 }}%"></div>
                    </div>
                </div>
            @endforeach
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <span class="text-sm font-semibold text-gray-900">Revenue</span>
                <span class="text-xl font-bold text-green-700">${{ number_format($revenue, 2) }}</span>
            </div>
        </div>

        {{-- Per-lander breakdown --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100"><h2 class="font-semibold text-gray-900">By lander</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="text-left px-4 py-2">Lander</th>
                            <th class="text-right px-3">Views</th>
                            <th class="text-right px-3">Clicks</th>
                            <th class="text-right px-3">BX Visits</th>
                            <th class="text-right px-3">ATC</th>
                            <th class="text-right px-3">Checkout</th>
                            <th class="text-right px-3">Orders</th>
                            <th class="text-right px-4">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rows as $r)
                            <tr class="{{ $r['revenue'] > 0 ? 'bg-green-50/40' : '' }}">
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-900">{{ $r['title'] }}</div>
                                    <div class="text-[11px] text-gray-400">/lp/{{ $r['slug'] }}</div>
                                </td>
                                <td class="px-3 text-right">{{ number_format($r['views']) }}</td>
                                <td class="px-3 text-right">{{ number_format($r['clicks']) }}</td>
                                <td class="px-3 text-right">{{ number_format($r['bx_visits']) }}</td>
                                <td class="px-3 text-right">{{ number_format($r['atc']) }}</td>
                                <td class="px-3 text-right">{{ number_format($r['checkout']) }}</td>
                                <td class="px-3 text-right">{{ number_format($r['orders']) }}</td>
                                <td class="px-4 text-right font-semibold {{ $r['revenue'] > 0 ? 'text-green-700' : 'text-gray-400' }}">${{ number_format($r['revenue'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-6 text-center text-gray-400">No funnel data in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
