<x-admin-layout>
    <x-slot name="header">A/B Test — Your page (A) vs AI page (B)</x-slot>

    <div class="mb-5 flex items-center justify-between flex-wrap gap-3">
        <p class="text-sm text-gray-500 max-w-2xl">
            Server-side 50/50 split on the live ad landers. <b>A</b> = your page, <b>B</b> = the AI-built page.
            Primary metric is <b>CTR to Biolinx</b> (the click that leads to a sale); email capture is the tiebreaker.
            Purchases lag and are low-volume in a week, so CTR decides.
        </p>
        <form method="GET" class="flex items-center gap-2 text-sm">
            <label class="text-gray-600">Window</label>
            <select name="days" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm">
                @foreach([1,3,7,14,30] as $d)
                    <option value="{{ $d }}" @selected($days === $d)>Last {{ $d }} {{ \Illuminate\Support\Str::plural('day', $d) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @foreach($rows as $r)
        @php
            $A = $r['variants']['A']; $B = $r['variants']['B'];
            $splitOk = $r['total_visits'] > 0
                ? abs(($A['visits'] / max(1,$r['total_visits'])) - 0.5) <= 0.1
                : true;
            $fmtPct = fn ($x) => number_format($x * 100, 2) . '%';
        @endphp
        <div class="card p-6 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $r['name'] }}</h3>
                    <a href="https://professorpeptides.co/lp/{{ $r['slug'] }}" target="_blank" class="text-xs text-blue-600 hover:underline">/lp/{{ $r['slug'] }}</a>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    @if(! $r['has_b'])
                        <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600">No B template</span>
                    @elseif($r['enabled'])
                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">● Test LIVE (50/50)</span>
                    @else
                        <span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 font-semibold">Test OFF — preview only</span>
                    @endif
                    <a href="https://professorpeptides.co/lp/{{ $r['slug'] }}?v=a" target="_blank" class="px-2 py-1 rounded-lg border border-gray-300 hover:bg-gray-50">Preview A</a>
                    <a href="https://professorpeptides.co/lp/{{ $r['slug'] }}?v=b" target="_blank" class="px-2 py-1 rounded-lg border border-gray-300 hover:bg-gray-50">Preview B</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-4">Variant</th>
                            <th class="py-2 pr-4 text-right">Visits</th>
                            <th class="py-2 pr-4 text-right">Clicks → Biolinx</th>
                            <th class="py-2 pr-4 text-right">CTR</th>
                            <th class="py-2 pr-4 text-right">Emails</th>
                            <th class="py-2 pr-4 text-right">Email rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['A' => 'A — your page', 'B' => 'B — AI page'] as $key => $label)
                            @php $d = $r['variants'][$key]; $isLeader = $r['ctr_test']['enough'] && $r['ctr_test']['leader'] === $key; @endphp
                            <tr class="border-b {{ $isLeader ? 'bg-green-50' : '' }}">
                                <td class="py-2 pr-4 font-medium text-gray-800">{{ $label }} @if($isLeader)<span class="text-green-600">▲</span>@endif</td>
                                <td class="py-2 pr-4 text-right">{{ number_format($d['visits']) }}</td>
                                <td class="py-2 pr-4 text-right">{{ number_format($d['clicks']) }}</td>
                                <td class="py-2 pr-4 text-right font-semibold">{{ $fmtPct($d['ctr']) }}</td>
                                <td class="py-2 pr-4 text-right">{{ number_format($d['emails']) }}</td>
                                <td class="py-2 pr-4 text-right">{{ $fmtPct($d['email_rate']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex items-center justify-between flex-wrap gap-3">
                <div class="text-sm">
                    <span class="font-semibold text-gray-700">Verdict:</span>
                    <span class="{{ \Illuminate\Support\Str::contains($r['ctr_test']['verdict'], 'winning') ? 'text-green-700 font-semibold' : 'text-gray-600' }}">
                        {{ $r['ctr_test']['verdict'] }}
                    </span>
                    @if($r['ctr_test']['enough'])
                        <span class="text-gray-400">· z = {{ number_format($r['ctr_test']['z'], 2) }} · {{ $r['ctr_test']['confidence'] }}</span>
                    @endif
                </div>
                <div class="text-xs {{ $splitOk ? 'text-gray-400' : 'text-amber-600' }}">
                    Split check: {{ $r['total_visits'] > 0 ? round($A['visits'] / max(1,$r['total_visits']) * 100) : 0 }}% A / {{ $r['total_visits'] > 0 ? round($B['visits'] / max(1,$r['total_visits']) * 100) : 0 }}% B
                    {!! $splitOk ? '' : '<b>(uneven — check)</b>' !!}
                </div>
            </div>
        </div>
    @endforeach

    <p class="text-xs text-gray-400 mt-2">
        Internal/test traffic should be excluded before final calls. Conversions (purchases) are tracked at the lander level via the Biolinx bridge and are typically too few in a week to be decisive — CTR is the call.
    </p>
</x-admin-layout>
