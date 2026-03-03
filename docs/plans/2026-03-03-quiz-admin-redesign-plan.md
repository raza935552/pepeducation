# Quiz Admin Redesign — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Replace the flat quiz admin editor with a tabbed journey editor that groups slides by phase, shows human-readable labels, and includes a visual journey map.

**Architecture:** UI-only redesign — no schema changes. The controller builds phase groupings from existing `show_conditions` data. The view uses Alpine.js tabs and drag-to-reorder. Existing question/outcome modals are reused unchanged.

**Tech Stack:** Laravel Blade, Alpine.js, Tailwind CSS (existing stack — no new packages)

---

### Task 1: Add Phase Grouping Logic to QuizController

**Files:**
- Modify: `app/Http/Controllers/Admin/QuizController.php:56-60` (the `edit()` method)

**Step 1: Update the `edit()` method to compute phase groups**

Replace the current `edit()` method with one that:
1. Loads questions ordered by `order`
2. Finds the segmentation question (first question slide, order=1)
3. Groups slides into phases based on their `show_conditions`:
   - **Shared Start**: no `show_conditions` at all
   - **TOF Path**: conditions reference segmentation question with `brand_new`
   - **MOF Path**: conditions reference segmentation question with `researching`
   - **BOF Path**: conditions reference segmentation question with `ready_to_buy` (or BOF sub-path values)
   - **Uncategorized**: slides with conditions that don't match any known pattern

```php
public function edit(Quiz $quiz)
{
    $quiz->load(['questions' => fn($q) => $q->orderBy('order'), 'outcomes']);

    // Build phase groups for the tabbed editor
    $questions = $quiz->questions;
    $phases = $this->buildPhaseGroups($questions);

    // Build a lookup map: question ID → readable label (for "leads to" display)
    $slideLabels = $questions->mapWithKeys(fn($q) => [
        $q->id => '#' . $q->order . ' ' . \Str::limit($q->question_text ?: $q->content_title ?: \App\Models\QuizQuestion::getSlideTypeLabel($q->slide_type), 40),
    ])->toArray();

    // Group outcomes by segment for sidebar display
    $outcomesBySegment = $quiz->outcomes->groupBy(function ($outcome) {
        $conditions = $outcome->conditions ?? [];
        return $conditions['segment'] ?? 'other';
    });

    return view('admin.quizzes.edit', compact('quiz', 'phases', 'slideLabels', 'outcomesBySegment'));
}

/**
 * Group quiz slides into journey phases based on show_conditions.
 */
private function buildPhaseGroups($questions): array
{
    // Find the segmentation question (first question-type slide)
    $segQuestion = $questions->first(fn($q) => $q->slide_type === 'question');
    $segId = $segQuestion?->id;

    // Map option values to phase names
    $valueToPhase = [
        'brand_new' => 'tof',
        'researching' => 'mof',
        'ready_to_buy' => 'bof',
    ];

    $phases = [
        'shared' => ['label' => 'Shared Start', 'icon' => 'play', 'slides' => collect()],
        'tof' => ['label' => 'TOF Path', 'icon' => 'academic-cap', 'description' => 'Top of Funnel — Brand new to peptides', 'slides' => collect()],
        'mof' => ['label' => 'MOF Path', 'icon' => 'book-open', 'description' => 'Middle of Funnel — Researching', 'slides' => collect()],
        'bof' => ['label' => 'BOF Path', 'icon' => 'shopping-cart', 'description' => 'Bottom of Funnel — Ready to buy', 'slides' => collect()],
    ];

    foreach ($questions as $slide) {
        $conditions = $slide->show_conditions['conditions'] ?? [];

        if (empty($conditions)) {
            $phases['shared']['slides']->push($slide);
            continue;
        }

        // Check if any condition references the segmentation question
        $phase = null;
        foreach ($conditions as $cond) {
            if (($cond['question_id'] ?? null) == $segId) {
                $phase = $valueToPhase[$cond['option_value'] ?? ''] ?? null;
                break;
            }
        }

        // If no direct seg reference, check if conditions reference any slide
        // that is itself in a known phase (transitive grouping for BOF sub-paths)
        if (!$phase) {
            foreach ($conditions as $cond) {
                $referencedSlide = $questions->firstWhere('id', $cond['question_id'] ?? null);
                if ($referencedSlide) {
                    // Find what phase the referenced slide is in
                    foreach ($phases as $key => $p) {
                        if ($p['slides']->contains('id', $referencedSlide->id)) {
                            $phase = $key;
                            break 2;
                        }
                    }
                }
            }
        }

        $phases[$phase ?? 'shared']['slides']->push($slide);
    }

    // Remove empty phases (except shared which always shows)
    return collect($phases)->filter(fn($p, $key) => $key === 'shared' || $p['slides']->isNotEmpty())->toArray();
}
```

