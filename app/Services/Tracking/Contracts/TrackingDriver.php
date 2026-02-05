<?php

namespace App\Services\Tracking\Contracts;

use App\Models\Subscriber;

interface TrackingDriver
{
    public function isEnabled(): bool;

    public function identify(Subscriber $subscriber): void;

    public function trackEvent(string $eventName, array $properties = [], ?Subscriber $subscriber = null): void;

    public function trackPageView(string $url, ?string $title = null): void;

    public function getName(): string;
}
