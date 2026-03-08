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
                <button @click="$dispatch('open-simulator')" class="btn btn-primary text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Preview Quiz
                </button>
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
                    @include('admin.quizzes.partials.journey-map', ['phases' => $phases, 'quiz' => $quiz, 'outcomesBySegment' => $outcomesBySegment, 'slideLabels' => $slideLabels])
                </div>

                {{-- Phase Tabs --}}
                @foreach($phases as $key => $phase)
                    <div x-show="activeTab === '{{ $key }}'" x-cloak x-data="{ slideFilter: '' }">
                        <div class="card p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ $phase['label'] }}</h3>
                                    @if(!empty($phase['description']))
                                        <p class="text-sm text-gray-500 mt-1">{{ $phase['description'] }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <select x-model="slideFilter" class="rounded-lg border-gray-300 text-xs py-1.5 pl-2 pr-7 focus:border-brand-gold focus:ring-brand-gold">
                                        <option value="">All types</option>
                                        @foreach(\App\Models\QuizQuestion::SLIDE_TYPES as $type)
                                            <option value="{{ $type }}">{{ \App\Models\QuizQuestion::getSlideTypeLabel($type) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="showAddQuestion()" class="btn btn-secondary text-sm">+ Add Slide</button>
                                </div>
                            </div>

                            <div class="space-y-1.5" data-phase="{{ $key }}">
                                @forelse($phase['slides'] as $question)
                                    <div x-show="!slideFilter || slideFilter === '{{ $question->slide_type ?? 'question' }}'">
                                        @include('admin.quizzes.partials.question-row', [
                                            'question' => $question,
                                            'slideLabels' => $slideLabels,
                                            'phaseKey' => $key,
                                        ])
                                    </div>
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

                {{-- Quick-Reference Guide --}}
                @include('admin.quizzes.partials.guide-panel')

                {{-- Outcomes (flat list sorted by priority) --}}
                <div class="card p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-semibold">Outcomes ({{ $quiz->outcomes->count() }})</h3>
                        <button type="button" @click="$dispatch('open-outcome-modal', {})" class="text-xs text-brand-gold hover:underline">+ Add</button>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-3">Drag to reorder. First matching outcome wins.</p>
                    <div id="outcomes-sortable" class="space-y-2">
                        @foreach($quiz->outcomes->sortBy('priority') as $outcome)
                            @include('admin.quizzes.partials.outcome-row', ['outcome' => $outcome])
                        @endforeach
                    </div>
                    @if($quiz->outcomes->isEmpty())
                        <p class="text-xs text-gray-400 text-center py-2">No outcomes configured yet.</p>
                    @endif
                </div>

                {{-- Outcome Coverage --}}
                @include('admin.quizzes.partials.outcome-coverage-panel')

                {{-- Product Mapping --}}
                @include('admin.quizzes.partials.product-mapping-panel')

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
    @include('admin.quizzes.partials.quiz-simulator', [
        'questionsJson' => $questionsJson,
        'outcomesJson' => $outcomesJson,
    ])

    <script>
    function quizEditor() {
        return {
            activeTab: 'map',
        };
    }

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const colors = {
            success: 'bg-green-600',
            error: 'bg-red-600',
            info: 'bg-blue-600',
        };
        toast.className = `fixed bottom-6 right-6 ${colors[type] || colors.success} text-white px-4 py-2.5 rounded-lg shadow-lg text-sm z-50 transition-all duration-300 translate-y-0 opacity-100`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    function assignSegment(url, segment, el) {
        const segDotColors = { shared: 'bg-gray-400', tof: 'bg-green-500', mof: 'bg-yellow-500', bof: 'bg-red-500' };
        const segLabels = { shared: 'Shared', tof: 'TOF', mof: 'MOF', bof: 'BOF' };

        // Find the slide row and its dot
        const slideRow = el.closest('[data-question-id]');
        const questionId = slideRow?.dataset.questionId;

        fetch(url, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ segment: segment }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Update the dot color inline
                const dot = document.getElementById('seg-dot-' + questionId);
                if (dot) {
                    dot.className = 'w-2 h-2 rounded-full ' + (segDotColors[segment] || 'bg-gray-400') + ' flex-shrink-0';
                    dot.title = segLabels[segment] || 'Shared';
                }
                showToast(data.message || 'Segment assigned.');
            } else {
                showToast(data.message || 'Failed to assign segment.', 'error');
            }
        })
        .catch(() => showToast('Something went wrong. Try again.', 'error'));
    }

    function duplicateSlide(url, btn) {
        btn.disabled = true;
        btn.classList.add('opacity-50');

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Slide duplicated as #' + (data.question?.order || '?') + '. Refreshing...');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('Duplicate failed.', 'error');
                btn.disabled = false;
                btn.classList.remove('opacity-50');
            }
        })
        .catch(() => {
            showToast('Something went wrong.', 'error');
            btn.disabled = false;
            btn.classList.remove('opacity-50');
        });
    }

    function deleteSlideWithCheck(url, slideName) {
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.needs_confirmation) {
                let msg = 'Deleting "' + slideName + '" will affect:\n\n';
                data.warnings.forEach(w => { msg += '- ' + w.message + '\n'; });
                msg += '\nDelete anyway?';
                if (confirm(msg)) {
                    fetch(url + '?confirmed=true', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    }).then(r => r.json()).then(d => {
                        if (d.success) removeSlideFromDom(url, slideName);
                    });
                }
            } else if (data.success) {
                removeSlideFromDom(url, slideName);
            }
        })
        .catch(() => {
            if (confirm('Delete "' + slideName + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').content + '"><input type="hidden" name="_method" value="DELETE">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function removeSlideFromDom(url, slideName) {
        // Find and remove the slide row by matching the delete button's URL
        document.querySelectorAll('[data-question-id]').forEach(row => {
            const deleteBtn = row.querySelector('[onclick*="deleteSlideWithCheck"]');
            if (deleteBtn && deleteBtn.getAttribute('onclick').includes(url)) {
                row.style.transition = 'opacity 0.3s, max-height 0.3s';
                row.style.opacity = '0';
                row.style.maxHeight = row.offsetHeight + 'px';
                row.style.overflow = 'hidden';
                setTimeout(() => {
                    row.style.maxHeight = '0';
                    row.style.marginBottom = '0';
                    row.style.padding = '0';
                    setTimeout(() => row.remove(), 300);
                }, 100);
            }
        });
        showToast('"' + slideName + '" deleted.');
    }
    </script>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Outcome reorder
        const outcomesContainer = document.getElementById('outcomes-sortable');
        if (outcomesContainer) {
            new Sortable(outcomesContainer, {
                handle: '.outcome-drag-handle',
                animation: 150,
                ghostClass: 'opacity-30',
                chosenClass: 'ring-2 ring-brand-gold/50',
                onEnd: function() {
                    const ids = [...outcomesContainer.querySelectorAll('[data-outcome-id]')]
                        .map(el => parseInt(el.dataset.outcomeId));

                    fetch('{{ route("admin.quizzes.outcomes.reorder", $quiz) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ outcomes: ids }),
                    }).then(r => {
                        if (!r.ok) console.error('Outcome reorder failed');
                    });
                }
            });
        }

        // Slide reorder
        document.querySelectorAll('[data-phase]').forEach(container => {
            new Sortable(container, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'opacity-30',
                chosenClass: 'ring-2 ring-brand-gold/50',
                onEnd: function() {
                    const ids = [...container.querySelectorAll('[data-question-id]')]
                        .map(el => parseInt(el.dataset.questionId));

                    fetch('{{ route("admin.quizzes.questions.reorder", $quiz) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ questions: ids }),
                    }).then(r => {
                        if (!r.ok) console.error('Reorder failed');
                    });
                }
            });
        });
    });
    </script>
    @endpush
</x-admin-layout>
