<?php

namespace App\Services\Klaviyo;

use App\Models\Subscriber;
use App\Models\QuizResponse;
use App\Models\LeadMagnetDownload;
use App\Models\OutboundClick;
use App\Models\Setting;

class KlaviyoService
{
    protected KlaviyoClient $client;
    protected ProfileService $profiles;
    protected EventService $events;

    public function __construct(KlaviyoClient $client, ProfileService $profiles, EventService $events)
    {
        $this->client = $client;
        $this->profiles = $profiles;
        $this->events = $events;
    }

    public function isEnabled(): bool
    {
        return $this->client->isEnabled();
    }

    public function getPublicKey(): ?string
    {
        return $this->client->getPublicKey();
    }

    // Profile Methods
    public function syncProfile(Subscriber $subscriber): ?string
    {
        return $this->profiles->createOrUpdate($subscriber);
    }

    public function updateProfileProperties(Subscriber $subscriber, array $properties): bool
    {
        return $this->profiles->updateProperties($subscriber, $properties);
    }

    public function addToList(Subscriber $subscriber, ?string $listId = null): bool
    {
        $listId = $listId ?? Setting::getValue('integrations', 'klaviyo_default_list_id');
        if (!$listId) return false;

        return $this->profiles->addToList($subscriber, $listId);
    }

    // Event Methods
    public function trackEvent(Subscriber $subscriber, string $eventName, array $properties = []): bool
    {
        return $this->events->track($subscriber, $eventName, $properties);
    }

    public function trackQuizStarted(Subscriber $subscriber, int $quizId, string $quizName): bool
    {
        return $this->events->trackQuizStarted($subscriber, $quizId, $quizName);
    }

    public function trackQuizCompleted(QuizResponse $response): bool
    {
        return $this->events->trackQuizCompleted($response);
    }

    public function trackLeadMagnetDownload(LeadMagnetDownload $download): bool
    {
        return $this->events->trackLeadMagnetDownload($download);
    }

    public function trackOutboundClick(OutboundClick $click): bool
    {
        return $this->events->trackOutboundClick($click);
    }

    public function trackSubscribed(Subscriber $subscriber, string $source, ?string $popupSlug = null): bool
    {
        return $this->events->trackSubscribed($subscriber, $source, $popupSlug);
    }

    // Batch sync for queued jobs
    public function syncPendingProfiles(int $limit = 100): int
    {
        $subscribers = Subscriber::needsSyncToKlaviyo()->limit($limit)->get();
        $synced = 0;

        foreach ($subscribers as $subscriber) {
            if ($this->profiles->createOrUpdate($subscriber)) {
                $synced++;
            }
        }

        return $synced;
    }

    public function syncPendingQuizResponses(int $limit = 100): int
    {
        $responses = QuizResponse::needsSyncToKlaviyo()->with('subscriber', 'quiz', 'outcome')->limit($limit)->get();
        $synced = 0;

        foreach ($responses as $response) {
            if ($this->events->trackQuizCompleted($response)) {
                $synced++;
            }
        }

        return $synced;
    }
}
