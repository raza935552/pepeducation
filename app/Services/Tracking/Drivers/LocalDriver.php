<?php

namespace App\Services\Tracking\Drivers;

use App\Models\Subscriber;
use App\Models\UserEvent;
use App\Services\Tracking\Contracts\TrackingDriver;
use App\Services\Tracking\SessionManager;
use App\Services\Tracking\EventRecorder;
use Illuminate\Support\Facades\Log;

class LocalDriver implements TrackingDriver
{
    protected SessionManager $sessionManager;
    protected EventRecorder $eventRecorder;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
        $this->eventRecorder = new EventRecorder();
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'local';
    }

    public function identify(Subscriber $subscriber): void
    {
        $session = $this->sessionManager->getOrCreateSession();
        $this->sessionManager->linkSubscriber($session, $subscriber);
    }

    public function trackEvent(string $eventName, array $properties = [], ?Subscriber $subscriber = null): void
    {
        try {
            $session = $this->sessionManager->getOrCreateSession();
            $this->eventRecorder->record($session, $eventName, $properties);
        } catch (\Exception $e) {
            Log::warning('LocalDriver tracking failed', [
                'event' => $eventName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function trackPageView(string $url, ?string $title = null): void
    {
        try {
            $session = $this->sessionManager->getOrCreateSession();
            $this->eventRecorder->record($session, UserEvent::TYPE_PAGE_VIEW, [
                'page_url' => $url,
                'page_title' => $title,
            ]);
        } catch (\Exception $e) {
            Log::warning('LocalDriver page view tracking failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
