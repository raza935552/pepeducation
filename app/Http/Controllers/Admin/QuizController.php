<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::withCount(['questions', 'responses'])
            ->latest()
            ->paginate(15);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:quizzes,slug',
            'type' => 'required|in:segmentation,product,custom',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
            'design' => 'nullable|array',
            'klaviyo_list_id' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $quiz = Quiz::create([
            'name' => $validated['name'],
            'slug' => !empty($validated['slug']) ? $validated['slug'] : \Str::slug($validated['name']),
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'settings' => $validated['settings'] ?? $this->defaultSettings(),
            'design' => $validated['design'] ?? $this->defaultDesign(),
            'klaviyo_list_id' => $validated['klaviyo_list_id'] ?? null,
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return redirect()
            ->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Quiz created. Now add questions!');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('questions', 'outcomes');
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:quizzes,slug,' . $quiz->id,
            'type' => 'required|in:segmentation,product,custom',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
            'design' => 'nullable|array',
            'klaviyo_list_id' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $quiz->update([
            'name' => $validated['name'],
            'slug' => !empty($validated['slug']) ? $validated['slug'] : \Str::slug($validated['name']),
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'settings' => $validated['settings'] ?? $quiz->settings,
            'design' => $validated['design'] ?? $quiz->design,
            'klaviyo_list_id' => $validated['klaviyo_list_id'] ?? null,
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return back()->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted.');
    }

    public function duplicate(Quiz $quiz)
    {
        $newQuiz = $quiz->replicate();
        $newQuiz->name = $quiz->name . ' (Copy)';
        $slug = $uniqueSlug = \Str::slug($newQuiz->name);
        $count = 1;
        while (Quiz::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$slug}-{$count}";
            $count++;
        }
        $newQuiz->slug = $uniqueSlug;
        $newQuiz->is_active = false;
        $newQuiz->starts_count = 0;
        $newQuiz->completions_count = 0;
        $newQuiz->save();

        // Duplicate questions and build old→new ID mapping
        $idMap = [];
        foreach ($quiz->questions()->orderBy('order')->get() as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->quiz_id = $newQuiz->id;
            $newQuestion->save();
            $idMap[$question->id] = $newQuestion->id;
        }

        // Remap skip_to_question in options and question_id in show_conditions
        foreach ($newQuiz->questions as $newQuestion) {
            $changed = false;

            // Remap skip_to_question inside options JSON
            $options = $newQuestion->options ?? [];
            foreach ($options as &$option) {
                if (!empty($option['skip_to_question']) && isset($idMap[$option['skip_to_question']])) {
                    $option['skip_to_question'] = $idMap[$option['skip_to_question']];
                    $changed = true;
                }
            }
            unset($option);

            // Remap question_id inside show_conditions JSON
            $showConditions = $newQuestion->show_conditions;
            if (!empty($showConditions['conditions'])) {
                foreach ($showConditions['conditions'] as &$cond) {
                    if (!empty($cond['question_id']) && isset($idMap[$cond['question_id']])) {
                        $cond['question_id'] = $idMap[$cond['question_id']];
                        $changed = true;
                    }
                }
                unset($cond);
            }

            if ($changed) {
                $newQuestion->update([
                    'options' => $options,
                    'show_conditions' => $showConditions,
                ]);
            }
        }

        // Duplicate outcomes
        foreach ($quiz->outcomes as $outcome) {
            $newOutcome = $outcome->replicate();
            $newOutcome->quiz_id = $newQuiz->id;
            $newOutcome->shown_count = 0;
            $newOutcome->save();
        }

        return redirect()->route('admin.quizzes.edit', $newQuiz)->with('success', 'Quiz duplicated.');
    }

    protected function defaultSettings(): array
    {
        return [
            'require_email' => true,
            'show_progress_bar' => true,
            'allow_back' => true,
            'shuffle_options' => false,
        ];
    }

    protected function defaultDesign(): array
    {
        return [
            'primary_color' => '#9A7B4F',
            'background_color' => '#FDF8F3',
            'text_color' => '#1f2937',
            'animation' => 'fade',
        ];
    }
}
