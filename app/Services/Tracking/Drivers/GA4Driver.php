<?php

namespace App\Services\Tracking\Drivers;

use App\Models\Subscriber;
use App\Models\Setting;
use App\Services\Tracking\Contracts\TrackingDriver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GA4Driver implements TrackingDriver
{
    protected ?string $measurementId;
    protected ?string $apiSecret;
    protected string $endpoint = 'https://www.google-analytics.com/mp/collect';

    public function __construct()
    {
        $this->measurementId = Setting::getValue('tracking', 'ga4_measurement_id');
        $this->apiSecret = Setting::getValue('tracking', 'ga4_api_secret');
    }

    public function isEnabled(): bool
    {
        return !empty($this->measurementId) && !empty($this->apiSecret);
    }

    public function getName(): string
    {
        return 'ga4';
    }

    public function identify(Subscriber $subscriber): void
    {
        // GA4 uses client_id from the browser, not server-side identification
        // User properties are set via events
    }

    public function trackEvent(string $eventName, array $properties = [], ?Subscriber $subscriber = null): void
    {
        if (!$this->isEnabled()) return;

        $clientId = $properties['client_id'] ?? $this->generateClientId();

        $payload = [
            'client_id' => $clientId,
            'events' => [
                [
                    'name' => $this->sanitizeEventName($eventName),
                    'params' => $this->sanitizeParams($properties),
                ],
            ],
        ];

        // Add user properties if subscriber exists
        if ($subscriber) {
            $payload['user_properties'] = [
                'segment' => ['value' => $subscriber->segment ?? 'unknown'],
                'engagement_tier' => ['value' => $subscriber->engagement_tier ?? 'cold'],
            ];
        }

        $this->send($payload);
    }

    public function trackPageView(string $url, ?string $title = null): void
    {
        // GA4 handles page views via their JS snippet (gtag.js)
        // Server-side is for custom events only
    }

    protected function send(array $payload): void
    {
        try {
            // GA4 Measurement Protocol requires api_secret as query parameter.
            // Using withQueryParameters keeps secrets out of string interpolation and logs.
            Http::timeout(5)
                ->withQueryParameters([
                    'measurement_id' => $this->measurementId,
                    'api_secret' => $this->apiSecret,
                ])
                ->post($this->endpoint, $payload);
        } catch (\Exception $e) {
            // Log only the error message, never the URL (contains api_secret)
            Log::warning('GA4 tracking failed', ['error' => $e->getMessage()]);
        }
    }

    protected function sanitizeEventName(string $name): string
    {
        // GA4 event names: max 40 chars, alphanumeric + underscores
        $name = preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
        return substr($name, 0, 40);
    }

    protected function sanitizeParams(array $params): array
    {
        $sanitized = [];
        foreach ($params as $key => $value) {
            // GA4 param keys: max 40 chars
            $key = substr(preg_replace('/[^a-zA-Z0-9_]/', '_', $key), 0, 40);
            // GA4 param values: max 100 chars for strings
            if (is_string($value)) {
                $value = substr($value, 0, 100);
            }
            $sanitized[$key] = $value;
        }
        return $sanitized;
    }

    protected function generateClientId(): string
    {
        return sprintf('%d.%d', random_int(1000000000, 9999999999), time());
    }

    public function getMeasurementId(): ?string
    {
        return $this->measurementId;
    }
}
