<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.quizzes.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg aria-hidden="true" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <span>Edit: {{ $quiz->name }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">{{ $quiz->completions_count }} completions</span>
                @if($quiz->is_active)
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div x-data="quizEditor()" class="space-y-6">
        {{-- Tab Bar --}}
        <div class="card">
            <div class="flex overflow-x-auto border-b border-gray-200">
                <button @click="activeTab = 'map'" :class="activeTab === 'map' ? 'border-brand-gold text-brand-gold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Journey Map
                </button>
                @foreach($phases as $key => $phase)
                    <button @click="activeTab = '{{ $key }}'" :class="activeTab === '{{ $key }}' ? 'border-brand-gold text-brand-gold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        {{ $phase['label'] }}
                        <span class="px-1.5 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">{{ $phase['slides']->count() }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Journey Map Tab --}}
                <div x-show="activeTab === 'map'" x-cloak>
                    @include('admin.quizzes.partials.journey-map', ['phases' => $phases, 'quiz' => $quiz])
                </div>

                {{-- Phase Tabs --}}
                @foreach($phases as $key => $phase)
                    <div x-show="activeTab === '{{ $key }}'" x-cloak>
                        <div class="card p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ $phase['label'] }}</h3>
                                    @if(!empty($phase['description']))
                                        <p class="text-sm text-gray-500 mt-1">{{ $phase['description'] }}</p>
                                    @endif
                                </div>
                                <button type="button" onclick="showAddQuestion()" class="btn btn-secondary text-sm">+ Add Slide</button>
                            </div>

                            <div class="space-y-3" data-phase="{{ $key }}">
                                @forelse($phase['slides'] as $question)
                                    @include('admin.quizzes.partials.question-row', [
                                        'question' => $question,
                                        'slideLabels' => $slideLabels,
                                    ])
                                @empty
                                    <div class="text-center py-8 text-gray-400">
                                        <p>No slides in this phase yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Quiz Settings (collapsible) --}}
                <div class="card" x-data="{ settingsOpen: false }">
                    <button @click="settingsOpen = !settingsOpen" class="flex items-center justify-between w-full p-4 text-left">
                        <h3 class="text-lg font-semibold">Quiz Settings</h3>
                        <svg :class="settingsOpen ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="settingsOpen" x-collapse>
                        <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" class="px-4 pb-4">
                            @csrf @method('PUT')
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                    <input type="text" name="name" value="{{ $quiz->name }}" required class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                    <input type="text" name="slug" value="{{ $quiz->slug }}" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select name="type" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm">
                                        <option value="segmentation" {{ $quiz->type === 'segmentation' ? 'selected' : '' }}>Segmentation</option>
                                        <option value="product" {{ $quiz->type === 'product' ? 'selected' : '' }}>Product</option>
                                        <option value="custom" {{ $quiz->type === 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Klaviyo List ID</label>
                                    <input type="text" name="klaviyo_list_id" value="{{ $quiz->klaviyo_list_id }}" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm">
                                </div>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="is_active" value="1" {{ $quiz->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                                    <span class="text-sm">Active</span>
                                </label>
                                <button type="submit" class="btn btn-primary w-full text-sm">Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Quiz URL --}}
                <div class="card p-4">
                    <h3 class="text-sm font-semibold mb-2">Quiz URL</h3>
                    <div class="bg-gray-50 rounded-lg p-2 text-xs font-mono break-all">{{ url('/quiz/' . $quiz->slug) }}</div>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ url('/quiz/' . $quiz->slug) }}')" class="mt-1 text-xs text-brand-gold hover:underline">Copy URL</button>
                </div>

                {{-- Stats --}}
                <div class="card p-4">
                    <h3 class="text-sm font-semibold mb-3">Stats</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Started</dt>
                            <dd class="font-medium">{{ number_format($quiz->starts_count) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Completed</dt>
                            <dd class="font-medium">{{ number_format($quiz->completions_count) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Completion Rate</dt>
                            <dd class="font-medium">{{ $quiz->getCompletionRate() }}%</dd>
                        </div>
                    </dl>
                    <a href="{{ route('admin.quizzes.analytics', $quiz) }}" class="mt-3 block text-center btn btn-secondary text-xs w-full">View Full Analytics</a>
                </div>

                {{-- Outcomes (grouped by segment) --}}
                <div class="card p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold">Outcomes ({{ $quiz->outcomes->count() }})</h3>
                        <button type="button" onclick="showAddOutcome()" class="text-xs text-brand-gold hover:underline">+ Add</button>
                    </div>
                    @php
                        $segmentLabels = ['tof' => 'Top of Funnel', 'mof' => 'Middle of Funnel', 'bof' => 'Bottom of Funnel', 'other' => 'Other'];
                    @endphp
                    @foreach($outcomesBySegment as $segment => $outcomes)
                        <div class="mb-3">
                            <h4 class="text-xs font-medium px-2 py-1 rounded mb-2
                                {{ $segment === 'tof' ? 'text-blue-700 bg-blue-50' : '' }}
                                {{ $segment === 'mof' ? 'text-yellow-700 bg-yellow-50' : '' }}
                                {{ $segment === 'bof' ? 'text-green-700 bg-green-50' : '' }}
                                {{ !in_array($segment, ['tof','mof','bof']) ? 'text-gray-700 bg-gray-50' : '' }}">
                                {{ strtoupper($segment) }} — {{ $segmentLabels[$segment] ?? $segment }}
                            </h4>
                            @foreach($outcomes as $outcome)
                                @include('admin.quizzes.partials.outcome-row', ['outcome' => $outcome])
                            @endforeach
                        </div>
                    @endforeach
                    @if($quiz->outcomes->isEmpty())
                        <p class="text-xs text-gray-400 text-center py-2">No outcomes configured yet.</p>
                    @endif
                </div>

                {{-- Delete --}}
                <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" class="card p-4" onsubmit="return confirm('Delete this quiz and all its questions?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600 text-sm">Delete Quiz</button>
                </form>
            </div>
        </div>
    </div>

    @include('admin.quizzes.partials.question-modal')
    @include('admin.quizzes.partials.outcome-modal')

    <script>
    function quizEditor() {
        return {
            activeTab: 'map',
        };
    }
    </script>
</x-admin-layout>
