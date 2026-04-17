<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizResponse;
use App\Models\ResultsBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'marketing_list_id' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Generate unique slug
        $baseSlug = !empty($validated['slug']) ? $validated['slug'] : \Str::slug($validated['name']);
        $slug = $baseSlug;
        $count = 1;
        while (Quiz::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        $quiz = Quiz::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'settings' => $validated['settings'] ?? $this->defaultSettings(),
            'design' => $validated['design'] ?? $this->defaultDesign(),
            'marketing_list_id' => $validated['marketing_list_id'] ?? null,
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return redirect()
            ->route('admin.quizzes.edit', $quiz)
            ->with('success', 'Quiz created. Now add questions!');
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load(['questions' => fn($q) => $q->orderBy('order'), 'outcomes']);

        $questions = $quiz->questions;
        $phases = $this->buildPhaseGroups($questions);

        // Build a lookup map: question ID → readable label (for "leads to" display)
        $slideLabels = $questions->mapWithKeys(fn($q) => [
            $q->id => '#' . $q->order . ' ' . \Str::limit($q->question_text ?: $q->content_title ?: \App\Models\QuizQuestion::getSlideTypeLabel($q->slide_type), 40),
        ])->toArray();

        // Serialize questions for simulator
        $questionsJson = $questions->map(fn($q) => [
            'id' => $q->id,
            'order' => $q->order,
            'slide_type' => $q->slide_type ?? 'question',
            'question_text' => $q->question_text,
            'question_type' => $q->question_type,
            'options' => $q->options ?? [],
            'content_title' => $q->content_title,
            'content_body' => $q->content_body,
            'content_source' => $q->content_source,
            'auto_advance_seconds' => $q->auto_advance_seconds ?? 5,
            'cta_text' => $q->cta_text,
            'cta_url' => $q->cta_url,
            'show_conditions' => $q->show_conditions,
            'skip_to_question' => $q->skip_to_question,
            'dynamic_content_key' => $q->dynamic_content_key,
            'dynamic_content_map' => $q->dynamic_content_map ?? [],
            'marketing_property' => $q->marketing_property,
        ])->values();

        // Serialize outcomes for simulator (sorted by priority like determineOutcome())
        $outcomesJson = $quiz->outcomes->sortBy('priority')->values()->map(fn($o) => [
            'id' => $o->id,
            'name' => $o->name,
            'conditions' => $o->conditions ?? [],
            'result_title' => $o->result_title,
            'result_message' => $o->result_message,
            'result_image' => $o->result_image ? Storage::url($o->result_image) : null,
            'redirect_url' => $o->redirect_url,
            'redirect_type' => $o->redirect_type,
            'product_link' => $o->product_link,
            'priority' => $o->priority,
            'is_active' => $o->is_active,
        ]);

        // Group outcomes by segment for sidebar display
        $outcomesBySegment = $quiz->outcomes->groupBy(function ($outcome) {
            $conditions = $outcome->conditions ?? [];

            // Direct segment condition
            if (!empty($conditions['segment'])) {
                return $conditions['segment'];
            }

            // Infer segment from answer-based conditions
            if (($conditions['type'] ?? '') === 'answer') {
                $question = $conditions['question'] ?? '';
                $value = $conditions['value'] ?? '';

                if ($question === 'awareness_level') {
                    return match ($value) {
                        'brand_new' => 'tof',
                        'researching' => 'mof',
                        'ready_to_buy' => 'bof',
                        default => 'other',
                    };
                }

                // bof_intent questions are always BOF
                if ($question === 'bof_intent') {
                    return 'bof';
                }
            }

            return 'other';
        });

        // Load Results Bank entries for the product mapping panel
        $resultsBankEntries = ResultsBank::where('is_active', true)
            ->with('stackProduct')
            ->orderBy('health_goal')
            ->orderBy('experience_level')
            ->get()
            ->groupBy('experience_level');

        // Serialize ResultsBank for the simulator preview
        $resultsBankJson = ResultsBank::where('is_active', true)->get()->map(fn($e) => [
            'health_goal' => $e->health_goal,
            'experience_level' => $e->experience_level,
            'peptide_name' => $e->peptide_name,
            'star_rating' => $e->star_rating,
            'rating_label' => $e->rating_label,
            'description' => $e->description,
            'benefits' => $e->benefits ?? [],
            'testimonial' => $e->testimonial,
            'testimonial_author' => $e->testimonial_author,
            'display_fields' => $e->display_fields ?? [],
        ])->values();

        return view('admin.quizzes.edit', compact('quiz', 'phases', 'slideLabels', 'outcomesBySegment', 'questionsJson', 'outcomesJson', 'resultsBankEntries', 'resultsBankJson'));
    }

    /**
     * Group quiz slides into journey phases based on show_conditions.
     */
    private function buildPhaseGroups($questions): array
    {
        // Find the segmentation question (first question-type slide)
        $segQuestion = $questions->first(fn($q) => $q->slide_type === 'question');
        $segId = $segQuestion?->id;

        // Build value→phase map from the segmentation question's option scores
        // Each option's highest score (tof/mof/bof) determines which phase that answer maps to
        $valueToPhase = [];
        if ($segQuestion) {
            foreach ($segQuestion->options ?? [] as $opt) {
                $value = $opt['value'] ?? $opt['marketing_value'] ?? '';
                if (!$value) continue;
                $scores = [
                    'tof' => (int) ($opt['score_tof'] ?? 0),
                    'mof' => (int) ($opt['score_mof'] ?? 0),
                    'bof' => (int) ($opt['score_bof'] ?? 0),
                ];
                $maxScore = max($scores);
                if ($maxScore > 0) {
                    $valueToPhase[$value] = array_search($maxScore, $scores);
                }
            }
        }

        // Fallback: legacy hardcoded mappings for quizzes that don't use scoring
        if (empty($valueToPhase)) {
            $valueToPhase = [
                'brand_new' => 'tof',
                'researching' => 'mof',
                'ready_to_buy' => 'bof',
            ];
        }

        $phases = [
            'shared' => ['label' => 'Shared Start', 'description' => 'Seen by everyone', 'slides' => collect()],
            'tof' => ['label' => 'TOF Path', 'description' => 'Top of Funnel — Brand new to peptides', 'slides' => collect()],
            'mof' => ['label' => 'MOF Path', 'description' => 'Middle of Funnel — Researching', 'slides' => collect()],
            'bof' => ['label' => 'BOF Path', 'description' => 'Bottom of Funnel — Ready to buy', 'slides' => collect()],
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

            // Transitive grouping: if conditions reference a slide already in a phase
            if (!$phase) {
                foreach ($conditions as $cond) {
                    $referencedSlide = $questions->firstWhere('id', $cond['question_id'] ?? null);
                    if ($referencedSlide) {
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

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:quizzes,slug,' . $quiz->id,
            'type' => 'required|in:segmentation,product,custom',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
            'design' => 'nullable|array',
            'marketing_list_id' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Generate unique slug (skip self)
        $baseSlug = !empty($validated['slug']) ? $validated['slug'] : \Str::slug($validated['name']);
        $slug = $baseSlug;
        $count = 1;
        while (Quiz::where('slug', $slug)->where('id', '!=', $quiz->id)->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        $quiz->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'settings' => $validated['settings'] ?? $quiz->settings,
            'design' => $validated['design'] ?? $quiz->design,
            'marketing_list_id' => $validated['marketing_list_id'] ?? null,
            'is_active' => $validated['is_active'] ?? false,
        ]);

        return back()->with('success', 'Quiz updated successfully.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz deleted.');
    }

    public function guide()
    {
        return view('admin.quizzes.guide');
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
            ->with('outcome')
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

    /**
     * Export the full quiz structure as a plain-text document.
     * Output is designed to be pasted into a Claude chat for briefing or improvement.
     */
    public function export(Quiz $quiz)
    {
        $quiz->load(['questions' => fn ($q) => $q->orderBy('order'), 'outcomes']);

        $lines = [];
        $lines[] = '================================================================';
        $lines[] = 'QUIZ EXPORT: ' . $quiz->name;
        $lines[] = '================================================================';
        $lines[] = 'Slug: ' . $quiz->slug;
        $lines[] = 'Type: ' . ($quiz->type ?? 'n/a');
        $lines[] = 'Status: ' . ($quiz->is_active ? 'Active' : 'Inactive');
        $lines[] = 'Questions: ' . $quiz->questions->count();
        $lines[] = 'Outcomes: ' . $quiz->outcomes->count();
        $lines[] = 'Exported: ' . now()->toDateTimeString();
        $lines[] = '';

        if (!empty($quiz->description)) {
            $lines[] = 'DESCRIPTION:';
            $lines[] = $quiz->description;
            $lines[] = '';
        }

        if (!empty($quiz->intro_text)) {
            $lines[] = 'INTRO TEXT:';
            $lines[] = $quiz->intro_text;
            $lines[] = '';
        }

        $lines[] = '================================================================';
        $lines[] = 'SLIDES (in order)';
        $lines[] = '================================================================';
        $lines[] = '';

        foreach ($quiz->questions as $i => $q) {
            $num = $i + 1;
            $lines[] = "---------- SLIDE #{$num} ----------";
            $lines[] = "ID: {$q->id}";
            $lines[] = "Order: {$q->order}";
            $lines[] = "Type: {$q->slide_type}";
            if (!empty($q->marketing_property)) {
                $lines[] = "Marketing Property: {$q->marketing_property}";
            }

            if (!empty($q->question_text)) {
                $lines[] = "Question: " . trim($q->question_text);
            }
            if (!empty($q->question_subtext)) {
                $lines[] = "Subtext: " . trim($q->question_subtext);
            }
            if (!empty($q->content_title)) {
                $lines[] = "Title: " . trim($q->content_title);
            }
            if (!empty($q->content_body)) {
                $lines[] = "Body: " . trim($q->content_body);
            }
            if (!empty($q->content_source)) {
                $lines[] = "Source: " . trim($q->content_source);
            }
            if (!empty($q->cta_text)) {
                $lines[] = "CTA: {$q->cta_text}" . ($q->cta_url ? " -> {$q->cta_url}" : '');
            }

            // Options
            if (!empty($q->options) && is_array($q->options)) {
                $lines[] = "Options:";
                foreach ($q->options as $idx => $opt) {
                    $label = $opt['label'] ?? $opt['text'] ?? '(no label)';
                    $value = $opt['value'] ?? $opt['id'] ?? '';
                    $skip = $opt['skip_to_question'] ?? null;
                    $scores = [];
                    foreach (['tof', 'mof', 'bof'] as $seg) {
                        if (!empty($opt["score_{$seg}"])) $scores[] = strtoupper($seg) . ':' . $opt["score_{$seg}"];
                    }
                    $parts = [];
                    $parts[] = ($idx + 1) . '. ' . $label;
                    $parts[] = "value={$value}";
                    if ($skip) $parts[] = "skip_to_slide={$skip}";
                    if ($scores) $parts[] = 'scores=[' . implode(',', $scores) . ']';
                    if (!empty($opt['marketing_value'])) $parts[] = "mkt={$opt['marketing_value']}";
                    $lines[] = '  ' . implode(' | ', $parts);
                }
            }

            // Show conditions
            if (!empty($q->show_conditions)) {
                $conds = $q->show_conditions['conditions'] ?? [];
                if ($conds) {
                    $parts = [];
                    foreach ($conds as $c) {
                        $parts[] = "slide_{$c['question_id']}={$c['option_value']}";
                    }
                    $type = $q->show_conditions['type'] ?? 'and';
                    $lines[] = "Show when: " . implode(" {$type} ", $parts);
                }
            }

            // Skip to
            if (!empty($q->skip_to_question)) {
                $lines[] = "Default skip_to_slide: {$q->skip_to_question}";
            }

            // Dynamic content map summary
            if (!empty($q->dynamic_content_key) && is_array($q->dynamic_content_map)) {
                $lines[] = "Dynamic content (key={$q->dynamic_content_key}):";
                foreach ($q->dynamic_content_map as $key => $content) {
                    $title = $content['title'] ?? '';
                    $body = $content['body'] ?? '';
                    $lines[] = "  [{$key}] {$title}";
                    if ($body) $lines[] = "    -> " . str_replace("\n", ' ', trim($body));
                }
            }

            $lines[] = '';
        }

        // Outcomes
        if ($quiz->outcomes->count()) {
            $lines[] = '================================================================';
            $lines[] = 'OUTCOMES';
            $lines[] = '================================================================';
            $lines[] = '';
            foreach ($quiz->outcomes->sortBy('priority') as $o) {
                $lines[] = "---------- OUTCOME: {$o->name} ----------";
                $lines[] = "ID: {$o->id} | Priority: {$o->priority}";
                if (!empty($o->conditions)) {
                    $lines[] = "Conditions: " . json_encode($o->conditions, JSON_UNESCAPED_SLASHES);
                }
                if (!empty($o->result_title)) $lines[] = "Result title: {$o->result_title}";
                if (!empty($o->result_message)) $lines[] = "Result message: " . trim($o->result_message);
                if (!empty($o->recommended_peptide_id)) $lines[] = "Recommended peptide ID: {$o->recommended_peptide_id}";
                if (!empty($o->marketing_event)) $lines[] = "Marketing event: {$o->marketing_event}";
                $lines[] = '';
            }
        }

        $content = implode("\n", $lines);
        $filename = 'quiz-' . $quiz->slug . '-' . now()->format('Ymd-His') . '.txt';

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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
