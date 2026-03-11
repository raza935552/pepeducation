@php
    $activeOutcomes = $quiz->outcomes->where('is_active', true);

    // Segment coverage
    $segmentCoverage = [
        'tof' => $activeOutcomes->contains(fn($o) => ($o->conditions['type'] ?? '') === 'segment' && ($o->conditions['segment'] ?? '') === 'tof'),
        'mof' => $activeOutcomes->contains(fn($o) => ($o->conditions['type'] ?? '') === 'segment' && ($o->conditions['segment'] ?? '') === 'mof'),
        'bof' => $activeOutcomes->contains(fn($o) => ($o->conditions['type'] ?? '') === 'segment' && ($o->conditions['segment'] ?? '') === 'bof'),
    ];
    $segmentsCovered = count(array_filter($segmentCoverage));

    // Explicit Tailwind classes per segment (no dynamic string concatenation)
    $segmentStyles = [
        'tof' => ['covered' => 'bg-blue-50 border-blue-200 text-blue-700', 'missing' => 'bg-yellow-50 border-yellow-200 text-yellow-700'],
        'mof' => ['covered' => 'bg-yellow-50 border-yellow-200 text-yellow-700', 'missing' => 'bg-yellow-50 border-yellow-200 text-yellow-700'],
        'bof' => ['covered' => 'bg-green-50 border-green-200 text-green-700', 'missing' => 'bg-yellow-50 border-yellow-200 text-yellow-700'],
    ];
    $segmentNames = ['tof' => 'Explorer', 'mof' => 'Researcher', 'bof' => 'Ready to Buy'];

    // Health goal answer coverage
    $healthGoals = \App\Models\ResultsBank::allHealthGoals();
    $goalCoverage = [];
    foreach ($healthGoals as $goalKey => $goalLabel) {
        $goalCoverage[$goalKey] = [
            'label' => $goalLabel,
            'covered' => $activeOutcomes->contains(fn($o) =>
                ($o->conditions['type'] ?? '') === 'answer' &&
                ($o->conditions['question'] ?? '') === 'health_goal' &&
                ($o->conditions['value'] ?? '') === $goalKey
            ),
        ];
    }
    $goalsCovered = count(array_filter(array_column($goalCoverage, 'covered')));
@endphp

<div class="card p-4">
    <h3 class="text-sm font-semibold mb-3">Outcome Coverage</h3>

    {{-- Segment Coverage --}}
    <div class="mb-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-600">By Segment</span>
            <span class="text-[10px] {{ $segmentsCovered === 3 ? 'text-green-600' : 'text-yellow-600' }}">{{ $segmentsCovered }}/3 covered</span>
        </div>
        <div class="grid grid-cols-3 gap-2">
            @foreach(['tof', 'mof', 'bof'] as $seg)
                @php
                    $isCovered = $segmentCoverage[$seg];
                    $style = $segmentStyles[$seg][$isCovered ? 'covered' : 'missing'];
                @endphp
                <div class="rounded-lg p-2 text-center border {{ $style }}">
                    <span class="text-[10px] font-bold">{{ strtoupper($seg) }}</span>
                    @if($isCovered)
                        <div class="text-green-500 mt-0.5">
                            <svg class="w-3.5 h-3.5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    @else
                        <button type="button"
                            @click="$dispatch('open-outcome-modal', { conditionMode: 'segment', segment: '{{ $seg }}' })"
                            class="text-[10px] hover:underline mt-0.5 block mx-auto">+ Add</button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Health Goal Coverage --}}
    <div>
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-600">By Health Goal</span>
            <span class="text-[10px] {{ $goalsCovered === count($healthGoals) ? 'text-green-600' : 'text-yellow-600' }}">{{ $goalsCovered }}/{{ count($healthGoals) }} covered</span>
        </div>
        <div class="space-y-1">
            @foreach($goalCoverage as $goalKey => $goal)
                <div class="flex items-center justify-between px-2 py-1.5 rounded {{ $goal['covered'] ? 'bg-green-50' : 'bg-gray-50' }}">
                    <span class="text-[11px] text-gray-700">{{ $goal['label'] }}</span>
                    @if($goal['covered'])
                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <button type="button"
                            @click="$dispatch('open-outcome-modal', { conditionMode: 'answer', answerQuestion: 'health_goal', answerValue: '{{ $goalKey }}' })"
                            class="text-[10px] text-brand-gold hover:underline">+ Add</button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
