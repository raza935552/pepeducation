@php
    $slideType = $question->slide_type ?? 'question';
    $slideLabel = \App\Models\QuizQuestion::getSlideTypeLabel($slideType);
    $slideColors = [
        'question' => 'bg-blue-100 text-blue-700',
        'question_text' => 'bg-blue-100 text-blue-700',
        'intermission' => 'bg-amber-100 text-amber-700',
        'loading' => 'bg-purple-100 text-purple-700',
        'email_capture' => 'bg-green-100 text-green-700',
        'peptide_reveal' => 'bg-pink-100 text-pink-700',
        'vendor_reveal' => 'bg-indigo-100 text-indigo-700',
        'bridge' => 'bg-orange-100 text-orange-700',
    ];
    $badgeColor = $slideColors[$slideType] ?? 'bg-gray-200 text-gray-700';

    // Build JSON data for the edit modal
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

    // Branching indicators
    $hasSkipTo = collect($question->options ?? [])->contains(fn ($o) => !empty($o['skip_to_question']));
    $hasConditions = !empty($question->show_conditions['conditions'] ?? []);
    $conditionType = $question->show_conditions['type'] ?? 'and';
@endphp

<div class="border rounded-lg p-4 bg-gray-50" data-question-id="{{ $question->id }}">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-sm font-medium text-gray-500">#{{ $question->order }}</span>
                <span class="px-2 py-0.5 text-xs rounded font-medium {{ $badgeColor }}">{{ $slideLabel }}</span>
                @if($slideType === 'question')
                    <span class="px-2 py-0.5 text-xs rounded bg-gray-200 text-gray-700">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                @endif
                @if($question->klaviyo_property)
                    <span class="px-2 py-0.5 text-xs rounded bg-purple-100 text-purple-700">{{ $question->klaviyo_property }}</span>
                @endif
                @if($slideType === 'loading' && $question->auto_advance_seconds)
                    <span class="px-2 py-0.5 text-xs rounded bg-gray-200 text-gray-600">{{ $question->auto_advance_seconds }}s</span>
                @endif
                @if($hasSkipTo)
                    <span class="px-2 py-0.5 text-xs rounded bg-yellow-100 text-yellow-700" title="Has skip-to branching">&#8599; Branch</span>
                @endif
                @if($hasConditions)
                    <span class="px-2 py-0.5 text-xs rounded bg-cyan-100 text-cyan-700" title="Has show conditions">&#9881; {{ strtoupper($conditionType) }}</span>
                @endif
            </div>

            {{-- Display varies by slide type --}}
            @if(in_array($slideType, ['question', 'question_text']))
                <p class="text-gray-900 font-medium">{{ $question->question_text }}</p>
            @elseif($question->content_title)
                <p class="text-gray-900 font-medium">{{ $question->content_title }}</p>
            @else
                <p class="text-gray-500 italic">{{ $slideLabel }} slide</p>
            @endif

            {{-- Show options for choice questions --}}
            @if($slideType === 'question' && $question->options)
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($question->options as $option)
                        <span class="text-xs px-2 py-1 bg-white border rounded">
                            {{ $option['text'] ?? $option['label'] ?? $option['value'] ?? 'Option' }}
                            @if(($option['score_tof'] ?? 0) > 0 || ($option['score_mof'] ?? 0) > 0 || ($option['score_bof'] ?? 0) > 0)
                                <span class="text-gray-400 ml-1">
                                    (T:{{ $option['score_tof'] ?? 0 }} M:{{ $option['score_mof'] ?? 0 }} B:{{ $option['score_bof'] ?? 0 }})
                                </span>
                            @endif
                            @if(!empty($option['skip_to_question']))
                                <span class="text-yellow-600 ml-1" title="Skips to question #{{ $option['skip_to_question'] }}">&#8599;</span>
                            @endif
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Show content preview for non-question slides --}}
            @if(in_array($slideType, ['intermission', 'loading', 'bridge']) && $question->content_body)
                <p class="text-sm text-gray-500 mt-1 truncate max-w-md">{{ Str::limit($question->content_body, 80) }}</p>
            @endif

            {{-- Show CTA for reveal/bridge slides --}}
            @if($question->cta_text)
                <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded bg-brand-gold/10 text-brand-gold">CTA: {{ $question->cta_text }}</span>
            @endif
        </div>
        <div class="flex items-center gap-2 ml-4">
            <button type="button" onclick='editQuestion({{ $question->id }}, {!! e($questionJson) !!})' class="text-gray-400 hover:text-gray-600">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>
            <form action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" class="inline"
                onsubmit="return confirm('Delete this slide?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-gray-400 hover:text-red-500">
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
