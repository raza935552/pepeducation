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
        'peptide_search' => 'bg-teal-100 text-teal-700 border-teal-200',
    ];
    $typeAbbrev = [
        'question' => 'Q', 'question_text' => 'Qt', 'intermission' => 'I',
        'loading' => 'L', 'email_capture' => 'E', 'peptide_reveal' => 'P',
        'vendor_reveal' => 'V', 'bridge' => 'B', 'peptide_search' => 'S',
    ];
    $badgeColor = $slideColors[$slideType] ?? 'bg-gray-200 text-gray-700 border-gray-300';
    $abbr = $typeAbbrev[$slideType] ?? '?';

    // Build JSON for edit modal
    $questionJson = json_encode([
        'slide_type' => $slideType,
        'question_text' => $question->question_text,
        'question_subtext' => $question->question_subtext,
        'question_type' => $question->question_type,
        'klaviyo_property' => $question->klaviyo_property,
        'is_required' => (bool) $question->is_required,
        'max_selections' => $question->max_selections,
        'settings' => $question->settings ?? [],
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

    $title = in_array($slideType, ['question', 'question_text'])
        ? $question->question_text
        : ($question->content_title ?: null);

    $optionCount = ($slideType === 'question' && $question->options) ? count($question->options) : 0;

    // Left border color by slide type
    $leftBorderColors = [
        'question'       => 'border-l-blue-300',
        'question_text'  => 'border-l-blue-300',
        'intermission'   => 'border-l-amber-300',
        'loading'        => 'border-l-purple-300',
        'email_capture'  => 'border-l-green-300',
        'peptide_reveal' => 'border-l-pink-300',
        'vendor_reveal'  => 'border-l-indigo-300',
        'bridge'         => 'border-l-orange-300',
        'peptide_search' => 'border-l-teal-300',
    ];
    $leftBorder = $leftBorderColors[$slideType] ?? 'border-l-gray-300';

    // Determine the next slide in order for routing indicator
    $allSlides = $quiz->questions->sortBy('order');
    $nextSlide = $allSlides->first(fn($s) => $s->order > $question->order);
    $hasSkipTo = $slideType === 'question' && $question->options && collect($question->options)->contains(fn($o) => !empty($o['skip_to_question']));

    // Hide redundant conditions: if slide is inside a segment phase (tof/mof/bof),
    // and the only condition is the branching question that routes to this phase,
    // suppress it — the phase tab already implies this.
    $phaseKey = $phaseKey ?? 'shared';
    $segmentDotColors = [
        'shared' => 'bg-gray-400',
        'tof' => 'bg-green-500',
        'mof' => 'bg-yellow-500',
        'bof' => 'bg-red-500',
    ];
    $segmentDot = $segmentDotColors[$phaseKey] ?? 'bg-gray-400';
    $segmentLabel = ['shared' => 'Shared', 'tof' => 'TOF', 'mof' => 'MOF', 'bof' => 'BOF'][$phaseKey] ?? 'Shared';
    $isRedundantCondition = false;
    if (in_array($phaseKey, ['tof', 'mof', 'bof']) && count($conditions) === 1 && $condType === 'and') {
        $isRedundantCondition = true;
    }

    // Check for broken references (3.4)
    $allSlideIds = $quiz->questions->pluck('id')->toArray();
    $brokenRefs = [];

    // Check show_conditions for references to non-existent slides
    foreach ($conditions as $cond) {
        $refId = $cond['question_id'] ?? null;
        if ($refId && !in_array($refId, $allSlideIds)) {
            $brokenRefs[] = 'Show condition references deleted slide #' . $refId;
        }
    }

    // Check skip_to_question values for non-existent slides
    if ($slideType === 'question' && $question->options) {
        foreach ($question->options as $option) {
            $skipTo = $option['skip_to_question'] ?? null;
            if ($skipTo && !in_array($skipTo, $allSlideIds)) {
                $optLabel = $option['label'] ?? $option['text'] ?? $option['value'] ?? 'Option';
                $brokenRefs[] = 'Option "' . Str::limit($optLabel, 20) . '" skips to deleted slide #' . $skipTo;
            }
        }
    }
@endphp

<div class="group border border-l-2 {{ $leftBorder }} rounded-lg bg-white hover:shadow-sm transition-shadow" data-question-id="{{ $question->id }}" data-slide-type="{{ $slideType }}" x-data="{ expanded: false }" id="slide-row-{{ $question->id }}">
    {{-- Compact row --}}
    <div class="flex items-center gap-3 px-3 py-2.5 cursor-pointer" @click="expanded = !expanded">
        {{-- Drag handle --}}
        <span class="drag-handle flex-shrink-0 cursor-grab active:cursor-grabbing text-gray-300 hover:text-gray-500" @click.stop>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 8zm0 6a2 2 0 10.001 4.001A2 2 0 007 14zm6-8a2 2 0 10-.001-4.001A2 2 0 0013 6zm0 2a2 2 0 10.001 4.001A2 2 0 0013 8zm0 6a2 2 0 10.001 4.001A2 2 0 0013 14z"/></svg>
        </span>
        {{-- Type badge --}}
        <span class="inline-flex items-center justify-center w-7 h-7 rounded-md text-xs font-bold border flex-shrink-0 {{ $badgeColor }}">{{ $abbr }}</span>

        {{-- Order + segment dot --}}
        <span class="text-xs font-mono text-gray-400 flex-shrink-0 w-6 text-right">#{{ $question->order }}</span>
        @if($quiz->type === 'segmentation')
            <span class="w-2 h-2 rounded-full {{ $segmentDot }} flex-shrink-0" title="{{ $segmentLabel }}" id="seg-dot-{{ $question->id }}"></span>
        @endif

        {{-- Title + meta --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                @if($title)
                    <span class="text-sm text-gray-900 truncate">{{ $title }}</span>
                @else
                    <span class="text-sm text-gray-400 italic">{{ $slideLabel }} slide</span>
                @endif
            </div>
        </div>

        {{-- Broken reference warning --}}
        @if(!empty($brokenRefs))
            <span class="flex-shrink-0 text-orange-500" title="{{ implode('; ', $brokenRefs) }}">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            </span>
        @endif

        {{-- Inline meta chips --}}
        <div class="hidden sm:flex items-center gap-1.5 flex-shrink-0">
            @if($question->klaviyo_property)
                <span class="px-1.5 py-0.5 text-[10px] rounded bg-purple-50 text-purple-600 border border-purple-200">{{ $question->klaviyo_property }}</span>
            @endif
            @if($optionCount > 0)
                <span class="text-[10px] text-gray-400">{{ $optionCount }} opts</span>
            @endif
            @if($slideType === 'loading' && $question->auto_advance_seconds)
                <span class="text-[10px] text-purple-500">{{ $question->auto_advance_seconds }}s</span>
            @endif
            {{-- Next-slide routing indicator --}}
            @if($hasSkipTo)
                <span class="text-[10px] text-yellow-600" title="Has conditional routing">&#8644;</span>
            @elseif($nextSlide)
                <span class="text-[10px] text-gray-400" title="Next: {{ Str::limit($nextSlide->question_text ?: $nextSlide->content_title ?: 'Slide #'.$nextSlide->order, 30) }}">&rarr; #{{ $nextSlide->order }}</span>
            @else
                <span class="text-[10px] text-green-500" title="Final slide">&#10003; End</span>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-0.5 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
            @if($quiz->type === 'segmentation')
                <div x-data="{ segOpen: false }" class="relative" @click.stop @click.away="segOpen = false">
                    <button type="button" @click="segOpen = !segOpen" class="p-1 rounded text-gray-400 hover:text-purple-600 hover:bg-purple-50" title="Assign to segment">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                    </button>
                    <div x-show="segOpen" x-transition class="absolute right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-20 py-1 w-36">
                        <button type="button" @click="assignSegment('{{ route('admin.quizzes.questions.segment', [$quiz, $question]) }}', 'shared', $el); segOpen = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-gray-50 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-gray-400"></span> Shared (all)
                        </button>
                        <button type="button" @click="assignSegment('{{ route('admin.quizzes.questions.segment', [$quiz, $question]) }}', 'tof', $el); segOpen = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-green-50 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> TOF Path
                        </button>
                        <button type="button" @click="assignSegment('{{ route('admin.quizzes.questions.segment', [$quiz, $question]) }}', 'mof', $el); segOpen = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-yellow-50 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span> MOF Path
                        </button>
                        <button type="button" @click="assignSegment('{{ route('admin.quizzes.questions.segment', [$quiz, $question]) }}', 'bof', $el); segOpen = false" class="w-full text-left px-3 py-1.5 text-xs hover:bg-red-50 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span> BOF Path
                        </button>
                    </div>
                </div>
            @endif
            <button type="button" onclick='event.stopPropagation(); editQuestion({{ $question->id }}, {!! e($questionJson) !!})' class="p-1 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100" title="Edit">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <button type="button" onclick="event.stopPropagation(); duplicateSlide('{{ route('admin.quizzes.questions.duplicate', [$quiz, $question]) }}', this)" class="p-1 rounded text-gray-400 hover:text-green-600 hover:bg-green-50" title="Duplicate slide (copies all conditions, scores & settings)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </button>
            <button type="button" onclick="event.stopPropagation(); deleteSlideWithCheck('{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}', {{ Js::from($question->question_text ?: $question->content_title ?: 'Slide #'.$question->order) }})" class="p-1 rounded text-gray-400 hover:text-red-500 hover:bg-red-50" title="Delete">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>

        {{-- Expand chevron --}}
        <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 text-gray-300 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </div>

    {{-- Expandable details --}}
    <div x-show="expanded" x-collapse class="border-t border-gray-100 px-3 py-3 bg-gray-50/50">
        {{-- Conditions (hidden when redundant with phase) --}}
        @if(!empty($conditionsText) && !$isRedundantCondition)
            <div class="flex items-start gap-1.5 mb-3">
                <span class="text-[10px] text-gray-400 mt-0.5 flex-shrink-0 uppercase tracking-wide">Show when</span>
                <span class="text-xs text-cyan-700 bg-cyan-50 px-2 py-0.5 rounded">
                    {{ implode(' ' . strtoupper($condType) . ' ', $conditionsText) }}
                </span>
            </div>
        @endif

        {{-- Options for choice questions --}}
        @if($slideType === 'question' && $question->options)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                @foreach($question->options as $option)
                    <div class="rounded border border-gray-200 bg-white px-2.5 py-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-medium text-gray-800 truncate">{{ $option['label'] ?? $option['text'] ?? $option['value'] ?? 'Option' }}</span>
                            @php $scoreLabel = $getScoreLabel($option); @endphp
                            @if($scoreLabel)
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 flex-shrink-0 whitespace-nowrap">{{ $scoreLabel }}</span>
                            @endif
                        </div>
                        @if(!empty($option['skip_to_question']))
                            <div class="mt-1 flex items-center gap-1">
                                <span class="text-[10px] text-gray-400">Jumps to:</span>
                                <span class="text-[10px] text-yellow-700 bg-yellow-50 px-1.5 py-0.5 rounded">
                                    {{ $slideLabels[$option['skip_to_question']] ?? 'Slide #'.$option['skip_to_question'] }}
                                </span>
                            </div>
                        @endif
                        @if(!empty($option['tags']))
                            <div class="mt-1 flex flex-wrap gap-0.5">
                                @foreach($option['tags'] as $tag)
                                    <span class="text-[9px] px-1 py-0.5 rounded bg-indigo-50 text-indigo-600">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Content preview for non-question slides --}}
        @if(in_array($slideType, ['intermission', 'loading', 'bridge']) && $question->content_body)
            <div class="rounded border border-gray-200 bg-white p-2.5">
                <p class="text-xs text-gray-600 whitespace-pre-line">{{ Str::limit($question->content_body, 200) }}</p>
                @if($question->content_source)
                    <p class="text-[10px] text-gray-400 mt-1 italic">Source: {{ $question->content_source }}</p>
                @endif
            </div>
        @endif

        {{-- CTA info --}}
        @if($question->cta_text)
            <div class="mt-2 flex items-center gap-2">
                <span class="text-[10px] text-gray-400 uppercase tracking-wide">CTA</span>
                <span class="text-xs px-2 py-0.5 rounded bg-brand-gold/10 text-brand-gold font-medium">{{ $question->cta_text }}</span>
                @if($question->cta_url)
                    <span class="text-xs text-gray-400">&rarr; {{ Str::limit($question->cta_url, 40) }}</span>
                @endif
            </div>
        @endif

        {{-- Auto-advance --}}
        @if($slideType === 'loading' && $question->auto_advance_seconds)
            <div class="mt-2">
                <span class="text-[10px] text-purple-600 bg-purple-50 px-2 py-0.5 rounded">Auto-advances in {{ $question->auto_advance_seconds }}s</span>
            </div>
        @endif

        {{-- Klaviyo property (mobile fallback) --}}
        @if($question->klaviyo_property)
            <div class="sm:hidden mt-2">
                <span class="px-1.5 py-0.5 text-[10px] rounded bg-purple-50 text-purple-600 border border-purple-200">{{ $question->klaviyo_property }}</span>
            </div>
        @endif
    </div>

    {{-- Insert after button --}}
    @php
        $insertSegArg = in_array($phaseKey, ['tof','mof','bof']) ? "'{$phaseKey}'" : 'null';
    @endphp
    <div class="relative h-0 group/insert">
        <button type="button"
            onclick="event.stopPropagation(); showAddQuestion({{ $insertSegArg }}, {{ $question->order }})"
            class="absolute left-1/2 -translate-x-1/2 -bottom-2.5 z-10 opacity-0 group-hover/insert:opacity-100 transition-opacity bg-white border border-dashed border-gray-300 hover:border-brand-gold text-gray-400 hover:text-brand-gold rounded-full w-5 h-5 flex items-center justify-center shadow-sm"
            title="Insert slide after #{{ $question->order }}">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </button>
    </div>
</div>
