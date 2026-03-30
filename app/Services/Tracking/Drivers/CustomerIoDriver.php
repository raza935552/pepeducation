<?php

namespace App\Services\Tracking\Drivers;

use App\Models\Subscriber;
use App\Services\Tracking\Contracts\TrackingDriver;
use App\Services\CustomerIo\CustomerIoService;

class CustomerIoDriver implements TrackingDriver
{
    protected CustomerIoService $customerIo;

    public function __construct()
    {
        $this->customerIo = CustomerIoService::make();
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
        $this->customerIo->syncSubscriber($subscriber);
    }

    public function trackEvent(string $eventName, array $properties = [], ?Subscriber $subscriber = null): void
    {
        if (!$this->isEnabled() || !$subscriber) return;
        $this->customerIo->trackCustomEvent($eventName, $subscriber->email, $properties);
    }

    public function trackPageView(string $url, ?string $title = null): void
    {
        // Customer.io handles page views via their JS snippet
    }

    public function getCustomerIoService(): CustomerIoService
    {
        return $this->customerIo;
    }
}
