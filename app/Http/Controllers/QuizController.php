<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
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
}
