<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="text-gray-400 hover:text-gray-600">
                    <svg aria-hidden="true" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <span>Analytics: {{ $quiz->name }}</span>
            </div>
            <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-secondary text-sm">Back to Editor</a>
        </div>
    </x-slot>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <div class="text-2xl font-bold">{{ number_format($totalStarted) }}</div>
            <div class="text-sm text-gray-500">Started</div>
        </div>
        <div class="card p-4">
            <div class="text-2xl font-bold text-green-600">{{ number_format($totalCompleted) }}</div>
            <div class="text-sm text-gray-500">Completed</div>
        </div>
        <div class="card p-4">
            <div class="text-2xl font-bold">{{ $completionRate }}%</div>
            <div class="text-sm text-gray-500">Completion Rate</div>
        </div>
        <div class="card p-4">
            <div class="text-2xl font-bold">{{ $avgDuration ? gmdate('i:s', (int) $avgDuration) : '--' }}</div>
            <div class="text-sm text-gray-500">Avg Duration</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Question Drop-off Funnel --}}
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Question Drop-off</h3>
            @if(count($dropoff) > 0 && $totalStarted > 0)
                <div class="space-y-3">
                    @foreach($dropoff as $i => $step)
                        @php
                            $width = $totalStarted > 0 ? max(round($step['answered'] / $totalStarted * 100), 4) : 4;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-700 truncate mr-2" title="{{ $step['label'] }}">
                                    Q{{ $step['order'] }}: {{ $step['label'] }}
                                </span>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-sm font-medium">{{ $step['answered'] }}</span>
                                    @if($step['dropoff_pct'] > 0)
                                        <span class="text-xs text-red-500">-{{ $step['dropoff_pct'] }}%</span>
                                    @endif
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-4">
                                <div class="bg-brand-gold rounded-full h-4 transition-all" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm text-center py-8">No response data yet</p>
            @endif
        </div>

        {{-- Outcome Distribution --}}
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Outcome Distribution</h3>
            @if($outcomeDistribution->sum('count') > 0)
                @php $maxOutcome = $outcomeDistribution->max('count'); @endphp
                <div class="space-y-3">
                    @foreach($outcomeDistribution as $outcome)
                        @php
                            $barWidth = $maxOutcome > 0 ? max(round($outcome['count'] / $maxOutcome * 100), 4) : 4;
                            $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-orange-500', 'bg-pink-500'];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-700 truncate mr-2">{{ $outcome['name'] }}</span>
                                <span class="text-sm font-medium shrink-0">{{ $outcome['count'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-4">
                                <div class="{{ $colors[$loop->index % count($colors)] }} rounded-full h-4 transition-all" style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm text-center py-8">No outcomes shown yet</p>
            @endif
        </div>
    </div>

    {{-- Segment Breakdown --}}
    @if(!empty($segmentBreakdown))
        <div class="card p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Segment Breakdown</h3>
            <div class="flex gap-4">
                @php
                    $segmentTotal = array_sum($segmentBreakdown);
                    $segmentColors = ['tof' => 'bg-blue-500', 'mof' => 'bg-yellow-500', 'bof' => 'bg-green-500'];
                    $segmentLabels = ['tof' => 'TOF (Explorer)', 'mof' => 'MOF (Researcher)', 'bof' => 'BOF (Ready)'];
                @endphp
                @foreach(['tof', 'mof', 'bof'] as $seg)
                    @php $count = $segmentBreakdown[$seg] ?? 0; @endphp
                    <div class="flex-1 text-center">
                        <div class="text-2xl font-bold">{{ $count }}</div>
                        <div class="text-sm text-gray-500">{{ $segmentLabels[$seg] }}</div>
                        <div class="text-xs text-gray-400">{{ $segmentTotal > 0 ? round($count / $segmentTotal * 100) : 0 }}%</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Recent Responses Table --}}
    <div class="card p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Responses ({{ $recentResponses->count() }})</h3>
        @if($recentResponses->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-3 font-medium text-gray-600">Status</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-600">Segment</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-600">Outcome</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-600">Score</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-600">Duration</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-600">Email</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-600">UTM Source</th>
                            <th class="text-left py-2 px-3 font-medium text-gray-600">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentResponses as $response)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 px-3">
                                    @if($response->status === 'completed')
                                        <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    @if($response->segment)
                                        <span class="inline-block px-2 py-0.5 text-xs rounded-full
                                            {{ $response->segment === 'bof' ? 'bg-green-100 text-green-800' :
                                               ($response->segment === 'mof' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ strtoupper($response->segment) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 max-w-[150px] truncate">{{ $response->outcome_name ?? '-' }}</td>
                                <td class="py-2 px-3">{{ $response->total_score ?? '-' }}</td>
                                <td class="py-2 px-3">{{ $response->duration_seconds ? gmdate('i:s', $response->duration_seconds) : '-' }}</td>
                                <td class="py-2 px-3 max-w-[150px] truncate">{{ $response->email ?? '-' }}</td>
                                <td class="py-2 px-3">{{ $response->utm_source ?? '-' }}</td>
                                <td class="py-2 px-3 whitespace-nowrap text-gray-500">{{ $response->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-8">No responses yet</p>
        @endif
    </div>
</x-admin-layout>
