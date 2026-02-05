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
            'slug' => 'nullable|string|max:255',
            'headline' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'image' => 'nullable|string',
            'min_score' => 'nullable|integer|min:0',
            'segment' => 'nullable|in:tof,mof,bof',
            'recommended_peptides' => 'nullable|array',
            'cta_text' => 'nullable|string|max:255',
            'cta_url' => 'nullable|string',
            'klaviyo_properties' => 'nullable|array',
        ]);

        $outcome = $quiz->outcomes()->create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: \Str::slug($validated['name']),
            'headline' => $validated['headline'],
            'body' => $validated['body'],
            'image' => $validated['image'],
            'min_score' => $validated['min_score'] ?? 0,
            'segment' => $validated['segment'],
            'recommended_peptides' => $validated['recommended_peptides'] ?? [],
            'cta_text' => $validated['cta_text'] ?? 'Continue',
            'cta_url' => $validated['cta_url'],
            'klaviyo_properties' => $validated['klaviyo_properties'] ?? [],
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
            'slug' => 'nullable|string|max:255',
            'headline' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'image' => 'nullable|string',
            'min_score' => 'nullable|integer|min:0',
            'segment' => 'nullable|in:tof,mof,bof',
            'recommended_peptides' => 'nullable|array',
            'cta_text' => 'nullable|string|max:255',
            'cta_url' => 'nullable|string',
            'klaviyo_properties' => 'nullable|array',
        ]);

        $outcome->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: \Str::slug($validated['name']),
            'headline' => $validated['headline'],
            'body' => $validated['body'],
            'image' => $validated['image'],
            'min_score' => $validated['min_score'] ?? 0,
            'segment' => $validated['segment'],
            'recommended_peptides' => $validated['recommended_peptides'] ?? [],
            'cta_text' => $validated['cta_text'],
            'cta_url' => $validated['cta_url'],
            'klaviyo_properties' => $validated['klaviyo_properties'] ?? [],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'outcome' => $outcome]);
        }

        return back()->with('success', 'Outcome updated.');
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
