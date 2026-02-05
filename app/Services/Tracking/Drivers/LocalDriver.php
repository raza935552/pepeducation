<?php

namespace App\Services\Tracking\Drivers;

use App\Models\Subscriber;
use App\Models\UserEvent;
use App\Services\Tracking\Contracts\TrackingDriver;
use App\Services\Tracking\SessionManager;
use App\Services\Tracking\EventRecorder;

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
        return true; // Always enabled
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
        $session = $this->sessionManager->getOrCreateSession();
        $this->eventRecorder->record($session, $eventName, $properties);
    }

    public function trackPageView(string $url, ?string $title = null): void
    {
        $session = $this->sessionManager->getOrCreateSession();
        $this->eventRecorder->record($session, UserEvent::TYPE_PAGE_VIEW, [
            'page_url' => $url,
            'page_title' => $title,
        ]);
    }
}
