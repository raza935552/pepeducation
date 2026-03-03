<div class="card p-6">
    <h3 class="text-lg font-semibold mb-6">Journey Map</h3>

    <div class="relative">
        {{-- Shared Start --}}
        @if(isset($phases['shared']))
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4 mb-6">
            <button @click="activeTab = 'shared'" class="flex-shrink-0 w-full lg:w-64 p-4 rounded-xl border-2 border-gray-300 bg-white hover:border-brand-gold hover:shadow-md transition-all cursor-pointer text-left">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span class="font-semibold text-gray-900">Shared Start</span>
                </div>
                <p class="text-sm text-gray-500">{{ $phases['shared']['slides']->count() }} slides — seen by everyone</p>
                @php
                    $segSlide = $phases['shared']['slides']->first(fn($s) => $s->slide_type === 'question');
                @endphp
                @if($segSlide)
                    <p class="text-xs text-gray-400 mt-1 truncate">{{ $segSlide->question_text }}</p>
                @endif
            </button>

            {{-- Branching arrow --}}
            <div class="flex-shrink-0 text-gray-300 hidden lg:block">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>
            <div class="flex-shrink-0 text-gray-300 lg:hidden self-center">
                <svg class="w-6 h-6 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>

            {{-- Branch paths --}}
            <div class="flex-1 space-y-2 w-full">
                @php
                    $branchInfo = [
                        'tof' => ['label' => 'Brand new to peptides', 'color' => 'blue'],
                        'mof' => ['label' => 'Researching', 'color' => 'yellow'],
                        'bof' => ['label' => 'Ready to buy', 'color' => 'green'],
                    ];
                @endphp
                @foreach(['tof', 'mof', 'bof'] as $phaseKey)
                    @if(isset($phases[$phaseKey]))
                        @php $info = $branchInfo[$phaseKey]; @endphp
                        <button @click="activeTab = '{{ $phaseKey }}'" class="w-full flex items-center gap-3 p-3 rounded-xl border-2 transition-all cursor-pointer text-left
                            {{ $info['color'] === 'blue' ? 'border-blue-200 bg-blue-50 hover:border-blue-400' : '' }}
                            {{ $info['color'] === 'yellow' ? 'border-yellow-200 bg-yellow-50 hover:border-yellow-400' : '' }}
                            {{ $info['color'] === 'green' ? 'border-green-200 bg-green-50 hover:border-green-400' : '' }}
                            hover:shadow-md">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                                {{ $info['color'] === 'blue' ? 'bg-blue-100' : '' }}
                                {{ $info['color'] === 'yellow' ? 'bg-yellow-100' : '' }}
                                {{ $info['color'] === 'green' ? 'bg-green-100' : '' }}">
                                <span class="text-xs font-bold
                                    {{ $info['color'] === 'blue' ? 'text-blue-700' : '' }}
                                    {{ $info['color'] === 'yellow' ? 'text-yellow-700' : '' }}
                                    {{ $info['color'] === 'green' ? 'text-green-700' : '' }}">{{ strtoupper($phaseKey) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm text-gray-900">{{ $phases[$phaseKey]['label'] }}</p>
                                <p class="text-xs text-gray-500">{{ $phases[$phaseKey]['slides']->count() }} slides — "{{ $info['label'] }}"</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Outcomes Summary --}}
        @if($quiz->outcomes->isNotEmpty())
            <div class="border-t pt-4 mt-2">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Outcomes</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach(['tof', 'mof', 'bof'] as $seg)
                        @php $segOutcomes = $quiz->outcomes->filter(fn($o) => ($o->conditions['segment'] ?? '') === $seg); @endphp
                        @if($segOutcomes->isNotEmpty())
                            <div class="rounded-lg border p-3
                                {{ $seg === 'tof' ? 'border-blue-200 bg-blue-50/50' : '' }}
                                {{ $seg === 'mof' ? 'border-yellow-200 bg-yellow-50/50' : '' }}
                                {{ $seg === 'bof' ? 'border-green-200 bg-green-50/50' : '' }}">
                                <p class="text-xs font-semibold mb-1
                                    {{ $seg === 'tof' ? 'text-blue-700' : '' }}
                                    {{ $seg === 'mof' ? 'text-yellow-700' : '' }}
                                    {{ $seg === 'bof' ? 'text-green-700' : '' }}">{{ strtoupper($seg) }} Outcomes</p>
                                @foreach($segOutcomes as $outcome)
                                    <p class="text-xs text-gray-600">{{ $outcome->name }}</p>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