**Step 2: Verify by loading the admin quiz edit page**

Run: Navigate to `pepprofesor.test/admin/quizzes/{id}/edit` — the page should still load (it will use the old view until we update it, but the data is now available).

**Step 3: Commit**

```bash
git add app/Http/Controllers/Admin/QuizController.php
git commit -m "feat(quiz-admin): add phase grouping logic to edit controller"
```

---

### Task 2: Rebuild edit.blade.php with Tabbed Layout

**Files:**
- Modify: `resources/views/admin/quizzes/edit.blade.php` (complete rewrite)

**Step 1: Replace the entire edit view with the tabbed layout**

The new layout has:
- Tab bar with Journey Map + phase tabs
- Main content area showing slides for active tab
- Right sidebar with collapsible settings, stats, and grouped outcomes

```blade
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
                        $segmentColors = ['tof' => 'blue', 'mof' => 'yellow', 'bof' => 'green', 'other' => 'gray'];
                        $segmentLabels = ['tof' => 'Top of Funnel', 'mof' => 'Middle of Funnel', 'bof' => 'Bottom of Funnel', 'other' => 'Other'];
                    @endphp
                    @foreach($outcomesBySegment as $segment => $outcomes)
                        <div class="mb-3">
                            <h4 class="text-xs font-medium text-{{ $segmentColors[$segment] ?? 'gray' }}-700 bg-{{ $segmentColors[$segment] ?? 'gray' }}-50 px-2 py-1 rounded mb-2">
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
```

**Step 2: Verify the page loads with the new tabbed layout**

Navigate to `pepprofesor.test/admin/quizzes/{id}/edit` — tabs should appear and switch content.

**Step 3: Commit**

```bash
git add resources/views/admin/quizzes/edit.blade.php
git commit -m "feat(quiz-admin): rebuild edit page with tabbed journey layout"
```

---

### Task 3: Create Journey Map Partial

**Files:**
- Create: `resources/views/admin/quizzes/partials/journey-map.blade.php`

**Step 1: Create the visual journey map overview**

This partial shows a visual flow of all phases with slide counts, branching labels, and clickable phase boxes.

