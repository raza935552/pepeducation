<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Visitor Log</h1>
                <p class="text-sm text-gray-500 mt-0.5">Every visitor's entry link &amp; where they came from — saved for reference.</p>
            </div>
            <a href="{{ route('admin.visitor-log.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-admin-primary-600 text-white hover:bg-admin-primary-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                Export CSV
            </a>
        </div>
    </x-slot>

    <div class="space-y-5">
        {{-- Summary --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Visitors</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($total) }}</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">From Ads</div>
                <div class="mt-1 text-2xl font-bold text-admin-primary-600">{{ number_format($ad) }}</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase">Organic / Direct</div>
                <div class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($organic) }}</div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white rounded-xl border border-gray-200 p-4 flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Search (URL, referrer, campaign, IP)</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="e.g. facebook, 10-years, professorpeptides…"
                       class="w-full rounded-lg border-gray-300 text-sm focus:ring-admin-primary-500 focus:border-admin-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Source</label>
                <select name="source" class="rounded-lg border-gray-300 text-sm">
                    @foreach (['all'=>'All','ad'=>'Ads only','organic'=>'Organic/Direct'] as $v=>$l)
                        <option value="{{ $v }}" @selected($source===$v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Device</label>
                <select name="device" class="rounded-lg border-gray-300 text-sm">
                    @foreach (['all'=>'All','mobile'=>'Mobile','desktop'=>'Desktop','bot'=>'Bot'] as $v=>$l)
                        <option value="{{ $v }}" @selected($device===$v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Period</label>
                <select name="period" class="rounded-lg border-gray-300 text-sm">
                    @foreach (['1h'=>'1h','24h'=>'24h','7d'=>'7d','30d'=>'30d','all'=>'All'] as $v=>$l)
                        <option value="{{ $v }}" @selected($period===$v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-900 text-white hover:bg-gray-800">Filter</button>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-2.5 text-left font-semibold">When</th>
                            <th class="px-4 py-2.5 text-left font-semibold">Entry link</th>
                            <th class="px-4 py-2.5 text-left font-semibold">Came from</th>
                            <th class="px-4 py-2.5 text-left font-semibold">Source</th>
                            <th class="px-4 py-2.5 text-left font-semibold">Device</th>
                            <th class="px-4 py-2.5 text-left font-semibold">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($entries as $e)
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="px-4 py-2.5 text-gray-500 whitespace-nowrap">{{ optional($e->created_at)->diffForHumans() }}</td>
                                <td class="px-4 py-2.5 max-w-xl">
                                    <a href="{{ $e->landing_url }}" target="_blank" rel="noopener noreferrer"
                                       class="text-admin-primary-600 hover:underline break-all text-xs leading-snug block">{{ $e->landing_url }}</a>
                                </td>
                                <td class="px-4 py-2.5 text-gray-600 break-all max-w-[200px]">
                                    @if ($e->referrer_domain)
                                        {{ $e->referrer_domain }}
                                    @elseif ($e->is_ad)
                                        <span class="text-gray-700">{{ $e->utm_source ?: 'ad' }} <span class="text-gray-400">(ad click)</span></span>
                                    @else
                                        <span class="text-gray-400">direct / none</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5">
                                    @if ($e->is_ad)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-admin-primary-50 text-admin-primary-700">{{ $e->utm_source ?: 'ad' }}{{ $e->utm_campaign ? ' · '.$e->utm_campaign : '' }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">organic</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-gray-600">{{ $e->device }}</td>
                                <td class="px-4 py-2.5 text-gray-500 whitespace-nowrap">{{ $e->ip }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No visitors logged for this filter yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($entries->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $entries->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
