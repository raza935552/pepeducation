<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Tracking\TrackingManager;
use App\Models\UserEvent;
use App\Models\TrackingError;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    protected TrackingManager $tracking;

    public function __construct(Request $request)
    {
        $this->tracking = new TrackingManager($request);
    }

    public function track(Request $request): JsonResponse
    {
        $type = $request->input('event_type');
        $data = $request->except(['event_type', 'session_id', 'timestamp']);

        match ($type) {
            'page_view' => $this->trackPageView($data),
            'click' => $this->tracking->trackClick($data),
            'cta_click' => $this->trackCTAClick($data),
            'scroll' => $this->trackScroll($data),
            'js_error' => $this->trackError($request),
            'page_exit' => $this->trackPageExit($data),
            'identify' => $this->identifyUser($data),
            'quiz_start' => $this->trackQuizStart($data),
            'quiz_answer' => $this->trackQuizAnswer($data),
            'popup_view' => $this->trackPopupView($data),
            'popup_convert' => $this->trackPopupConvert($data),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    protected function trackPageView(array $data): void
    {
        $this->tracking->trackPageView(
            $data['page_url'] ?? '',
            $data['page_title'] ?? null
        );
    }

    protected function trackScroll(array $data): void
    {
        $depth = (int) ($data['scroll_depth'] ?? 0);
        $url = $data['page_url'] ?? '';

        if ($depth > 0) {
            $this->tracking->trackScroll($depth, $url);
        }
    }

    protected function trackError(Request $request): void
    {
        $errorKey = md5(
            $request->input('error_message') .
            $request->input('page_url') .
            $request->input('error_line', '')
        );

        $existing = TrackingError::where('error_code', $errorKey)
            ->where('status', '!=', 'resolved')
            ->first();

        if ($existing) {
            $existing->incrementOccurrence();
            return;
        }

        TrackingError::create([
            'session_id' => $this->tracking->getSessionId(),
            'user_id' => auth()->id(),
            'error_type' => TrackingError::TYPE_JS_ERROR,
            'error_code' => $errorKey,
            'error_message' => $request->input('error_message', 'Unknown error'),
            'stack_trace' => $request->input('error_stack'),
            'page_url' => $request->input('page_url', ''),
            'page_title' => $request->input('page_title'),
            'component' => $request->input('error_file'),
            'browser' => $this->tracking->getSession()->browser,
            'os' => $this->tracking->getSession()->os,
            'device_type' => $this->tracking->getSession()->device_type,
            'user_action' => $request->input('user_action'),
            'first_occurred_at' => now(),
            'last_occurred_at' => now(),
        ]);
    }

    protected function trackPageExit(array $data): void
    {
        $session = $this->tracking->getSession();

        $session->update([
            'exit_url' => $data['page_url'] ?? $session->exit_url,
            'avg_scroll_depth' => max($session->avg_scroll_depth ?? 0, $data['max_scroll_depth'] ?? 0),
            'duration_seconds' => ($session->duration_seconds ?? 0) + ($data['time_on_page'] ?? 0),
        ]);
    }

    protected function identifyUser(array $data): void
    {
        if (!empty($data['email'])) {
            $subscriber = \App\Models\Subscriber::firstOrCreate(
                ['email' => $data['email']],
                ['source' => 'tracker', 'status' => 'active', 'subscribed_at' => now()]
            );
            $this->tracking->linkSubscriber($subscriber);
        }
    }

    protected function trackQuizStart(array $data): void
    {
        if (!empty($data['quiz_id'])) {
            $this->tracking->trackQuizStartLocal((int) $data['quiz_id']);
        }
    }

    protected function trackQuizAnswer(array $data): void
    {
        if (!empty($data['quiz_id']) && !empty($data['question_id'])) {
            $this->tracking->trackQuizAnswer(
                (int) $data['quiz_id'],
                (int) $data['question_id'],
                $data['answer'] ?? ''
            );
        }
    }

    protected function trackPopupView(array $data): void
    {
        if (!empty($data['popup_id'])) {
            $popup = \App\Models\Popup::find($data['popup_id']);
            if ($popup) {
                $popup->increment('views_count');
                \App\Models\PopupInteraction::create([
                    'popup_id' => $popup->id,
                    'session_id' => $this->tracking->getSessionId(),
                    'interaction_type' => 'view',
                    'created_at' => now(),
                ]);
            }
        }
    }

    protected function trackPopupConvert(array $data): void
    {
        if (!empty($data['popup_id'])) {
            $popup = \App\Models\Popup::find($data['popup_id']);
            if ($popup) {
                $popup->increment('conversions_count');
            }
        }
    }

    protected function trackCTAClick(array $data): void
    {
        UserEvent::create([
            'session_id' => $this->tracking->getSessionId(),
            'user_id' => auth()->id(),
            'event_type' => 'cta_click',
            'event_name' => $data['cta_name'] ?? 'Unknown CTA',
            'page_url' => $data['source_page'] ?? $data['page_url'] ?? '',
            'event_data' => [
                'cta_name' => $data['cta_name'] ?? null,
                'cta_type' => $data['cta_type'] ?? 'general',
                'cta_position' => $data['cta_position'] ?? null,
                'destination' => $data['destination'] ?? null,
                'element_text' => $data['element_text'] ?? null,
                'element_id' => $data['element_id'] ?? null,
                'element_class' => $data['element_class'] ?? null,
            ],
            'engagement_points' => 5, // CTAs are high-value actions
        ]);

        // Also update session engagement
        $session = $this->tracking->getSession();
        $session->increment('engagement_score', 5);
    }

    public function getSession(Request $request): JsonResponse
    {
        $session = $this->tracking->getSession();

        return response()->json([
            'session_id' => $session->session_id,
            'segment' => $session->segment,
            'engagement_score' => $session->engagement_score,
        ]);
    }
}
