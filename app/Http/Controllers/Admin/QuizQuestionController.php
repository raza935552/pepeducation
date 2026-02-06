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
        $rules = [
            'question_text' => 'required|string',
            'question_type' => 'required|in:single_choice,multiple_choice,text,email,scale',
            'options' => 'nullable|array',
            'options.*.value' => 'required|string',
            'options.*.label' => 'required|string',
            'options.*.klaviyo_value' => 'nullable|string',
            'options.*.score_tof' => 'nullable|integer',
            'options.*.score_mof' => 'nullable|integer',
            'options.*.score_bof' => 'nullable|integer',
            'klaviyo_property' => 'nullable|string|max:255',
            'is_required' => 'boolean',
        ];

        if (in_array($request->input('question_type'), ['single_choice', 'multiple_choice'])) {
            $rules['options'] = 'required|array|min:2';
        }

        $validated = $request->validate($rules);

        $maxOrder = $quiz->questions()->max('order') ?? 0;

        $question = $quiz->questions()->create([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'order' => $maxOrder + 1,
            'options' => $validated['options'] ?? [],
            'klaviyo_property' => $validated['klaviyo_property'],
            'is_required' => $validated['is_required'] ?? true,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'question' => $question]);
        }

        return back()->with('success', 'Question added.');
    }

    public function update(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $rules = [
            'question_text' => 'required|string',
            'question_type' => 'required|in:single_choice,multiple_choice,text,email,scale',
            'options' => 'nullable|array',
            'klaviyo_property' => 'nullable|string|max:255',
            'is_required' => 'boolean',
        ];

        if (in_array($request->input('question_type'), ['single_choice', 'multiple_choice'])) {
            $rules['options'] = 'required|array|min:2';
        }

        $validated = $request->validate($rules);

        $question->update([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'options' => $validated['options'] ?? [],
            'klaviyo_property' => $validated['klaviyo_property'],
            'is_required' => $validated['is_required'] ?? true,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'question' => $question]);
        }

        return back()->with('success', 'Question updated.');
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
}
