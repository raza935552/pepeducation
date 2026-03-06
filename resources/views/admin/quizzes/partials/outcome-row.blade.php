@php
    $conditions = $outcome->conditions ?? [];
    $condType = $conditions['type'] ?? null;
    $condSegment = $conditions['segment'] ?? null;
    $condMinScore = $conditions['min_score'] ?? 0;
    $condQuestion = $conditions['question'] ?? null;
    $condValue = $conditions['value'] ?? null;

    // Left border color by condition type
    $borderColor = match(true) {
        $condSegment === 'bof' => 'border-l-green-400',
        $condSegment === 'mof' => 'border-l-yellow-400',
        $condSegment === 'tof' => 'border-l-blue-400',
        $condType === 'answer' => 'border-l-purple-400',
        default => 'border-l-gray-300',
    };

    // Build friendly condition text
    $conditionText = 'Always shown (default)';
    if ($condType === 'segment' && $condSegment) {
        $segNames = ['tof' => 'Explorer (TOF)', 'mof' => 'Researcher (MOF)', 'bof' => 'Ready to Buy (BOF)'];
        $conditionText = 'Shows when segment is ' . ($segNames[$condSegment] ?? strtoupper($condSegment));
    } elseif ($condType === 'answer' && $condQuestion && $condValue) {
        $friendlyQuestion = \Str::of($condQuestion)->replace('_', ' ')->title()->toString();
        $friendlyValue = $condValue;
        // Look up friendly value label from health goals
        if ($condQuestion === 'health_goal') {
            $friendlyValue = \App\Models\ResultsBank::HEALTH_GOALS[$condValue] ?? $condValue;
        } else {
            // Try to find the slide and option label
            $refSlide = $quiz->questions->first(fn($q) => $q->klaviyo_property === $condQuestion);
            if ($refSlide && $refSlide->options) {
                $matchOpt = collect($refSlide->options)->first(fn($o) => ($o['klaviyo_value'] ?? $o['label'] ?? $o['value'] ?? '') === $condValue);
                if ($matchOpt) {
                    $friendlyValue = $matchOpt['label'] ?? $matchOpt['text'] ?? $condValue;
                }
            }
        }
        $conditionText = 'Shows when ' . $friendlyQuestion . ' = ' . $friendlyValue;
    } elseif ($condType === 'score' && $condMinScore > 0) {
        $conditionText = 'Shows when score ≥ ' . $condMinScore;
    }

    // Check for broken answer condition references
    $hasBrokenRef = false;
    $brokenRefMessage = '';
    if ($condType === 'answer' && $condQuestion) {
        $hasSlide = $quiz->questions->contains(fn($q) => $q->klaviyo_property === $condQuestion);
        if (!$hasSlide) {
            $hasBrokenRef = true;
            $brokenRefMessage = 'No slide has klaviyo_property "' . $condQuestion . '"';
        }
    }

    // Data for Alpine dispatch
    $outcomeData = [
        'id' => $outcome->id,
        'name' => $outcome->name,
        'conditions' => $conditions,
        'result_title' => $outcome->result_title,
        'result_message' => $outcome->result_message,
        'redirect_url' => $outcome->redirect_url,
    ];
@endphp
<div class="border border-l-4 {{ $borderColor }} rounded-lg bg-white hover:shadow-sm transition-shadow" data-outcome-id="{{ $outcome->id }}">
    <div class="p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3 flex-1 min-w-0">
                {{-- Drag handle --}}
                <span class="outcome-drag-handle flex-shrink-0 cursor-grab active:cursor-grabbing text-gray-300 hover:text-gray-500 mt-0.5">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 8zm0 6a2 2 0 10.001 4.001A2 2 0 007 14zm6-8a2 2 0 10-.001-4.001A2 2 0 0013 6zm0 2a2 2 0 10.001 4.001A2 2 0 0013 8zm0 6a2 2 0 10.001 4.001A2 2 0 0013 14z"/></svg>
                </span>

                <div class="flex-1 min-w-0">
                    {{-- Name + badges --}}
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-xs font-mono text-gray-400">#{{ $outcome->priority }}</span>
                        <span class="font-medium text-gray-900 text-sm">{{ $outcome->name }}</span>
                        @if(!$outcome->is_active)
                            <span class="px-1.5 py-0.5 text-[10px] rounded bg-gray-100 text-gray-500 border border-gray-200">Inactive</span>
                        @endif
                        @if($hasBrokenRef)
                            <span class="text-orange-500" title="{{ $brokenRefMessage }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </span>
                        @endif
                    </div>

                    {{-- Condition text (plain English) --}}
                    <p class="text-xs text-gray-500 mb-2">{{ $conditionText }}</p>

                    {{-- Mini preview card --}}
                    @if($outcome->result_title || $outcome->result_message)
                        <div class="rounded-lg border border-gray-100 bg-gray-50 p-2.5 mb-1">
                            @if($outcome->result_title)
                                <p class="text-xs font-semibold text-gray-800 mb-0.5">{{ $outcome->result_title }}</p>
                            @endif
                            @if($outcome->result_message)
                                <p class="text-[11px] text-gray-500 line-clamp-2">{{ Str::limit($outcome->result_message ?? '', 120) }}</p>
                            @endif
                            @if($outcome->recommended_peptide_id)
                                <p class="text-[10px] text-brand-gold mt-1">
                                    Recommends: {{ $outcome->recommendedPeptide?->name ?? 'Peptide #'.$outcome->recommended_peptide_id }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <span class="text-[10px] text-gray-400">Shown {{ number_format((int) ($outcome->shown_count ?? 0)) }} times</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1.5 flex-shrink-0">
                <button type="button"
                    @click="$dispatch('open-outcome-modal', {{ Js::from($outcomeData) }})"
                    class="p-1.5 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <form action="{{ route('admin.quizzes.outcomes.destroy', [$quiz, $outcome]) }}" method="POST" class="inline"
                    onsubmit="return confirm('Delete this outcome?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 rounded text-gray-400 hover:text-red-500 hover:bg-red-50" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
