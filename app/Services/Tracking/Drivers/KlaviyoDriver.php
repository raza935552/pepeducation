<?php

namespace App\Services\Tracking\Drivers;

use App\Models\Subscriber;
use App\Models\Setting;
use App\Services\Tracking\Contracts\TrackingDriver;
use App\Services\Klaviyo\KlaviyoService;

class KlaviyoDriver implements TrackingDriver
{
    protected KlaviyoService $klaviyo;

    public function __construct()
    {
        $this->klaviyo = app(KlaviyoService::class);
    }

    public function isEnabled(): bool
    {
        return $this->klaviyo->isEnabled();
    }

    public function getName(): string
    {
        return 'klaviyo';
    }

    public function identify(Subscriber $subscriber): void
    {
        if (!$this->isEnabled()) return;

        $this->klaviyo->syncProfile($subscriber);

        $defaultListId = Setting::getValue('integrations', 'klaviyo_default_list_id');
        if ($defaultListId) {
            $this->klaviyo->addToList($subscriber, $defaultListId);
        }
    }

    public function trackEvent(string $eventName, array $properties = [], ?Subscriber $subscriber = null): void
    {
        if (!$this->isEnabled() || !$subscriber) return;

        $this->klaviyo->trackEvent($subscriber, $eventName, $properties);
    }

    public function trackPageView(string $url, ?string $title = null): void
    {
        // Klaviyo handles page views via their JS snippet
        // We only sync server-side events
    }

    public function getKlaviyoService(): KlaviyoService
    {
        return $this->klaviyo;
    }
}
