<?php

namespace App\Services\Tracking\Drivers;

use App\Models\Subscriber;
use App\Models\Setting;
use App\Services\Tracking\Contracts\TrackingDriver;
use App\Services\CustomerIo\CustomerIoService;

class KlaviyoDriver implements TrackingDriver
{
    protected CustomerIoService $customerIo;

    public function __construct()
    {
        $this->customerIo = app(CustomerIoService::class);
    }

    public function isEnabled(): bool
    {
        return $this->customerIo->isEnabled();
    }

    public function getName(): string
    {
        return 'customerio';
    }

    public function identify(Subscriber $subscriber): void
    {
        if (!$this->isEnabled()) return;

        $this->customerIo->syncProfile($subscriber);

        $defaultListId = Setting::getValue('integrations', 'customerio_default_list_id');
        if ($defaultListId) {
            $this->customerIo->addToList($subscriber, $defaultListId);
        }
    }

    public function trackEvent(string $eventName, array $properties = [], ?Subscriber $subscriber = null): void
    {
        if (!$this->isEnabled() || !$subscriber) return;

        $this->customerIo->trackEvent($subscriber, $eventName, $properties);
    }

    public function trackPageView(string $url, ?string $title = null): void
    {
        // Customer.io handles page views via their JS snippet
        // We only sync server-side events
    }

    public function getCustomerIoService(): CustomerIoService
    {
        return $this->customerIo;
    }
}
