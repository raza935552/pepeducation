<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Models\UserEvent;
use App\Models\QuizResponse;
use App\Models\OutboundClick;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JourneyController extends Controller
{
    /**
     * Get full journey data for a session
     *
     * GET /api/journey/{session_id}?key={api_key}
     */
    public function show(Request $request, string $sessionId): JsonResponse
    {
        // Validate API key
        if (!$this->validateApiKey($request)) {
            return response()->json([
                'error' => 'Invalid or missing API key',
                'code' => 'INVALID_API_KEY'
            ], 401);
        }

        // Find session
        $session = UserSession::where('session_id', $sessionId)->first();

        if (!$session) {
            return response()->json([
                'error' => 'Session not found',
                'code' => 'SESSION_NOT_FOUND'
            ], 404);
        }

        return response()->json($this->formatJourneyData($session));
    }

    /**
     * Validate the API key from request
     */
    protected function validateApiKey(Request $request): bool
    {
        $providedKey = $request->query('key') ?? $request->header('X-API-Key');

        if (!$providedKey) {
            return false;
        }

        $storedKey = Setting::getValue('integrations', 'journey_api_key');

        return $storedKey && hash_equals($storedKey, $providedKey);
    }

    /**
     * Format all journey data for response
     */
    protected function formatJourneyData(UserSession $session): array
    {
        $events = $this->getSessionEvents($session);
        $quizData = $this->getQuizData($session);
        $outboundClick = $this->getOutboundClick($session);

        return [
            'session_id' => $session->session_id,
            'status' => 'success',

            // Segment & Scoring
            'segment' => $session->segment,
            'segment_label' => $this->getSegmentLabel($session->segment),
            'engagement_score' => $session->engagement_score ?? 0,

            // Session Stats
            'stats' => [
                'pages_viewed' => $session->pages_viewed ?? 0,
                'events_count' => $session->events_count ?? 0,
                'duration_seconds' => $session->duration_seconds,
                'duration_formatted' => $this->formatDuration($session->duration_seconds),
                'avg_scroll_depth' => $session->avg_scroll_depth ?? 0,
                'is_returning' => $session->is_returning ?? false,
                'session_number' => $session->session_number ?? 1,
            ],

            // Traffic Source
            'source' => [
                'referrer' => $session->referrer,
                'referrer_domain' => $session->referrer_domain,
                'utm_source' => $session->utm_source,
                'utm_medium' => $session->utm_medium,
                'utm_campaign' => $session->utm_campaign,
                'utm_content' => $session->utm_content,
                'utm_term' => $session->utm_term,
                'entry_url' => $session->entry_url,
            ],

            // Device Info
            'device' => [
                'type' => $session->device_type,
                'browser' => $session->browser,
                'os' => $session->os,
                'is_mobile' => $session->is_mobile ?? false,
            ],

            // Location
            'location' => [
                'country' => $session->country_name ?? $session->country,
                'region' => $session->region,
                'city' => $session->city,
            ],

            // Quiz Data
            'quiz' => $quizData,

            // Journey Timeline
            'journey' => $events,

            // Timestamps
            'timestamps' => [
                'started_at' => $session->started_at?->toIso8601String(),
                'ended_at' => $session->ended_at?->toIso8601String(),
                'clicked_shop_at' => $outboundClick?->created_at?->toIso8601String(),
            ],
        ];
    }

    /**
     * Get all events for the session as timeline
     */
    protected function getSessionEvents(UserSession $session): array
    {
        $events = UserEvent::where('session_id', $session->session_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return $events->map(function ($event) {
            return [
                'time' => $event->created_at->format('g:i A'),
                'timestamp' => $event->created_at->toIso8601String(),
                'type' => $event->event_type,
                'action' => $this->formatEventAction($event),
                'details' => $event->properties,
                'engagement_points' => $event->engagement_points ?? 0,
            ];
        })->toArray();
    }

    /**
     * Format event into human-readable action
     */
    protected function formatEventAction(UserEvent $event): string
    {
        $props = $event->properties ?? [];

        return match($event->event_type) {
            'page_view' => 'Viewed: ' . ($props['title'] ?? $props['url'] ?? 'Page'),
            'scroll' => 'Scrolled to ' . ($props['depth'] ?? $props['percent'] ?? '?') . '%',
            'click' => 'Clicked: ' . ($props['text'] ?? $props['element'] ?? 'Element'),
            'quiz_start' => 'Started quiz: ' . ($props['quiz_name'] ?? 'Quiz'),
            'quiz_complete' => 'Completed quiz - ' . ($props['segment'] ?? 'Unknown') . ' segment',
            'quiz_answer' => 'Answered: ' . ($props['question'] ?? 'Question'),
            'outbound_click' => 'Clicked shop link: ' . ($props['link_name'] ?? 'Link'),
            'form_submit' => 'Submitted form: ' . ($props['form_name'] ?? 'Form'),
            'video_play' => 'Played video: ' . ($props['title'] ?? 'Video'),
            'download' => 'Downloaded: ' . ($props['file_name'] ?? 'File'),
            default => ucfirst(str_replace('_', ' ', $event->event_type)),
        };
    }

    /**
     * Get quiz response data if exists
     */
    protected function getQuizData(UserSession $session): ?array
    {
        $response = QuizResponse::where('session_id', $session->session_id)
            ->with('quiz')
            ->first();

        if (!$response) {
            return null;
        }

        return [
            'completed' => $response->status === 'completed',
            'quiz_name' => $response->quiz?->name ?? $response->quiz?->title,
            'segment' => $response->segment,
            'segment_label' => $this->getSegmentLabel($response->segment),
            'scores' => [
                'tof' => $response->score_tof ?? 0,
                'mof' => $response->score_mof ?? 0,
                'bof' => $response->score_bof ?? 0,
            ],
            'answers' => $this->formatQuizAnswers($response),
            'completed_at' => $response->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Format quiz answers for display
     */
    protected function formatQuizAnswers(QuizResponse $response): array
    {
        $answers = $response->answers ?? [];
        $formatted = [];

        foreach ($answers as $answer) {
            $formatted[] = [
                'question' => $answer['question_text'] ?? $answer['question'] ?? 'Question',
                'answer' => $answer['option_text'] ?? $answer['answer'] ?? 'Answer',
            ];
        }

        return $formatted;
    }

    /**
     * Get outbound click data
     */
    protected function getOutboundClick(UserSession $session): ?OutboundClick
    {
        return OutboundClick::where('session_id', $session->session_id)
            ->latest()
            ->first();
    }

    /**
     * Get human-readable segment label
     */
    protected function getSegmentLabel(?string $segment): string
    {
        return match(strtolower($segment ?? '')) {
            'tof' => 'Top of Funnel - Just Exploring',
            'mof' => 'Middle of Funnel - Researching',
            'bof' => 'Bottom of Funnel - Ready to Buy',
            default => 'Unknown',
        };
    }

    /**
     * Format duration in seconds to human readable
     */
    protected function formatDuration(?int $seconds): string
    {
        if (!$seconds) {
            return '0 seconds';
        }

        if ($seconds < 60) {
            return $seconds . ' seconds';
        }

        $minutes = floor($seconds / 60);
        $remaining = $seconds % 60;

        if ($minutes < 60) {
            return $minutes . ' min ' . $remaining . ' sec';
        }

        $hours = floor($minutes / 60);
        $remaining = $minutes % 60;

        return $hours . 'h ' . $remaining . 'm';
    }
}
