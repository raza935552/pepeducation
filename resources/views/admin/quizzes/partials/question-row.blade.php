@php
    $slideType = $question->slide_type ?? 'question';
    $slideLabel = \App\Models\QuizQuestion::getSlideTypeLabel($slideType);
    $slideColors = [
        'question' => 'bg-blue-100 text-blue-700 border-blue-200',
        'question_text' => 'bg-blue-100 text-blue-700 border-blue-200',
        'intermission' => 'bg-amber-100 text-amber-700 border-amber-200',
        'loading' => 'bg-purple-100 text-purple-700 border-purple-200',
        'email_capture' => 'bg-green-100 text-green-700 border-green-200',
        'peptide_reveal' => 'bg-pink-100 text-pink-700 border-pink-200',
        'vendor_reveal' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
        'bridge' => 'bg-orange-100 text-orange-700 border-orange-200',
    ];
    $badgeColor = $slideColors[$slideType] ?? 'bg-gray-200 text-gray-700 border-gray-300';

    // Build JSON for edit modal
    $questionJson = json_encode([
        'slide_type' => $slideType,
        'question_text' => $question->question_text,
        'question_type' => $question->question_type,
        'klaviyo_property' => $question->klaviyo_property,
        'options' => $question->options ?? [],
        'content_title' => $question->content_title,
        'content_body' => $question->content_body,
        'content_source' => $question->content_source,
        'auto_advance_seconds' => $question->auto_advance_seconds ?? 5,
        'cta_text' => $question->cta_text,
        'cta_url' => $question->cta_url,
        'dynamic_content_key' => $question->dynamic_content_key,
        'dynamic_content_map' => $question->dynamic_content_map ?? [],
        'show_conditions' => $question->show_conditions,
    ]);

    // Parse conditions into human-readable text
    $conditionsText = [];
    $conditions = $question->show_conditions['conditions'] ?? [];
    $condType = $question->show_conditions['type'] ?? 'and';
    foreach ($conditions as $cond) {
        $refSlide = $quiz->questions->firstWhere('id', $cond['question_id'] ?? null);
        $refLabel = $refSlide ? Str::limit($refSlide->question_text ?: $refSlide->content_title ?: 'Slide #'.$refSlide->order, 30) : 'Unknown slide';
        $optionLabel = $cond['option_value'] ?? '?';
        if ($refSlide && $refSlide->options) {
            $matchedOpt = collect($refSlide->options)->firstWhere('value', $cond['option_value'] ?? '');
            if ($matchedOpt) {
                $optionLabel = Str::limit($matchedOpt['label'] ?? $matchedOpt['text'] ?? $optionLabel, 25);
            }
        }
        $conditionsText[] = '"' . $optionLabel . '" on "' . $refLabel . '"';
    }

    // Determine max score direction for each option
    $getScoreLabel = function($option) {
        $tof = $option['score_tof'] ?? 0;
        $mof = $option['score_mof'] ?? 0;
        $bof = $option['score_bof'] ?? 0;
        if ($tof === 0 && $mof === 0 && $bof === 0) return null;
        $max = max($tof, $mof, $bof);
        $labels = [];
        if ($tof === $max && $tof > 0) $labels[] = "TOF (+{$tof})";
        if ($mof === $max && $mof > 0) $labels[] = "MOF (+{$mof})";
        if ($bof === $max && $bof > 0) $labels[] = "BOF (+{$bof})";
        return 'Leans ' . implode(' / ', $labels);
    };
@endphp

<div class="border rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition-shadow" data-question-id="{{ $question->id }}" x-data="{ expanded: false }">
    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="text-xs font-mono text-gray-400">#{{ $question->order }}</span>
                <span class="px-2 py-0.5 text-xs rounded-full font-medium border {{ $badgeColor }}">{{ $slideLabel }}</span>
                @if($question->klaviyo_property)
                    <span class="px-2 py-0.5 text-xs rounded bg-purple-50 text-purple-600 border border-purple-200">{{ $question->klaviyo_property }}</span>
                @endif
            </div>

            {{-- Title --}}
            @if(in_array($slideType, ['question', 'question_text']))
                <p class="text-gray-900 font-medium">{{ $question->question_text }}</p>
            @elseif($question->content_title)
                <p class="text-gray-900 font-medium">{{ $question->content_title }}</p>
            @else
                <p class="text-gray-500 italic">{{ $slideLabel }} slide</p>
            @endif

            {{-- Conditions (plain English) --}}
            @if(!empty($conditionsText))
                <div class="mt-2 flex items-start gap-1.5">
                    <span class="text-xs text-gray-400 mt-0.5 flex-shrink-0">Visible when:</span>
                    <span class="text-xs text-cyan-700 bg-cyan-50 px-2 py-0.5 rounded">
                        {{ implode(' ' . strtoupper($condType) . ' ', $conditionsText) }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-1 ml-4 flex-shrink-0">
            <button type="button" @click="expanded = !expanded" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100" :title="expanded ? 'Collapse' : 'Expand'">
                <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <button type="button" onclick='editQuestion({{ $question->id }}, {!! e($questionJson) !!})' class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100" title="Edit">
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <form action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this slide?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50" title="Delete">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Expandable Content --}}
    <div x-show="expanded" x-collapse class="mt-3">
        {{-- Options for choice questions --}}
        @if($slideType === 'question' && $question->options)
            <div class="space-y-2">
                @foreach($question->options as $option)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-800">{{ $option['label'] ?? $option['text'] ?? $option['value'] ?? 'Option' }}</span>
                            @php $scoreLabel = $getScoreLabel($option); @endphp
                            @if($scoreLabel)
                                <span class="text-xs px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">{{ $scoreLabel }}</span>
                            @endif
                        </div>
                        @if(!empty($option['skip_to_question']))
                            <div class="mt-1 flex items-center gap-1">
                                <span class="text-xs text-gray-400">Jumps to:</span>
                                <span class="text-xs text-yellow-700 bg-yellow-50 px-1.5 py-0.5 rounded">
                                    {{ $slideLabels[$option['skip_to_question']] ?? 'Slide #'.$option['skip_to_question'] }}
                                </span>
                            </div>
                        @endif
                        @if(!empty($option['tags']))
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach($option['tags'] as $tag)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-600">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Content preview for non-question slides --}}
        @if(in_array($slideType, ['intermission', 'loading', 'bridge']) && $question->content_body)
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ Str::limit($question->content_body, 200) }}</p>
                @if($question->content_source)
                    <p class="text-xs text-gray-400 mt-1 italic">Source: {{ $question->content_source }}</p>
                @endif
            </div>
        @endif

        {{-- CTA info --}}
        @if($question->cta_text)
            <div class="mt-2 flex items-center gap-2">
                <span class="text-xs text-gray-400">CTA:</span>
                <span class="text-xs px-2 py-0.5 rounded bg-brand-gold/10 text-brand-gold font-medium">{{ $question->cta_text }}</span>
                @if($question->cta_url)
                    <span class="text-xs text-gray-400">→ {{ Str::limit($question->cta_url, 40) }}</span>
                @endif
            </div>
        @endif

        {{-- Auto-advance --}}
        @if($slideType === 'loading' && $question->auto_advance_seconds)
            <div class="mt-2">
                <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded">Auto-advances in {{ $question->auto_advance_seconds }}s</span>
            </div>
        @endif
    </div>
</div>
