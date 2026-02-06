<?php

namespace App\Services\Tracking;

use App\Models\Subscriber;
use App\Models\UserSession;
use App\Models\UserEvent;
use App\Models\QuizResponse;
use App\Models\LeadMagnetDownload;
use App\Models\OutboundClick;
use App\Models\OutboundLink;
use App\Services\Tracking\Contracts\TrackingDriver;
use App\Services\Tracking\Drivers\LocalDriver;
use App\Services\Tracking\Drivers\KlaviyoDriver;
use App\Services\Tracking\Drivers\GA4Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TrackingManager
{
    protected Collection $drivers;
    protected SessionManager $sessionManager;
    protected EventRecorder $eventRecorder;
    protected ?UserSession $session = null;

    public function __construct(Request $request)
    {
        $this->sessionManager = new SessionManager($request);
        $this->eventRecorder = new EventRecorder();
        $this->drivers = collect();

        $this->registerDrivers();
    }

    protected function registerDrivers(): void
    {
        // Local driver is always registered
        $this->drivers->put('local', new LocalDriver($this->sessionManager));

        // Klaviyo driver
        $klaviyo = new KlaviyoDriver();
        if ($klaviyo->isEnabled()) {
            $this->drivers->put('klaviyo', $klaviyo);
        }

        // GA4 driver
        $ga4 = new GA4Driver();
        if ($ga4->isEnabled()) {
            $this->drivers->put('ga4', $ga4);
        }
    }

    public function getSession(): UserSession
    {
        if (!$this->session) {
            $this->session = $this->sessionManager->getOrCreateSession();
        }
        return $this->session;
    }

    public function getSessionId(): string
    {
        return $this->sessionManager->getSessionId();
    }

    // Identify user across all platforms
    public function identify(Subscriber $subscriber): void
    {
        $this->drivers->each(fn(TrackingDriver $driver) => $driver->identify($subscriber));
        $this->sessionManager->linkSubscriber($this->getSession(), $subscriber);
    }

    // Track event across all platforms
    public function track(string $eventName, array $properties = [], ?Subscriber $subscriber = null): void
    {
        $subscriber = $subscriber ?? $this->getSession()->subscriber;

        $this->drivers->each(function (TrackingDriver $driver) use ($eventName, $properties, $subscriber) {
            $driver->trackEvent($eventName, $properties, $subscriber);
        });
    }

    // Convenience methods for common events
    public function trackPageView(string $url, ?string $title = null): void
    {
        $this->drivers->each(fn(TrackingDriver $driver) => $driver->trackPageView($url, $title));
    }

    // Local-only tracking methods (not broadcast to external drivers)
    public function trackClick(array $data): UserEvent
    {
        return $this->eventRecorder->record($this->getSession(), UserEvent::TYPE_CLICK, $data);
    }

    public function trackCTAClick(array $data): UserEvent
    {
        return $this->eventRecorder->record($this->getSession(), UserEvent::TYPE_CTA_CLICK, [
            'extra' => [
                'cta_name' => $data['cta_name'] ?? null,
                'cta_type' => $data['cta_type'] ?? 'general',
                'cta_position' => $data['cta_position'] ?? null,
                'destination' => $data['destination'] ?? null,
            ],
            'page_url' => $data['source_page'] ?? $data['page_url'] ?? '',
            'element_text' => $data['element_text'] ?? null,
            'element_id' => $data['element_id'] ?? null,
            'element_class' => $data['element_class'] ?? null,
        ]);
    }

    public function trackScroll(int $depth, string $url): UserEvent
    {
        return $this->eventRecorder->record($this->getSession(), UserEvent::TYPE_SCROLL, [
            'scroll_depth' => $depth,
            'page_url' => $url,
        ]);
    }

    public function trackQuizAnswer(int $quizId, int $questionId, string $answer): UserEvent
    {
        return $this->eventRecorder->record($this->getSession(), UserEvent::TYPE_QUIZ_ANSWER, [
            'extra' => [
                'quiz_id' => $quizId,
                'question_id' => $questionId,
                'answer' => $answer,
            ],
        ]);
    }

    public function linkSubscriber(Subscriber $subscriber): void
    {
        $this->sessionManager->linkSubscriber($this->getSession(), $subscriber);
    }

    public function trackQuizStartLocal(int $quizId): UserEvent
    {
        return $this->eventRecorder->record($this->getSession(), UserEvent::TYPE_QUIZ_START, [
            'extra' => ['quiz_id' => $quizId],
        ]);
    }

    public function trackQuizCompleteLocal(QuizResponse $response): UserEvent
    {
        return $this->eventRecorder->record($this->getSession(), UserEvent::TYPE_QUIZ_COMPLETE, [
            'extra' => [
                'quiz_id' => $response->quiz_id,
                'segment' => $response->segment,
                'outcome_id' => $response->outcome_id,
            ],
            'outcome' => $response->outcome?->name,
        ]);
    }

    // Broadcast methods (send to all drivers including external)
    public function trackQuizStart(int $quizId, ?string $quizName = null): void
    {
        $this->track('Quiz Started', [
            'quiz_id' => $quizId,
            'quiz_name' => $quizName,
        ]);
    }

    public function trackQuizComplete(QuizResponse $response): void
    {
        $this->track('Quiz Completed', array_merge([
            'quiz_id' => $response->quiz_id,
            'quiz_name' => $response->quiz?->name,
            'segment' => $response->segment,
            'outcome' => $response->outcome?->name,
            'time_to_complete' => $response->duration_seconds,
        ], $response->klaviyo_properties ?? []));

        // Sync to Klaviyo specifically for quiz responses
        $klaviyoService = $this->getKlaviyoService();
        if ($klaviyoService) {
            $klaviyoService->trackQuizCompleted($response);
        }
    }

    public function trackLeadMagnetDownload(LeadMagnetDownload $download): void
    {
        $this->track('Lead Magnet Downloaded', [
            'lead_magnet_id' => $download->leadMagnet->id,
            'lead_magnet_name' => $download->leadMagnet->name,
            'delivery_method' => $download->delivery_method,
        ]);

        $klaviyoService = $this->getKlaviyoService();
        if ($klaviyoService) {
            $klaviyoService->trackLeadMagnetDownload($download);
        }
    }

    public function trackOutboundClick(OutboundClick $click): void
    {
        $this->track('Outbound Click', [
            'destination' => $click->final_url,
            'link_name' => $click->outboundLink?->name,
            'segment' => $click->pp_segment,
        ]);

        $klaviyoService = $this->getKlaviyoService();
        if ($klaviyoService) {
            $klaviyoService->trackOutboundClick($click);
        }
    }

    protected function getKlaviyoService()
    {
        $driver = $this->drivers->get('klaviyo');
        return $driver && method_exists($driver, 'getKlaviyoService')
            ? $driver->getKlaviyoService()
            : null;
    }

    // Create outbound click record and broadcast (for OutboundController)
    public function recordOutboundClick(OutboundLink $link, string $finalUrl, array $passedData): OutboundClick
    {
        $session = $this->getSession();

        $click = OutboundClick::create([
            'outbound_link_id' => $link->id,
            'session_id' => $session->session_id,
            'subscriber_id' => $session->subscriber_id,
            'user_id' => $session->user_id,
            'source_page' => request()->header('referer'),
            'final_url' => $finalUrl,
            'passed_data' => $passedData,
            'pp_session' => $passedData['pp_session'] ?? null,
            'pp_email_hash' => $passedData['pp_email_hash'] ?? null,
            'pp_segment' => $passedData['pp_segment'] ?? null,
            'pp_engagement_score' => $passedData['pp_engagement_score'] ?? null,
            'pp_health_goal' => $passedData['pp_health_goal'] ?? null,
            'pp_experience_level' => $passedData['pp_experience_level'] ?? null,
            'pp_recommended_peptide' => $passedData['pp_recommended_peptide'] ?? null,
            'utm_source' => $link->utm_source,
            'utm_medium' => $link->utm_medium,
            'utm_campaign' => $link->utm_campaign,
            'utm_content' => $link->utm_content,
            'created_at' => now(),
        ]);

        $link->increment('click_count');

        // Record as local event
        $this->eventRecorder->record($session, UserEvent::TYPE_OUTBOUND_CLICK, [
            'extra' => ['link_id' => $link->id, 'destination' => $link->destination_url],
        ]);

        // Update subscriber shop click tracking
        if ($session->subscriber) {
            $subscriber = $session->subscriber;
            if (!$subscriber->clicked_to_shop) {
                $subscriber->update([
                    'clicked_to_shop' => true,
                    'first_shop_click_at' => now(),
                ]);
            }
            $subscriber->increment('shop_clicks');
        }

        // Broadcast to Klaviyo
        $this->trackOutboundClick($click);

        return $click;
    }

    public function trackSubscription(Subscriber $subscriber, string $source): void
    {
        $this->identify($subscriber);
        $this->track('Subscribed', [
            'source' => $source,
            'segment' => $subscriber->segment,
        ], $subscriber);
    }

    // Get cross-domain data for Fast Peptix
    public function getCrossDomainData(): array
    {
        $session = $this->getSession();
        $subscriber = $session->subscriber;
        $quizResponse = QuizResponse::where('session_id', $session->session_id)
            ->completed()
            ->latest()
            ->first();

        return array_filter([
            'pp_session' => $session->session_id,
            'pp_segment' => $session->segment ?? $subscriber?->segment,
            'pp_engagement_score' => $session->engagement_score,
            'pp_email_hash' => $subscriber ? hash('sha256', $subscriber->email) : null,
            'pp_quiz_completed' => $session->converted && $session->conversion_type === 'quiz',
            'pp_health_goal' => $quizResponse?->klaviyo_properties['pp_health_goal'] ?? null,
            'pp_experience_level' => $quizResponse?->klaviyo_properties['pp_experience_level'] ?? null,
            'pp_recommended_peptide' => $quizResponse?->outcome?->recommended_peptides[0] ?? null,
            'pp_utm_source' => $session->utm_source,
            'pp_utm_campaign' => $session->utm_campaign,
        ]);
    }

    // Access specific driver
    public function driver(string $name): ?TrackingDriver
    {
        return $this->drivers->get($name);
    }

    public function getEnabledDrivers(): array
    {
        return $this->drivers->keys()->toArray();
    }
}
