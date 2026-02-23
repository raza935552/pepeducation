<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function analytics(Quiz $quiz)
    {
        $quiz->load('questions', 'outcomes');
        $responses = QuizResponse::where('quiz_id', $quiz->id);

        // Summary stats
        $totalStarted = (clone $responses)->count();
        $totalCompleted = (clone $responses)->completed()->count();
        $totalAbandoned = (clone $responses)->abandoned()->count();
        $completionRate = $totalStarted > 0 ? round($totalCompleted / $totalStarted * 100, 1) : 0;
        $abandonmentRate = $totalStarted > 0 ? round($totalAbandoned / $totalStarted * 100, 1) : 0;
        $avgDuration = (clone $responses)->completed()->avg('duration_seconds');
        $avgQuestionsBeforeAbandon = (clone $responses)->abandoned()->avg('questions_answered');

        // Drop-off per question: count how many respondents answered each question index
        $allResponses = (clone $responses)->whereNotNull('answers')->get(['answers', 'status']);
        $inputQuestions = $quiz->questions->filter(fn ($q) => in_array($q->slide_type, ['question', 'question_text', 'email_capture']))->values();
        $dropoff = [];
        foreach ($inputQuestions as $i => $question) {
            $answeredCount = $allResponses->filter(fn ($r) => isset($r->answers[$i]))->count();
            $prevCount = $i === 0 ? $totalStarted : ($dropoff[$i - 1]['answered'] ?? $totalStarted);
            $dropoffPct = $prevCount > 0 ? round((1 - $answeredCount / $prevCount) * 100, 1) : 0;
            $dropoff[] = [
                'order' => $question->order,
                'label' => \Str::limit($question->question_text, 50),
                'answered' => $answeredCount,
                'dropoff_pct' => $i === 0 ? 0 : $dropoffPct,
            ];
        }

        // Outcome distribution
        $outcomeDistribution = $quiz->outcomes->map(fn ($o) => [
            'name' => $o->name,
            'segment' => $o->conditions['segment'] ?? $o->conditions['type'] ?? '-',
            'count' => $o->shown_count,
        ]);

        // Segment breakdown
        $segmentBreakdown = (clone $responses)->completed()
            ->select('segment', DB::raw('count(*) as count'))
            ->groupBy('segment')
            ->pluck('count', 'segment')
            ->toArray();

        // Recent responses
        $recentResponses = QuizResponse::where('quiz_id', $quiz->id)
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.quizzes.analytics', compact(
            'quiz', 'totalStarted', 'totalCompleted', 'totalAbandoned',
            'completionRate', 'abandonmentRate', 'avgDuration',
            'avgQuestionsBeforeAbandon', 'dropoff', 'outcomeDistribution',
            'segmentBreakdown', 'recentResponses'
        ));
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
