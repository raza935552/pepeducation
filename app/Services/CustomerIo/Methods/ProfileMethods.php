<?php

namespace App\Services\CustomerIo\Methods;

use App\Models\Subscriber;
use App\Services\CustomerIo\CustomerIoResponse;
use Illuminate\Support\Facades\Log;

trait ProfileMethods
{
    public function syncSubscriber(Subscriber $subscriber): ?string
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $attributes = array_filter([
            'email' => $subscriber->email,
            'created_at' => $subscriber->created_at?->timestamp ?? time(),
            'pp_segment' => $subscriber->segment,
            'pp_engagement_score' => $subscriber->engagement_score,
            'pp_engagement_tier' => $subscriber->engagement_tier,
            'pp_quiz_completed' => $subscriber->quiz_completed,
            'pp_source' => $subscriber->source,
            'pp_first_utm_source' => $subscriber->first_utm_source,
            'pp_first_utm_medium' => $subscriber->first_utm_medium,
            'pp_first_utm_campaign' => $subscriber->first_utm_campaign,
            'pp_clicked_to_shop' => $subscriber->clicked_to_shop,
            'pp_shop_clicks' => $subscriber->shop_clicks,
            'pp_total_sessions' => $subscriber->total_sessions,
            'pp_primary_interest' => $subscriber->primary_interest,
        ], fn ($v) => $v !== null && $v !== '');

        // Add name fields
        if ($subscriber->name) {
            $nameParts = explode(' ', $subscriber->name, 2);
            $attributes['first_name'] = $nameParts[0];
            $attributes['last_name'] = $nameParts[1] ?? '';
        }

        if ($subscriber->phone) {
            $attributes['phone'] = $this->formatPhoneNumber($subscriber->phone);
        }

        if ($subscriber->city) $attributes['city'] = $subscriber->city;
        if ($subscriber->region) $attributes['region'] = $subscriber->region;
        if ($subscriber->country) $attributes['country'] = $subscriber->country;

        // Merge any stored custom properties
        $customProps = $subscriber->customerio_properties ?? [];
        $attributes = array_merge($attributes, $customProps);

        // Use email as identifier (Customer.io convention for anonymous-first users)
        $identifier = $subscriber->email;
        $response = $this->client->put("customers/{$identifier}", $attributes);

        if ($response->isSuccess()) {
            $subscriber->update([
                'customerio_id' => $identifier,
                'customerio_synced_at' => now(),
                'needs_customerio_sync' => false,
            ]);

            Log::info('Customer.io: Subscriber synced', [
                'subscriber_id' => $subscriber->id,
                'email' => $subscriber->email,
            ]);

            return $identifier;
        }

        Log::warning('Customer.io: Failed to sync subscriber', [
            'subscriber_id' => $subscriber->id,
            'error' => $response->getError(),
        ]);

        return null;
    }

    public function updateProperties(Subscriber $subscriber, array $properties): bool
    {
        if (!$this->isEnabled() || empty($properties)) {
            return false;
        }

        $identifier = $subscriber->customerio_id ?? $subscriber->email;
        if (!$identifier) return false;

        $response = $this->client->put("customers/{$identifier}", $properties);

        if ($response->isSuccess()) {
            $existing = $subscriber->customerio_properties ?? [];
            $subscriber->update([
                'customerio_properties' => array_merge($existing, $properties),
                'customerio_synced_at' => now(),
            ]);
            return true;
        }

        return false;
    }

    protected function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) return null;

        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($digits) === 10) return '+1' . $digits;
        if (strlen($digits) === 11 && str_starts_with($digits, '1')) return '+' . $digits;
        if (!str_starts_with($phone, '+')) return '+' . $digits;

        return $phone;
    }
}
