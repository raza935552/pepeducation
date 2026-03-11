@php
    $healthGoals = \App\Models\ResultsBank::allHealthGoals();
    $beginnerEntries = ($resultsBankEntries['beginner'] ?? collect())->keyBy('health_goal');
    $advancedEntries = ($resultsBankEntries['advanced'] ?? collect())->keyBy('health_goal');
    $coveredCount = $beginnerEntries->count();
    $totalGoals = count($healthGoals);
@endphp

<div class="card p-4" x-data="{ showAdvanced: false }">
    <div class="mb-3">
        <h3 class="text-sm font-semibold">Product Recommendations</h3>
        <p class="text-xs text-gray-500 mt-1">Based on the <span class="font-medium text-gray-700">health_goal</span> answer</p>
    </div>

    {{-- Experience level toggle --}}
    <div class="flex items-center gap-2 mb-3">
        <button @click="showAdvanced = false" :class="!showAdvanced ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600'" class="px-2 py-1 text-xs rounded-full font-medium transition-colors">
            Beginner
        </button>
        <button @click="showAdvanced = true" :class="showAdvanced ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-600'" class="px-2 py-1 text-xs rounded-full font-medium transition-colors">
            Advanced
        </button>
        <span class="text-xs text-gray-400 ml-auto">TOF = Beginner</span>
    </div>

    {{-- Beginner mappings --}}
    <div x-show="!showAdvanced" class="space-y-1.5">
        @foreach($healthGoals as $key => $label)
            @php $entry = $beginnerEntries[$key] ?? null; @endphp
            <div class="flex items-center justify-between py-1.5 px-2 rounded {{ $entry ? 'bg-gray-50' : 'bg-yellow-50' }}">
                <div class="flex-1 min-w-0">
                    <span class="text-xs font-medium text-gray-700 truncate block">{{ $label }}</span>
                </div>
                @if($entry)
                    <div class="flex items-center gap-1.5 ml-2">
                        <span class="text-xs text-gray-600 font-medium">{{ $entry->peptide_name }}</span>
                        <a href="{{ route('admin.results-bank.edit', $entry) }}" class="text-gray-400 hover:text-brand-gold" title="Edit in Results Bank">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    </div>
                @else
                    <span class="text-xs text-yellow-600 font-medium">Not set</span>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Advanced mappings --}}
    <div x-show="showAdvanced" x-cloak class="space-y-1.5">
        @foreach($healthGoals as $key => $label)
            @php $entry = $advancedEntries[$key] ?? null; @endphp
            <div class="flex items-center justify-between py-1.5 px-2 rounded {{ $entry ? 'bg-gray-50' : 'bg-yellow-50' }}">
                <div class="flex-1 min-w-0">
                    <span class="text-xs font-medium text-gray-700 truncate block">{{ $label }}</span>
                </div>
                @if($entry)
                    <div class="flex items-center gap-1.5 ml-2">
                        <span class="text-xs text-gray-600 font-medium">{{ $entry->peptide_name }}</span>
                        <a href="{{ route('admin.results-bank.edit', $entry) }}" class="text-gray-400 hover:text-brand-gold" title="Edit in Results Bank">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    </div>
                @else
                    <span class="text-xs text-yellow-600 font-medium">Not set</span>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Coverage status --}}
    <div class="mt-3 pt-2 border-t border-gray-100">
        @if($coveredCount >= $totalGoals)
            <p class="text-xs text-green-600 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                All {{ $totalGoals }} goals have products assigned
            </p>
        @else
            <p class="text-xs text-yellow-600 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ $totalGoals - $coveredCount }} goals missing products
            </p>
        @endif
        <a href="{{ route('admin.results-bank.index') }}" class="text-xs text-brand-gold hover:underline mt-1 inline-block">Manage Results Bank</a>
    </div>
</div>
