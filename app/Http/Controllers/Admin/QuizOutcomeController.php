<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizOutcome;
use Illuminate\Http\Request;

class QuizOutcomeController extends Controller
{
    public function store(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'condition_type' => 'nullable|in:segment,score,answer',
            'segment' => 'nullable|in:tof,mof,bof',
            'min_score' => 'nullable|integer|min:0',
            'score_type' => 'nullable|string|max:50',
            'answer_question' => 'nullable|string|max:255',
            'answer_value' => 'nullable|string|max:255',
            'result_title' => 'nullable|string|max:255',
            'result_message' => 'nullable|string',
            'result_image' => 'nullable|string',
            'redirect_url' => 'nullable|string',
            'redirect_type' => 'nullable|in:internal,external,product',
            'recommended_peptide_id' => 'nullable|exists:peptides,id',
            'product_link' => 'nullable|string',
            'klaviyo_properties' => 'nullable|array',
        ]);

        $outcome = $quiz->outcomes()->create([
            'name' => $validated['name'],
            'conditions' => $this->buildConditions($validated),
            'result_title' => $validated['result_title'] ?? null,
            'result_message' => $validated['result_message'] ?? null,
            'result_image' => $validated['result_image'] ?? null,
            'redirect_url' => $validated['redirect_url'] ?? null,
            'redirect_type' => $validated['redirect_type'] ?? 'internal',
            'recommended_peptide_id' => $validated['recommended_peptide_id'] ?? null,
            'product_link' => $validated['product_link'] ?? null,
            'klaviyo_properties' => $validated['klaviyo_properties'] ?? [],
            'priority' => $quiz->outcomes()->max('priority') + 1,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'outcome' => $outcome]);
        }

        return back()->with('success', 'Outcome added.');
    }

    public function update(Request $request, Quiz $quiz, QuizOutcome $outcome)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'condition_type' => 'nullable|in:segment,score,answer',
            'segment' => 'nullable|in:tof,mof,bof',
            'min_score' => 'nullable|integer|min:0',
            'score_type' => 'nullable|string|max:50',
            'answer_question' => 'nullable|string|max:255',
            'answer_value' => 'nullable|string|max:255',
            'result_title' => 'nullable|string|max:255',
            'result_message' => 'nullable|string',
            'result_image' => 'nullable|string',
            'redirect_url' => 'nullable|string',
            'redirect_type' => 'nullable|in:internal,external,product',
            'recommended_peptide_id' => 'nullable|exists:peptides,id',
            'product_link' => 'nullable|string',
            'klaviyo_properties' => 'nullable|array',
        ]);

        $outcome->update([
            'name' => $validated['name'],
            'conditions' => $this->buildConditions($validated),
            'result_title' => $validated['result_title'] ?? null,
            'result_message' => $validated['result_message'] ?? null,
            'result_image' => $validated['result_image'] ?? null,
            'redirect_url' => $validated['redirect_url'] ?? null,
            'redirect_type' => $validated['redirect_type'] ?? 'internal',
            'recommended_peptide_id' => $validated['recommended_peptide_id'] ?? null,
            'product_link' => $validated['product_link'] ?? null,
            'klaviyo_properties' => $validated['klaviyo_properties'] ?? [],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'outcome' => $outcome]);
        }

        return back()->with('success', 'Outcome updated.');
    }

    private function buildConditions(array $validated): array
    {
        $type = $validated['condition_type'] ?? null;

        return match ($type) {
            'segment' => [
                'type' => 'segment',
                'segment' => $validated['segment'] ?? 'tof',
            ],
            'score' => [
                'type' => 'score',
                'min_score' => $validated['min_score'] ?? 0,
                'score_type' => $validated['score_type'] ?? 'total',
            ],
            'answer' => [
                'type' => 'answer',
                'question' => $validated['answer_question'] ?? null,
                'value' => $validated['answer_value'] ?? null,
            ],
            default => [],
        };
    }

    public function destroy(Quiz $quiz, QuizOutcome $outcome)
    {
        $outcome->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Outcome deleted.');
    }
}
