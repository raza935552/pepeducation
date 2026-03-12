<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizResponse;
use App\Models\Subscriber;
use App\Services\Klaviyo\KlaviyoService;
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
    public function abandon(Request $request, KlaviyoService $klaviyo)
    {
        $responseId = $request->input('response_id');
        if (!$responseId) {
            return response()->json(['ok' => false], 422);
        }

        $response = QuizResponse::where('id', $responseId)
            ->where('status', 'in_progress')
            ->with('subscriber', 'quiz')
            ->first();

        if (!$response) {
            return response()->json(['ok' => false], 404);
        }

        // Link subscriber from pp_email cookie if not already linked
        if (!$response->subscriber_id) {
            $email = $request->cookie('pp_email');
            if ($email) {
                $subscriber = Subscriber::where('email', strtolower(trim($email)))->first();
                if ($subscriber) {
                    $response->subscriber_id = $subscriber->id;
                    $response->email = $subscriber->email;
                }
            }
        }

        $response->update([
            'status' => 'abandoned',
            'subscriber_id' => $response->subscriber_id,
            'email' => $response->email,
            'updated_at' => now(),
        ]);

        // Fire Klaviyo "Quiz Abandoned" event if subscriber exists
        $subscriber = $response->subscriber_id ? ($response->subscriber ?? Subscriber::find($response->subscriber_id)) : null;
        if ($subscriber && $klaviyo->isEnabled()) {
            try {
                $klaviyo->trackQuizAbandoned($subscriber, $response);
            } catch (\Exception $e) {
                // Beacon responses must be fast — don't block on errors
            }
        }

        return response()->json(['ok' => true]);
    }
}
