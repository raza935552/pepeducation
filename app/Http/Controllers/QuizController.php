<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(string $slug)
    {
        $quiz = Quiz::where('slug', $slug)
            ->where('is_active', true)
            ->with(['questions' => fn($q) => $q->orderBy('order')])
            ->firstOrFail();

        return view('quizzes.show', compact('quiz'));
    }

    public function embed(string $slug)
    {
        $quiz = Quiz::where('slug', $slug)
            ->where('is_active', true)
            ->with(['questions' => fn($q) => $q->orderBy('order')])
            ->firstOrFail();

        return view('quizzes.embed', compact('quiz'));
    }

    /**
     * Beacon endpoint: mark an in-progress quiz response as abandoned.
     * Called via navigator.sendBeacon when user leaves mid-quiz.
     */
    public function abandon(Request $request)
    {
        $responseId = $request->input('response_id');
        if (!$responseId) {
            return response()->json(['ok' => false], 422);
        }

        QuizResponse::where('id', $responseId)
            ->where('status', 'in_progress')
            ->update(['status' => 'abandoned', 'updated_at' => now()]);

        return response()->json(['ok' => true]);
    }
}
