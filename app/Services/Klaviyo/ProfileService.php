<?php

namespace App\Services\Klaviyo;

use App\Models\Subscriber;

class ProfileService
{
    protected KlaviyoClient $client;

    public function __construct(KlaviyoClient $client)
    {
        $this->client = $client;
    }

    public function createOrUpdate(Subscriber $subscriber): ?string
    {
        // If we already have Klaviyo ID, just update
        if ($subscriber->klaviyo_id) {
            return $this->updateProfile($subscriber);
        }

        // Try to find existing profile by email first
        $existingId = $this->findByEmail($subscriber->email);
        if ($existingId) {
            $subscriber->update(['klaviyo_id' => $existingId]);
            return $this->updateProfile($subscriber);
        }

        // Create new profile
        $profileData = $this->buildProfileData($subscriber);

        $response = $this->client->post('/profiles/', [
            'data' => [
                'type' => 'profile',
                'attributes' => $profileData,
            ],
        ]);

        if ($response && isset($response['data']['id'])) {
            $klaviyoId = $response['data']['id'];
            $subscriber->update([
                'klaviyo_id' => $klaviyoId,
                'klaviyo_synced_at' => now(),
            ]);
            return $klaviyoId;
        }

        // Handle 409 Conflict (duplicate email) - extract existing ID
        if ($response === null) {
            $existingId = $this->findByEmail($subscriber->email);
            if ($existingId) {
                $subscriber->update(['klaviyo_id' => $existingId]);
                return $this->updateProfile($subscriber);
            }
        }

        return null;
    }

    /**
     * Find Klaviyo profile by email
     */
    public function findByEmail(string $email): ?string
    {
        $response = $this->client->get('/profiles/', [
            'filter' => "equals(email,\"{$email}\")",
        ]);

        if ($response && !empty($response['data'][0]['id'])) {
            return $response['data'][0]['id'];
        }

        return null;
    }

    /**
     * Update existing profile
     */
    protected function updateProfile(Subscriber $subscriber): ?string
    {
        if (!$subscriber->klaviyo_id) {
            return null;
        }

        $profileData = $this->buildProfileData($subscriber);

        $response = $this->client->patch("/profiles/{$subscriber->klaviyo_id}/", [
            'data' => [
                'type' => 'profile',
                'id' => $subscriber->klaviyo_id,
                'attributes' => $profileData,
            ],
        ]);

        if ($response !== null) {
            $subscriber->update(['klaviyo_synced_at' => now()]);
            return $subscriber->klaviyo_id;
        }

        return $subscriber->klaviyo_id;
    }

    public function updateProperties(Subscriber $subscriber, array $properties): bool
    {
        if (!$subscriber->klaviyo_id) {
            $this->createOrUpdate($subscriber);
        }

        if (!$subscriber->klaviyo_id) return false;

        $response = $this->client->post("/profiles/{$subscriber->klaviyo_id}", [
            'data' => [
                'type' => 'profile',
                'id' => $subscriber->klaviyo_id,
                'attributes' => [
                    'properties' => $properties,
                ],
            ],
        ]);

        if ($response) {
            $existing = $subscriber->klaviyo_properties ?? [];
            $subscriber->update([
                'klaviyo_properties' => array_merge($existing, $properties),
                'klaviyo_synced_at' => now(),
            ]);
            return true;
        }

        return false;
    }

    public function addToList(Subscriber $subscriber, string $listId): bool
    {
        if (!$subscriber->klaviyo_id) {
            $this->createOrUpdate($subscriber);
        }

        if (!$subscriber->klaviyo_id) return false;

        $response = $this->client->post("/lists/{$listId}/relationships/profiles/", [
            'data' => [
                ['type' => 'profile', 'id' => $subscriber->klaviyo_id],
            ],
        ]);

        return $response !== null;
    }

    protected function buildProfileData(Subscriber $subscriber): array
    {
        $data = [
            'email' => $subscriber->email,
            'properties' => array_merge([
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
            ], $subscriber->klaviyo_properties ?? []),
        ];

        if ($subscriber->name) {
            $nameParts = explode(' ', $subscriber->name, 2);
            $data['first_name'] = $nameParts[0];
            $data['last_name'] = $nameParts[1] ?? '';
        }

        if ($subscriber->phone) {
            $data['phone_number'] = $subscriber->phone;
        }

        if ($subscriber->city || $subscriber->region || $subscriber->country) {
            $data['location'] = [
                'city' => $subscriber->city,
                'region' => $subscriber->region,
                'country' => $subscriber->country,
            ];
        }

        return $data;
    }
}