```blade
<div class="card p-6">
    <h3 class="text-lg font-semibold mb-6">Journey Map</h3>

    <div class="relative">
        {{-- Shared Start --}}
        @if(isset($phases['shared']))
        <div class="flex items-center gap-4 mb-6">
            <button @click="activeTab = 'shared'" class="flex-shrink-0 w-64 p-4 rounded-xl border-2 border-gray-300 bg-white hover:border-brand-gold hover:shadow-md transition-all cursor-pointer text-left">
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
            <div class="flex-shrink-0 text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </div>

            {{-- Branch labels --}}
            <div class="flex-1 space-y-2">
                @php
                    $branchInfo = [
                        'tof' => ['label' => 'Brand new to peptides', 'color' => 'blue'],
                        'mof' => ['label' => 'Researching', 'color' => 'yellow'],
                        'bof' => ['label' => 'Ready to buy', 'color' => 'green'],
                    ];
                @endphp
                @foreach(['tof', 'mof', 'bof'] as $phase)
                    @if(isset($phases[$phase]))
                        <button @click="activeTab = '{{ $phase }}'" class="w-full flex items-center gap-3 p-3 rounded-xl border-2 border-{{ $branchInfo[$phase]['color'] }}-200 bg-{{ $branchInfo[$phase]['color'] }}-50 hover:border-{{ $branchInfo[$phase]['color'] }}-400 hover:shadow-md transition-all cursor-pointer text-left">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-{{ $branchInfo[$phase]['color'] }}-100 flex items-center justify-center">
                                <span class="text-xs font-bold text-{{ $branchInfo[$phase]['color'] }}-700">{{ strtoupper($phase) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm text-gray-900">{{ $phases[$phase]['label'] }}</p>
                                <p class="text-xs text-gray-500">{{ $phases[$phase]['slides']->count() }} slides — "{{ $branchInfo[$phase]['label'] }}"</p>
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
                    @php
                        $segColors = ['tof' => 'blue', 'mof' => 'yellow', 'bof' => 'green'];
                    @endphp
                    @foreach(['tof', 'mof', 'bof'] as $seg)
                        @php $segOutcomes = $quiz->outcomes->filter(fn($o) => ($o->conditions['segment'] ?? '') === $seg); @endphp
                        @if($segOutcomes->isNotEmpty())
                            <div class="rounded-lg border border-{{ $segColors[$seg] }}-200 bg-{{ $segColors[$seg] }}-50/50 p-3">
                                <p class="text-xs font-semibold text-{{ $segColors[$seg] }}-700 mb-1">{{ strtoupper($seg) }} Outcomes</p>
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
```

**Step 2: Verify the journey map renders correctly**

Navigate to the edit page — Journey Map tab should show phase boxes with branching connections.

**Step 3: Commit**

```bash
git add resources/views/admin/quizzes/partials/journey-map.blade.php
git commit -m "feat(quiz-admin): add visual journey map overview tab"
```

---

### Task 4: Enhance Question Row Partial

**Files:**
- Modify: `resources/views/admin/quizzes/partials/question-row.blade.php` (complete rewrite)

**Step 1: Rewrite with enhanced cards showing human-readable labels**

Replace the current flat row with a card that shows:
- "Visible when:" in plain English (replaces gear icon)
- "Next →" destination slide name (replaces arrow icon)
- "Leans TOF/MOF/BOF" friendly score labels (replaces `T:3 M:0 B:0`)
- Options as structured sub-cards

```blade
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
        // Try to find the option label from the referenced slide
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
            <div class="flex items-center gap-2 mb-1">
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
```

**Step 2: Verify cards render with expanded details**

Navigate to a phase tab, click expand on a question card — should show options with friendly labels, jump targets, and score directions.

**Step 3: Commit**

```bash
git add resources/views/admin/quizzes/partials/question-row.blade.php
git commit -m "feat(quiz-admin): enhance slide cards with human-readable labels"
```

---

### Task 5: Test End-to-End

**Step 1: Navigate to the quiz admin edit page and verify:**
- [ ] Tab bar shows all phases with correct slide counts
- [ ] Journey Map tab shows flow visualization with clickable phase boxes
- [ ] Clicking a phase box switches to that tab
- [ ] Each phase tab shows only its slides (not all 30+)
- [ ] Slide cards show "Visible when:" conditions in plain English
- [ ] Expanding a question card shows options with "Leans TOF/MOF/BOF" labels
- [ ] Options with skip_to show "Jumps to: [slide name]" with readable names
- [ ] Sidebar shows collapsible settings, stats, and grouped outcomes
- [ ] "Edit" button on slide cards opens the existing modal correctly
- [ ] Adding a new slide via the modal still works
- [ ] Existing question/outcome modals work unchanged

**Step 2: Test edge cases:**
- [ ] Quiz with no questions (empty state)
- [ ] Quiz with no outcomes
- [ ] Slides with no conditions show in Shared Start
- [ ] BOF sub-path slides group correctly under BOF

**Step 3: Final commit (if any tweaks needed)**

```bash
git add -A
git commit -m "fix(quiz-admin): polish tabbed editor after testing"
```
