<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200">
        <h3 class="font-semibold text-gray-900">Quiz Responses</h3>
    </div>
    <div class="divide-y divide-gray-200">
        @forelse($subscriber->quizResponses()->with('quiz')->latest()->get() as $response)
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $response->quiz?->name ?? 'Unknown Quiz' }}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                {{ $response->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($response->status) }}
                            </span>
                            @if($response->segment)
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">
                                    {{ strtoupper($response->segment) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">
                        {{ $response->created_at->diffForHumans() }}
                    </p>
                </div>

                {{-- Show Answers --}}
                @if($response->answers && is_array($response->answers) && count($response->answers) > 0)
                    <div class="mt-3 space-y-2">
                        @foreach($response->answers as $answer)
                            <div class="text-xs">
                                <span class="text-gray-500">{{ $answer['question_text'] ?? 'Q' }}:</span>
                                <span class="text-gray-900 font-medium">{{ $answer['option_text'] ?? 'N/A' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Outcome --}}
                @if($response->outcome_name)
                    <div class="mt-2 pt-2 border-t border-gray-100">
                        <span class="text-xs text-gray-500">Outcome:</span>
                        <span class="text-xs font-medium text-amber-600">{{ $response->outcome_name }}</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-4 text-center text-sm text-gray-500">
                No quiz responses
            </div>
        @endforelse
    </div>
</div>
