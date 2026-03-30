<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizQuestionController extends Controller
{
    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate($this->slideRules($request));

        // Determine insertion order
        $insertAfter = $request->input('insert_after'); // slide order to insert after
        if ($insertAfter !== null && $insertAfter !== '') {
            $insertAfter = (int) $insertAfter;
            // Shift all slides after this position down by 1
            $quiz->questions()->where('order', '>', $insertAfter)->increment('order');
            $newOrder = $insertAfter + 1;
        } else {
            $newOrder = ($quiz->questions()->max('order') ?? 0) + 1;
        }

        // Auto-set show_conditions from segment parameter
        $showConditions = $this->parseShowConditions($request);
        $segment = $request->input('segment');
        if ($segment && in_array($segment, ['tof', 'mof', 'bof']) && !$showConditions) {
            $showConditions = $this->buildSegmentConditions($quiz, $segment);
        }

        $klaviyoProperty = $validated['klaviyo_property'] ?? null;
        if (empty($klaviyoProperty) && ($validated['slide_type'] ?? '') === 'email_capture') {
            $klaviyoProperty = 'email';
        }

        $question = $quiz->questions()->create([
            'slide_type' => $validated['slide_type'] ?? 'question',
            'question_text' => $validated['question_text'] ?? $validated['content_title'] ?? 'Slide',
            'question_subtext' => $validated['question_subtext'] ?? null,
            'question_type' => $validated['question_type'] ?? 'single_choice',
            'order' => $newOrder,
            'options' => $validated['options'] ?? [],
            'klaviyo_property' => $klaviyoProperty,
            'is_required' => $validated['is_required'] ?? true,
            'max_selections' => $validated['max_selections'] ?? null,
            'settings' => $validated['settings'] ?? null,
            'show_conditions' => $showConditions,
            'content_title' => $validated['content_title'] ?? null,
            'content_body' => $validated['content_body'] ?? null,
            'content_source' => $validated['content_source'] ?? null,
            'auto_advance_seconds' => $validated['auto_advance_seconds'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'skip_to_question' => $validated['skip_to_question'] ?? null,
            'dynamic_content_key' => $validated['dynamic_content_key'] ?? null,
            'dynamic_content_map' => $this->parseDynamicContentMap($request),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'question' => $question]);
        }

        return back()->with('success', 'Slide added.');
    }

    public function update(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $validated = $request->validate($this->slideRules($request));

        $klaviyoProperty = $validated['klaviyo_property'] ?? null;
        if (empty($klaviyoProperty) && ($validated['slide_type'] ?? $question->slide_type) === 'email_capture') {
            $klaviyoProperty = 'email';
        }

        $question->update([
            'slide_type' => $validated['slide_type'] ?? $question->slide_type,
            'question_text' => $validated['question_text'] ?? $validated['content_title'] ?? $question->question_text,
            'question_subtext' => $validated['question_subtext'] ?? null,
            'question_type' => $validated['question_type'] ?? $question->question_type,
            'options' => $validated['options'] ?? [],
            'klaviyo_property' => $klaviyoProperty,
            'is_required' => $validated['is_required'] ?? true,
            'max_selections' => $validated['max_selections'] ?? null,
            'settings' => $validated['settings'] ?? null,
            'show_conditions' => $this->parseShowConditions($request),
            'content_title' => $validated['content_title'] ?? null,
            'content_body' => $validated['content_body'] ?? null,
            'content_source' => $validated['content_source'] ?? null,
            'auto_advance_seconds' => $validated['auto_advance_seconds'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'skip_to_question' => $validated['skip_to_question'] ?? null,
            'dynamic_content_key' => $validated['dynamic_content_key'] ?? null,
            'dynamic_content_map' => $this->parseDynamicContentMap($request),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'question' => $question]);
        }

        return back()->with('success', 'Slide updated.');
    }

    public function destroy(Quiz $quiz, QuizQuestion $question)
    {
        // Check for cascade impacts before deleting
        if (request()->wantsJson() && !request()->boolean('confirmed')) {
            $quiz->loadMissing(['questions', 'outcomes']);
            $warnings = $this->checkDeleteImpact($quiz, $question);

            if (!empty($warnings)) {
                return response()->json([
                    'needs_confirmation' => true,
                    'warnings' => $warnings,
                ]);
            }
        }

        $question->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Question deleted.');
    }

    private function checkDeleteImpact(Quiz $quiz, QuizQuestion $question): array
    {
        $warnings = [];
        $slideId = $question->id;

        // 1. Other slides with show_conditions referencing this slide
        foreach ($quiz->questions as $otherQuestion) {
            if ($otherQuestion->id === $slideId) continue;

            $conditions = $otherQuestion->show_conditions['conditions'] ?? [];
            foreach ($conditions as $cond) {
                if (($cond['question_id'] ?? null) == $slideId) {
                    $warnings[] = [
                        'type' => 'show_condition',
                        'message' => 'Slide #' . $otherQuestion->order . ' "' . \Str::limit($otherQuestion->question_text ?: $otherQuestion->content_title ?: 'Untitled', 40) . '" has a show condition referencing this slide',
                    ];
                    break;
                }
            }
        }

        // 2. Options on other slides with skip_to_question pointing here
        foreach ($quiz->questions as $otherQuestion) {
            if ($otherQuestion->id === $slideId) continue;

            // Slide-level skip_to_question
            if (($otherQuestion->skip_to_question ?? null) == $slideId) {
                $warnings[] = [
                    'type' => 'skip_to',
                    'message' => 'Slide #' . $otherQuestion->order . ' "' . \Str::limit($otherQuestion->question_text ?: $otherQuestion->content_title ?: 'Untitled', 40) . '" jumps to this slide',
                ];
                continue;
            }

            // Option-level skip_to_question
            foreach ($otherQuestion->options ?? [] as $option) {
                if (($option['skip_to_question'] ?? null) == $slideId) {
                    $warnings[] = [
                        'type' => 'skip_to',
                        'message' => 'Slide #' . $otherQuestion->order . ' "' . \Str::limit($otherQuestion->question_text ?: $otherQuestion->content_title ?: 'Untitled', 40) . '" has an option that skips to this slide',
                    ];
                    break;
                }
            }
        }

        // 3. Outcomes with answer conditions matching this slide's klaviyo_property
        if ($question->klaviyo_property) {
            foreach ($quiz->outcomes as $outcome) {
                $conditions = $outcome->conditions ?? [];
                if (($conditions['type'] ?? null) === 'answer' && ($conditions['question'] ?? null) === $question->klaviyo_property) {
                    $warnings[] = [
                        'type' => 'outcome',
                        'message' => 'Outcome "' . $outcome->name . '" has an answer condition on "' . $question->klaviyo_property . '"',
                    ];
                }
            }
        }

        return $warnings;
    }

    public function duplicate(Quiz $quiz, QuizQuestion $question)
    {
        $maxOrder = $quiz->questions()->max('order') ?? 0;

        $clone = $question->replicate();
        $clone->order = $maxOrder + 1;
        $clone->question_text = ($clone->question_text ?? 'Slide') . ' (copy)';
        if ($clone->content_title) {
            $clone->content_title = $clone->content_title . ' (copy)';
        }
        $clone->save();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'question' => $clone]);
        }

        return back()->with('success', 'Slide duplicated — all conditions, scores, and settings copied.');
    }

    public function assignSegment(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $validated = $request->validate([
            'segment' => 'required|in:shared,tof,mof,bof',
        ]);

        $segment = $validated['segment'];

        if ($segment === 'shared') {
            $question->update(['show_conditions' => null]);
            return response()->json(['success' => true, 'message' => 'Slide moved to Shared.']);
        }

        // Find the segmentation question (first question-type slide with scored options)
        $segQuestion = $quiz->questions()
            ->where('slide_type', 'question')
            ->orderBy('order')
            ->get()
            ->first(function ($q) {
                foreach ($q->options ?? [] as $opt) {
                    if (($opt['score_tof'] ?? 0) > 0 || ($opt['score_mof'] ?? 0) > 0 || ($opt['score_bof'] ?? 0) > 0) {
                        return true;
                    }
                }
                return false;
            });

        if (!$segQuestion) {
            return response()->json([
                'success' => false,
                'message' => 'No segmentation question found. Create a question with TOF/MOF/BOF scores first.',
            ], 422);
        }

        // Find which option value maps to the requested segment
        $targetValue = null;
        foreach ($segQuestion->options ?? [] as $opt) {
            $scores = [
                'tof' => (int) ($opt['score_tof'] ?? 0),
                'mof' => (int) ($opt['score_mof'] ?? 0),
                'bof' => (int) ($opt['score_bof'] ?? 0),
            ];
            $maxScore = max($scores);
            if ($maxScore > 0 && array_search($maxScore, $scores) === $segment) {
                $targetValue = $opt['value'] ?? $opt['klaviyo_value'] ?? '';
                break;
            }
        }

        if (!$targetValue) {
            return response()->json([
                'success' => false,
                'message' => "No option maps to {$segment}. Check funnel scores on the segmentation question.",
            ], 422);
        }

        $question->update([
            'show_conditions' => [
                'type' => 'and',
                'conditions' => [
                    ['question_id' => $segQuestion->id, 'option_value' => $targetValue],
                ],
            ],
        ]);

        $segmentLabels = ['tof' => 'TOF', 'mof' => 'MOF', 'bof' => 'BOF'];
        return response()->json([
            'success' => true,
            'message' => "Slide assigned to {$segmentLabels[$segment]} path.",
        ]);
    }

    public function move(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $validated = $request->validate([
            'order' => 'required|integer|min:1',
        ]);

        $targetOrder = $validated['order'];
        $currentOrder = $question->order;
        $maxOrder = $quiz->questions()->max('order') ?? 1;
        $targetOrder = min($targetOrder, $maxOrder);

        if ($targetOrder === $currentOrder) {
            return response()->json(['success' => true, 'message' => 'Already at that position.']);
        }

        if ($targetOrder < $currentOrder) {
            // Moving up: shift slides between target and current down by 1
            $quiz->questions()
                ->where('order', '>=', $targetOrder)
                ->where('order', '<', $currentOrder)
                ->increment('order');
        } else {
            // Moving down: shift slides between current and target up by 1
            $quiz->questions()
                ->where('order', '>', $currentOrder)
                ->where('order', '<=', $targetOrder)
                ->decrement('order');
        }

        $question->update(['order' => $targetOrder]);

        return response()->json(['success' => true, 'message' => "Slide moved to #{$targetOrder}."]);
    }

    public function reorder(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'integer|exists:quiz_questions,id',
        ]);

        // Use the minimum current order of the submitted slides as the starting offset
        // so partial-list reorders (e.g. only MOF slides) don't collide with other segments
        $startOrder = QuizQuestion::where('quiz_id', $quiz->id)
            ->whereIn('id', $validated['questions'])
            ->min('order') ?? 1;

        foreach ($validated['questions'] as $index => $questionId) {
            QuizQuestion::where('id', $questionId)
                ->where('quiz_id', $quiz->id)
                ->update(['order' => $startOrder + $index]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Build validation rules based on slide_type.
     */
    private function slideRules(Request $request): array
    {
        $slideType = $request->input('slide_type', 'question');
        $validSlideTypes = implode(',', QuizQuestion::SLIDE_TYPES);

        $rules = [
            'slide_type' => "required|string|in:{$validSlideTypes}",
            'klaviyo_property' => 'nullable|string|max:255',
            'insert_after' => 'nullable|integer|min:0',
            'segment' => 'nullable|string|in:shared,tof,mof,bof',
            'is_required' => 'boolean',
            'content_title' => 'nullable|string|max:500',
            'content_body' => 'nullable|string|max:5000',
            'content_source' => 'nullable|string|max:500',
            'auto_advance_seconds' => 'nullable|integer|min:1|max:30',
            'cta_text' => 'nullable|string|max:255',
            'cta_url' => 'nullable|string|max:2048',
            'skip_to_question' => 'nullable|integer',
            // New fields
            'question_subtext' => 'nullable|string|max:500',
            'max_selections' => 'nullable|integer|min:1|max:20',
            'settings' => 'nullable|array',
            'settings.placeholder' => 'nullable|string|max:200',
            'settings.doctor_heading' => 'nullable|string|max:500',
            'settings.doctor_description' => 'nullable|string|max:500',
            'settings.doctor_unavailable_text' => 'nullable|string|max:500',
            'settings.research_heading' => 'nullable|string|max:500',
            'settings.research_description' => 'nullable|string|max:500',
            'settings.research_unavailable_text' => 'nullable|string|max:500',
            // Accordion sections (universal)
            'settings.accordion_items' => 'nullable|array',
            'settings.accordion_items.*.title' => 'required_with:settings.accordion_items|string|max:200',
            'settings.accordion_items.*.content' => 'required_with:settings.accordion_items|string|max:2000',
            // Peptide reveal settings
            'settings.pre_headline' => 'nullable|string|max:500',
            'settings.benefits_heading' => 'nullable|string|max:500',
            'settings.fallback_headline' => 'nullable|string|max:500',
            'settings.fallback_body' => 'nullable|string|max:2000',
            // Dynamic content
            'dynamic_content_key' => 'nullable|string|max:255',
            'dynamic_variants' => 'nullable|array',
            'dynamic_variants.*.key' => 'nullable|string|max:255',
            'dynamic_variants.*.title' => 'nullable|string|max:500',
            'dynamic_variants.*.body' => 'nullable|string|max:5000',
            // Show conditions
            'show_conditions_type' => 'nullable|in:and,or',
            'show_conditions_question_id' => 'nullable|array',
            'show_conditions_question_id.*' => 'nullable|integer',
            'show_conditions_option_value' => 'nullable|array',
            'show_conditions_option_value.*' => 'nullable|string',
        ];

        if (in_array($slideType, ['question', 'question_text'])) {
            $rules['question_text'] = 'required|string';
            $rules['question_type'] = 'required|in:single_choice,multiple_choice,text,email,scale';
        } else {
            $rules['question_text'] = 'nullable|string';
            $rules['question_type'] = 'nullable|in:single_choice,multiple_choice,text,email,scale';
        }

        if ($slideType === 'question' && in_array($request->input('question_type'), ['single_choice', 'multiple_choice'])) {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.value'] = 'required|string';
            $rules['options.*.label'] = 'required|string';
            $rules['options.*.klaviyo_value'] = 'nullable|string';
            $rules['options.*.score_tof'] = 'nullable|integer';
            $rules['options.*.score_mof'] = 'nullable|integer';
            $rules['options.*.score_bof'] = 'nullable|integer';
            $rules['options.*.skip_to_question'] = 'nullable|integer';
            $rules['options.*.subtext'] = 'nullable|string|max:200';
            $rules['options.*.tags'] = 'nullable|array';
            $rules['options.*.tags.*'] = 'nullable|string|max:100';
        } else {
            $rules['options'] = 'nullable|array';
        }

        return $rules;
    }

    /**
     * Parse dynamic_variants array from form into the dynamic_content_map JSON format.
     */
    private function parseDynamicContentMap(Request $request): ?array
    {
        $variants = $request->input('dynamic_variants', []);
        if (empty($variants)) return null;

        $map = [];
        foreach ($variants as $v) {
            $key = trim($v['key'] ?? '');
            if ($key) {
                $map[$key] = [
                    'title' => $v['title'] ?? '',
                    'body' => $v['body'] ?? '',
                ];
            }
        }

        return !empty($map) ? $map : null;
    }

    /**
     * Parse show_conditions from flat form inputs into structured JSON.
     */
    private function parseShowConditions(Request $request): ?array
    {
        $type = $request->input('show_conditions_type');
        $questionIds = $request->input('show_conditions_question_id', []);
        $optionValues = $request->input('show_conditions_option_value', []);

        if (!$type || empty($questionIds)) {
            return null;
        }

        $conditions = [];
        foreach ($questionIds as $i => $qId) {
            if ($qId && !empty($optionValues[$i] ?? '')) {
                $conditions[] = [
                    'question_id' => (int) $qId,
                    'option_value' => $optionValues[$i],
                ];
            }
        }

        if (empty($conditions)) {
            return null;
        }

        return [
            'type' => $type,
            'conditions' => $conditions,
        ];
    }

    /**
     * Build show_conditions for a given segment (tof/mof/bof) by finding
     * the segmentation question and mapping the segment to its option value.
     */
    private function buildSegmentConditions(Quiz $quiz, string $segment): ?array
    {
        $segQuestion = $quiz->questions()
            ->where('slide_type', 'question')
            ->orderBy('order')
            ->get()
            ->first(function ($q) {
                foreach ($q->options ?? [] as $opt) {
                    if (($opt['score_tof'] ?? 0) > 0 || ($opt['score_mof'] ?? 0) > 0 || ($opt['score_bof'] ?? 0) > 0) {
                        return true;
                    }
                }
                return false;
            });

        if (!$segQuestion) return null;

        foreach ($segQuestion->options ?? [] as $opt) {
            $scores = [
                'tof' => (int) ($opt['score_tof'] ?? 0),
                'mof' => (int) ($opt['score_mof'] ?? 0),
                'bof' => (int) ($opt['score_bof'] ?? 0),
            ];
            $maxScore = max($scores);
            if ($maxScore > 0 && array_search($maxScore, $scores) === $segment) {
                return [
                    'type' => 'and',
                    'conditions' => [
                        ['question_id' => $segQuestion->id, 'option_value' => $opt['value'] ?? ''],
                    ],
                ];
            }
        }

        return null;
    }
}
