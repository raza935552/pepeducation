{{-- Reusable visits/clicks/CTR breakdown table. Expects: $title, $colLabel, $rows, $empty --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-2.5 text-left font-semibold">{{ $colLabel }}</th>
                    <th class="px-5 py-2.5 text-right font-semibold">Ad Visits</th>
                    <th class="px-5 py-2.5 text-right font-semibold">Clicks</th>
                    <th class="px-5 py-2.5 text-right font-semibold">CTR</th>
                    <th class="px-5 py-2.5 text-right font-semibold">Orders</th>
                    <th class="px-5 py-2.5 text-right font-semibold">Revenue</th>
                    <th class="px-5 py-2.5 text-right font-semibold">CVR</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($rows as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-2.5 font-medium text-gray-900">{{ $row['key'] }}</td>
                        <td class="px-5 py-2.5 text-right text-gray-700">{{ number_format($row['visits']) }}</td>
                        <td class="px-5 py-2.5 text-right text-gray-700">{{ number_format($row['clicks']) }}</td>
                        <td class="px-5 py-2.5 text-right">
                            @if (is_null($row['ctr']))
                                <span class="text-gray-400">—</span>
                            @else
                                <span class="font-semibold {{ $row['ctr'] >= 40 ? 'text-green-600' : ($row['ctr'] >= 15 ? 'text-amber-600' : 'text-gray-700') }}">{{ $row['ctr'] }}%</span>
                            @endif
                        </td>
                        <td class="px-5 py-2.5 text-right text-gray-700">{{ number_format($row['orders'] ?? 0) }}</td>
                        <td class="px-5 py-2.5 text-right font-semibold {{ ($row['revenue'] ?? 0) > 0 ? 'text-green-700' : 'text-gray-400' }}">${{ number_format($row['revenue'] ?? 0, 2) }}</td>
                        <td class="px-5 py-2.5 text-right">
                            @if (is_null($row['cvr'] ?? 0))
                                <span class="text-gray-400">—</span>
                            @else
                                <span class="text-gray-700">{{ $row['cvr'] ?? 0 }}%</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-6 text-center text-gray-400">{{ $empty }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
