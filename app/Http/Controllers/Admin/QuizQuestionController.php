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

        $maxOrder = $quiz->questions()->max('order') ?? 0;

        $question = $quiz->questions()->create([
            'slide_type' => $validated['slide_type'] ?? 'question',
            'question_text' => $validated['question_text'] ?? $validated['content_title'] ?? 'Slide',
            'question_type' => $validated['question_type'] ?? 'single_choice',
            'order' => $maxOrder + 1,
            'options' => $validated['options'] ?? [],
            'klaviyo_property' => $validated['klaviyo_property'] ?? null,
            'is_required' => $validated['is_required'] ?? true,
            'show_conditions' => $this->parseShowConditions($request),
            'content_title' => $validated['content_title'] ?? null,
            'content_body' => $validated['content_body'] ?? null,
            'content_source' => $validated['content_source'] ?? null,
            'auto_advance_seconds' => $validated['auto_advance_seconds'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'question' => $question]);
        }

        return back()->with('success', 'Slide added.');
    }

    public function update(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $validated = $request->validate($this->slideRules($request));

        $question->update([
            'slide_type' => $validated['slide_type'] ?? $question->slide_type,
            'question_text' => $validated['question_text'] ?? $validated['content_title'] ?? $question->question_text,
            'question_type' => $validated['question_type'] ?? $question->question_type,
            'options' => $validated['options'] ?? [],
            'klaviyo_property' => $validated['klaviyo_property'] ?? null,
            'is_required' => $validated['is_required'] ?? true,
            'show_conditions' => $this->parseShowConditions($request),
            'content_title' => $validated['content_title'] ?? null,
            'content_body' => $validated['content_body'] ?? null,
            'content_source' => $validated['content_source'] ?? null,
            'auto_advance_seconds' => $validated['auto_advance_seconds'] ?? null,
            'cta_text' => $validated['cta_text'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'question' => $question]);
        }

        return back()->with('success', 'Slide updated.');
    }

    public function destroy(Quiz $quiz, QuizQuestion $question)
    {
        $question->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Question deleted.');
    }

    public function reorder(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'integer|exists:quiz_questions,id',
        ]);

        foreach ($validated['questions'] as $order => $questionId) {
            QuizQuestion::where('id', $questionId)
                ->where('quiz_id', $quiz->id)
                ->update(['order' => $order + 1]);
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
            'is_required' => 'boolean',
            'content_title' => 'nullable|string|max:500',
            'content_body' => 'nullable|string|max:5000',
            'content_source' => 'nullable|string|max:500',
            'auto_advance_seconds' => 'nullable|integer|min:1|max:30',
            'cta_text' => 'nullable|string|max:255',
            'cta_url' => 'nullable|string|max:2048',
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
        } else {
            $rules['options'] = 'nullable|array';
        }

        return $rules;
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
}
