<div class="border rounded-lg p-4 bg-gray-50" data-outcome-id="{{ $outcome->id }}">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
                <span class="font-medium text-gray-900">{{ $outcome->name }}</span>
                @if($outcome->segment)
                    <span class="px-2 py-0.5 text-xs rounded
                        {{ $outcome->segment === 'bof' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $outcome->segment === 'mof' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $outcome->segment === 'tof' ? 'bg-blue-100 text-blue-800' : '' }}">
                        {{ strtoupper($outcome->segment) }}
                    </span>
                @endif
                <span class="text-sm text-gray-500">Min score: {{ $outcome->min_score }}</span>
            </div>
            @if($outcome->headline)
                <p class="text-sm text-gray-600 mb-1">{{ $outcome->headline }}</p>
            @endif
            @if($outcome->recommended_peptides)
                <div class="text-xs text-gray-500">
                    Recommends: {{ implode(', ', $outcome->recommended_peptides) }}
                </div>
            @endif
            <div class="text-xs text-gray-400 mt-2">Shown {{ number_format($outcome->shown_count) }} times</div>
        </div>
        <div class="flex items-center gap-2 ml-4">
            <button type="button" onclick="editOutcome({{ $outcome->id }})" class="text-gray-400 hover:text-gray-600">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>
            <form action="{{ route('admin.quizzes.outcomes.destroy', [$quiz, $outcome]) }}" method="POST" class="inline"
                onsubmit="return confirm('Delete this outcome?')">
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
