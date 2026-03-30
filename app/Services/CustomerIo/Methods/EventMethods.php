<?php

namespace App\Services\CustomerIo\Methods;

use App\Models\Subscriber;
use App\Models\QuizResponse;
use App\Models\LeadMagnetDownload;
use App\Models\OutboundClick;
use Illuminate\Support\Facades\Log;

trait EventMethods
{
    protected static array $identifiedEmails = [];

    public function trackQuizStarted(Subscriber $subscriber, int $quizId, string $quizName): bool
    {
        if (!$this->shouldTrack('quiz_started')) return false;

        return $this->trackEvent($subscriber->email, 'Started Quiz', [
            'quiz_id' => $quizId,
            'quiz_name' => $quizName,
        ]);
    }

    public function trackQuizCompleted(QuizResponse $response): bool
    {
        if (!$this->shouldTrack('quiz_completed')) return false;

        if (!$response->subscriber) {
            Log::warning('Customer.io quiz sync skipped: no subscriber', [
                'quiz_response_id' => $response->id,
            ]);
            return false;
        }

        $properties = array_merge([
            'quiz_id' => $response->quiz_id,
            'quiz_name' => $response->quiz?->name,
            'segment' => $response->segment,
            'outcome' => $response->outcome?->name,
            'time_to_complete' => $response->duration_seconds,
        ], $response->marketing_properties ?? []);

        $success = $this->trackEvent($response->subscriber->email, 'Completed Quiz', $properties);

        if ($success) {
            // Update profile properties with quiz answers
            $this->updateProperties($response->subscriber, $response->marketing_properties ?? []);
            $response->update(['synced_to_marketing' => true]);
        }

        return $success;
    }

    public function trackEmailCaptured(Subscriber $subscriber, QuizResponse $response): bool
    {
        if (!$this->shouldTrack('email_captured')) return false;

        return $this->trackEvent($subscriber->email, 'Email Captured', [
            'quiz_id' => $response->quiz_id,
            'quiz_name' => $response->quiz?->name,
            'source' => 'quiz:' . ($response->quiz?->slug ?? 'unknown'),
            'segment' => $response->segment,
            'questions_answered' => count($response->answers ?? []),
        ]);
    }

    public function trackQuizAbandoned(Subscriber $subscriber, QuizResponse $response): bool
    {
        if (!$this->shouldTrack('quiz_abandoned')) return false;

        $answers = $response->answers ?? [];
        $properties = [
            'quiz_id' => $response->quiz_id,
            'quiz_name' => $response->quiz?->name,
            'segment' => $response->segment,
            'questions_answered' => count($answers),
            'time_spent_seconds' => $response->duration_seconds
                ?? ($response->started_at ? now()->diffInSeconds($response->started_at) : null),
        ];

        // Include answer properties so flows can branch on what they selected
        foreach ($answers as $answer) {
            if (!empty($answer['marketing_property']) && !empty($answer['marketing_value'])) {
                $properties[$answer['marketing_property']] = $answer['marketing_value'];
            }
            // Backward compat: also check old klaviyo_property/klaviyo_value keys in stored answers
            if (!empty($answer['klaviyo_property']) && !empty($answer['klaviyo_value'])) {
                $properties[$answer['klaviyo_property']] = $answer['klaviyo_value'];
            }
        }

        return $this->trackEvent($subscriber->email, 'Quiz Abandoned', $properties);
    }

    public function trackLeadMagnetDownload(LeadMagnetDownload $download): bool
    {
        if (!$this->shouldTrack('lead_magnet_download')) return false;

        if (!$download->subscriber) return false;
        $leadMagnet = $download->leadMagnet;
        if (!$leadMagnet) return false;

        $eventName = $leadMagnet->marketing_event ?? 'Downloaded Lead Magnet';

        $success = $this->trackEvent($download->subscriber->email, $eventName, [
            'lead_magnet_id' => $leadMagnet->id,
            'lead_magnet_name' => $leadMagnet->name,
            'lead_magnet_slug' => $leadMagnet->slug,
            'delivery_method' => $download->delivery_method,
            'source_page' => $download->source_page,
            'source_popup' => $download->source_popup,
        ]);

        if ($success) {
            if ($leadMagnet->marketing_property_name) {
                $this->updateProperties($download->subscriber, [
                    $leadMagnet->marketing_property_name => true,
                ]);
            }
            $download->update(['synced_to_marketing' => true]);
        }

        return $success;
    }

    public function trackOutboundClick(OutboundClick $click): bool
    {
        if (!$this->shouldTrack('outbound_click')) return false;

        if (!$click->subscriber_id) return false;

        $subscriber = \App\Models\Subscriber::find($click->subscriber_id);
        if (!$subscriber) return false;

        return $this->trackEvent($subscriber->email, 'Clicked to Shop', [
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
        if (!$this->shouldTrack('stack_completed')) return false;

        $success = $this->trackEvent($subscriber->email, 'Completed Stack Builder', [
            'goal_slug' => $goalSlug,
            'goal_name' => $goalName,
        ]);

        if ($success && $goalName) {
            $this->updateProperties($subscriber, ['pp_stack_goal' => $goalName]);
        }

        return $success;
    }

    public function trackSubscribed(Subscriber $subscriber, string $source, ?string $popupSlug = null): bool
    {
        if (!$this->shouldTrack('subscribed')) return false;

        // Upsert the profile with subscription flag
        $this->client->put("customers/{$subscriber->email}", [
            'email' => $subscriber->email,
            'subscribed' => true,
            'subscription_source' => $source,
        ]);

        return $this->trackEvent($subscriber->email, 'Subscribed', [
            'source' => $source,
            'popup' => $popupSlug,
            'segment' => $subscriber->segment,
            'landing_page' => $subscriber->first_landing_page,
            'utm_source' => $subscriber->first_utm_source,
            'utm_campaign' => $subscriber->first_utm_campaign,
        ]);
    }

    public function trackCustomEvent(string $eventName, string $email, array $properties = []): bool
    {
        if (!$this->isEnabled()) return false;

        return $this->trackEvent($email, $eventName, $properties);
    }

    protected function trackEvent(string $email, string $eventName, array $data = []): bool
    {
        if (empty($email)) return false;

        // Ensure person exists (Customer.io requires it before tracking events)
        if (!in_array($email, static::$identifiedEmails)) {
            $this->client->put("customers/{$email}", ['email' => $email]);
            static::$identifiedEmails[] = $email;
        }

        $response = $this->client->post("customers/{$email}/events", [
            'name' => $eventName,
            'data' => $data,
        ]);

        if ($response->failed()) {
            Log::warning("Customer.io: Failed to track '{$eventName}'", [
                'email' => $email,
                'error' => $response->getError(),
            ]);
            return false;
        }

        return true;
    }
}
