<?php

namespace App\Services\Klaviyo;

use App\Models\Subscriber;
use App\Models\UserSession;
use App\Models\QuizResponse;
use App\Models\LeadMagnetDownload;
use App\Models\OutboundClick;

class EventService
{
    protected KlaviyoClient $client;
    protected ProfileService $profileService;

    public function __construct(KlaviyoClient $client, ProfileService $profileService)
    {
        $this->client = $client;
        $this->profileService = $profileService;
    }

    public function track(Subscriber $subscriber, string $eventName, array $properties = []): bool
    {
        // Ensure profile exists (refresh to get klaviyo_id set by createOrUpdate)
        if (!$subscriber->klaviyo_id) {
            $this->profileService->createOrUpdate($subscriber);
            $subscriber->refresh();
        }

        if (!$subscriber->klaviyo_id) return false;

        $response = $this->client->post('/events/', [
            'data' => [
                'type' => 'event',
                'attributes' => [
                    'metric' => ['data' => ['type' => 'metric', 'attributes' => ['name' => $eventName]]],
                    'profile' => ['data' => ['type' => 'profile', 'id' => $subscriber->klaviyo_id]],
                    'properties' => $properties,
                    'time' => now()->toIso8601String(),
                ],
            ],
        ]);

        return $response !== null;
    }

    public function trackQuizStarted(Subscriber $subscriber, int $quizId, string $quizName): bool
    {
        return $this->track($subscriber, 'Started Quiz', [
            'quiz_id' => $quizId,
            'quiz_name' => $quizName,
        ]);
    }

    public function trackQuizCompleted(QuizResponse $response): bool
    {
        if (!$response->subscriber) return false;

        $properties = array_merge([
            'quiz_id' => $response->quiz_id,
            'quiz_name' => $response->quiz?->name,
            'segment' => $response->segment,
            'outcome' => $response->outcome?->name,
            'time_to_complete' => $response->duration_seconds,
        ], $response->klaviyo_properties ?? []);

        $success = $this->track($response->subscriber, 'Completed Quiz', $properties);

        if ($success) {
            // Update profile properties with quiz answers
            $this->profileService->updateProperties($response->subscriber, $response->klaviyo_properties ?? []);
            $response->update(['synced_to_klaviyo' => true]);
        }

        return $success;
    }

    public function trackLeadMagnetDownload(LeadMagnetDownload $download): bool
    {
        if (!$download->subscriber) return false;

        $leadMagnet = $download->leadMagnet;
        if (!$leadMagnet) return false;

        $success = $this->track($download->subscriber, $leadMagnet->klaviyo_event ?? 'Downloaded Lead Magnet', [
            'lead_magnet_id' => $leadMagnet->id,
            'lead_magnet_name' => $leadMagnet->name,
            'lead_magnet_slug' => $leadMagnet->slug,
            'delivery_method' => $download->delivery_method,
            'source_page' => $download->source_page,
            'source_popup' => $download->source_popup,
        ]);

        if ($success) {
            // Update profile property
            if ($leadMagnet->klaviyo_property_name) {
                $this->profileService->updateProperties($download->subscriber, [
                    $leadMagnet->klaviyo_property_name => true,
                ]);
            }
            $download->update(['synced_to_klaviyo' => true]);
        }

        return $success;
    }

    public function trackOutboundClick(OutboundClick $click): bool
    {
        if (!$click->subscriber_id) return false;

        $subscriber = Subscriber::find($click->subscriber_id);
        if (!$subscriber) return false;

        return $this->track($subscriber, 'Clicked to Shop', [
            'destination_url' => $click->final_url,
            'link_name' => $click->outboundLink?->name,
            'segment' => $click->pp_segment,
            'engagement_score' => $click->pp_engagement_score,
            'health_goal' => $click->pp_health_goal,
            'recommended_peptide' => $click->pp_recommended_peptide,
        ]);
    }

    public function trackStackCompleted(Subscriber $subscriber, string $goalSlug, string $goalName): bool
    {
        $success = $this->track($subscriber, 'Completed Stack Builder', [
            'goal_slug' => $goalSlug,
            'goal_name' => $goalName,
        ]);

        if ($success && $goalName) {
            $this->profileService->updateProperties($subscriber, [
                'pp_stack_goal' => $goalName,
            ]);
        }

        return $success;
    }

    public function trackSubscribed(Subscriber $subscriber, string $source, ?string $popupSlug = null): bool
    {
        return $this->track($subscriber, 'Subscribed', [
            'source' => $source,
            'popup' => $popupSlug,
            'segment' => $subscriber->segment,
            'landing_page' => $subscriber->first_landing_page,
            'utm_source' => $subscriber->first_utm_source,
            'utm_campaign' => $subscriber->first_utm_campaign,
        ]);
    }
}
