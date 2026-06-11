{{-- Campaign → Ad drilldown. Each campaign expands to the specific ads (utm_content) inside it.
     Expects $drilldown: array of campaigns, each with totals + ['ads'=>[...]] (see buildDrilldown()). --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">Campaign → Ad Drilldown</h3>
        <p class="text-xs text-gray-400 mt-0.5">Click a campaign to expand the specific ads (utm_content) inside it. Sorted by revenue, then visits.</p>
    </div>

    @forelse ($drilldown as $c)
        <details class="border-b border-gray-100 last:border-0" {{ $loop->first ? 'open' : '' }}>
            <summary class="flex flex-wrap items-center gap-x-5 gap-y-1 px-5 py-3 cursor-pointer hover:bg-gray-50 select-none list-none">
                <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="font-semibold text-gray-900 flex-1 min-w-0 break-all">{{ $c['campaign'] }}</span>
                <span class="text-xs text-gray-500 whitespace-nowrap">{{ number_format($c['visits']) }} visits</span>
                <span class="text-xs text-gray-500 whitespace-nowrap">{{ number_format($c['clicks']) }} ad clicks</span>
                <span class="text-xs text-gray-500 whitespace-nowrap">{{ number_format($c['orders']) }} orders</span>
                <span class="text-xs font-semibold whitespace-nowrap {{ $c['revenue'] > 0 ? 'text-green-700' : 'text-gray-400' }}">${{ number_format($c['revenue'], 2) }}</span>
                <span class="text-[11px] text-gray-400 whitespace-nowrap">{{ count($c['ads']) }} {{ \Illuminate\Support\Str::plural('ad', count($c['ads'])) }}</span>
            </summary>
            <div class="overflow-x-auto bg-gray-50/60 border-t border-gray-100">
                <table class="min-w-full text-sm">
                    <thead class="text-xs uppercase tracking-wide text-gray-400">
                        <tr>
                            <th class="px-5 py-2 pl-12 text-left font-semibold">Ad (utm_content)</th>
                            <th class="px-5 py-2 text-right font-semibold">Ad Visits</th>
                            <th class="px-5 py-2 text-right font-semibold">Ad Clicks</th>
                            <th class="px-5 py-2 text-right font-semibold">CTR</th>
                            <th class="px-5 py-2 text-right font-semibold">Orders</th>
                            <th class="px-5 py-2 text-right font-semibold">Revenue</th>
                            <th class="px-5 py-2 text-right font-semibold">CVR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($c['ads'] as $ad)
                            <tr class="hover:bg-white">
                                <td class="px-5 py-2 pl-12 text-gray-800 break-all max-w-md">{{ $ad['key'] }}</td>
                                <td class="px-5 py-2 text-right text-gray-600">{{ number_format($ad['visits']) }}</td>
                                <td class="px-5 py-2 text-right text-gray-600">{{ number_format($ad['clicks']) }}</td>
                                <td class="px-5 py-2 text-right">
                                    @if (is_null($ad['ctr']))
                                        <span class="text-gray-400">—</span>
                                    @else
                                        <span class="{{ $ad['ctr'] >= 40 ? 'text-green-600 font-semibold' : ($ad['ctr'] >= 15 ? 'text-amber-600' : 'text-gray-600') }}">{{ $ad['ctr'] }}%</span>
                                    @endif
                                </td>
                                <td class="px-5 py-2 text-right text-gray-600">{{ number_format($ad['orders']) }}</td>
                                <td class="px-5 py-2 text-right {{ $ad['revenue'] > 0 ? 'text-green-700 font-semibold' : 'text-gray-400' }}">${{ number_format($ad['revenue'], 2) }}</td>
                                <td class="px-5 py-2 text-right">
                                    @if (is_null($ad['cvr']))
                                        <span class="text-gray-400">—</span>
                                    @else
                                        <span class="text-gray-600">{{ $ad['cvr'] }}%</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
    @empty
        <div class="px-5 py-6 text-center text-gray-400 text-sm">No campaign / ad data in this period yet.</div>
    @endforelse
</div>
