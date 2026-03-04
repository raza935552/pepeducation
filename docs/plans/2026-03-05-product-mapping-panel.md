# Product Mapping Panel — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a read-only "Product Recommendations" card to the quiz editor sidebar so marketing can see which health goal maps to which peptide, with edit links to the Results Bank.

**Architecture:** Query the `results_bank` table in `QuizController::edit()`, group entries by experience level, pass to a new Blade partial that renders a mapping table in the sidebar.

**Tech Stack:** Laravel, Blade, Tailwind CSS

---

### Task 1: Pass Results Bank data from QuizController

**Files:**
- Modify: `app/Http/Controllers/Admin/QuizController.php:57-137` (the `edit` method)

**Step 1: Add the ResultsBank import**

At line 7, add the import:

```php
use App\Models\ResultsBank;
```

**Step 2: Query Results Bank entries in the `edit` method**

Before the `return view(...)` line (line 136), add:

```php
// Load Results Bank entries for the product mapping panel
$resultsBankEntries = ResultsBank::where('is_active', true)
    ->with('stackProduct')
    ->orderBy('health_goal')
    ->orderBy('experience_level')
    ->get()
    ->groupBy('experience_level');
```

**Step 3: Pass to the view**

Update the `return view(...)` line to include the new variable:

```php
return view('admin.quizzes.edit', compact('quiz', 'phases', 'slideLabels', 'outcomesBySegment', 'questionsJson', 'outcomesJson', 'resultsBankEntries'));
```

**Step 4: Commit**

```bash
git add app/Http/Controllers/Admin/QuizController.php
git commit -m "feat(quiz-admin): pass Results Bank data to quiz editor view"
```

---

### Task 2: Create the Product Mapping Panel partial

**Files:**
- Create: `resources/views/admin/quizzes/partials/product-mapping-panel.blade.php`

**Step 1: Create the Blade partial**

```blade
@php
    $healthGoals = \App\Models\ResultsBank::HEALTH_GOALS;
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
```

**Step 2: Commit**

```bash
git add resources/views/admin/quizzes/partials/product-mapping-panel.blade.php
git commit -m "feat(quiz-admin): create product mapping panel partial"
```

---

### Task 3: Include the panel in the quiz editor sidebar

**Files:**
- Modify: `resources/views/admin/quizzes/edit.blade.php:152-178` (sidebar, after Outcomes card)

**Step 1: Add the include**

After the Outcomes card closing `</div>` (line 178) and before the Delete form (line 180), add:

```blade
                {{-- Product Mapping --}}
                @include('admin.quizzes.partials.product-mapping-panel')
```

**Step 2: Commit**

```bash
git add resources/views/admin/quizzes/edit.blade.php
git commit -m "feat(quiz-admin): add product mapping panel to quiz editor sidebar"
```

---

### Task 4: Visual verification in browser

**Step 1: Navigate to the quiz editor**

Open `http://pepprofesor.test/admin/quizzes/9/edit` in the browser.

**Step 2: Verify the panel**

Check the sidebar for the "Product Recommendations" card. Verify:
- All 10 health goals are listed with their peptide names
- Beginner/Advanced toggle works
- Edit links point to the correct Results Bank entries
- Coverage indicator shows green (all goals covered)
- "TOF = Beginner" hint is visible

**Step 3: Verify edit links**

Click one of the edit icons and confirm it navigates to the correct Results Bank entry.

**Step 4: Commit all together if any fixes needed, then push**

```bash
git push origin main
```
