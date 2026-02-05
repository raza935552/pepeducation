<div class="border rounded-lg p-4 bg-gray-50" data-question-id="{{ $question->id }}">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-sm font-medium text-gray-500">Q{{ $question->order }}</span>
                <span class="px-2 py-0.5 text-xs rounded bg-gray-200 text-gray-700">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                @if($question->klaviyo_property)
                    <span class="px-2 py-0.5 text-xs rounded bg-purple-100 text-purple-700">{{ $question->klaviyo_property }}</span>
                @endif
            </div>
            <p class="text-gray-900 font-medium">{{ $question->question_text }}</p>
            @if($question->options)
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($question->options as $option)
                        <span class="text-xs px-2 py-1 bg-white border rounded">
                            {{ $option['text'] ?? $option['label'] ?? $option['value'] ?? 'Option' }}
                            @if(($option['score_tof'] ?? 0) > 0 || ($option['score_mof'] ?? 0) > 0 || ($option['score_bof'] ?? 0) > 0)
                                <span class="text-gray-400 ml-1">
                                    (T:{{ $option['score_tof'] ?? 0 }} M:{{ $option['score_mof'] ?? 0 }} B:{{ $option['score_bof'] ?? 0 }})
                                </span>
                            @endif
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="flex items-center gap-2 ml-4">
            <button type="button" onclick="editQuestion({{ $question->id }})" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>
            <form action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" class="inline"
                onsubmit="return confirm('Delete this question?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
