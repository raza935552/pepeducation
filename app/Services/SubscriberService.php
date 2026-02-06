<?php

namespace App\Services;

use App\Models\Subscriber;
use App\Jobs\SyncSubscriberToKlaviyo;
use App\Services\Klaviyo\KlaviyoService;

class SubscriberService
{
    protected KlaviyoService $klaviyo;

    public function __construct(KlaviyoService $klaviyo)
    {
        $this->klaviyo = $klaviyo;
    }

    /**
     * Subscribe an email - handles deduplication locally and in Klaviyo
     */
    public function subscribe(string $email, array $data = []): Subscriber
    {
        $email = strtolower(trim($email));

        // Check if subscriber exists
        $subscriber = Subscriber::where('email', $email)->first();

        if ($subscriber) {
            // Update existing subscriber
            $subscriber = $this->updateExisting($subscriber, $data);
        } else {
            // Create new subscriber
            $subscriber = $this->createNew($email, $data);
        }

        // Sync to Klaviyo (handles dedup on their side too)
        $this->syncToKlaviyo($subscriber, $data['source'] ?? 'unknown');

        return $subscriber;
    }

    /**
     * Update existing subscriber - don't overwrite important data
     */
    protected function updateExisting(Subscriber $subscriber, array $data): Subscriber
    {
        $updates = [];

        // Reactivate if unsubscribed
        if ($subscriber->status === 'unsubscribed') {
            $updates['status'] = 'active';
            $updates['subscribed_at'] = now();
            $updates['unsubscribed_at'] = null;
        }

        // Update segment if provided and subscriber doesn't have one
        if (!empty($data['segment']) && !$subscriber->segment) {
            $updates['segment'] = $data['segment'];
        }

        // Update first touch attribution only if not set
        if (!$subscriber->first_session_id && !empty($data['first_session_id'])) {
            $updates['first_session_id'] = $data['first_session_id'];
        }
        if (!$subscriber->first_landing_page && !empty($data['first_landing_page'])) {
            $updates['first_landing_page'] = $data['first_landing_page'];
        }

        // Track additional sources (append to source field)
        if (!empty($data['source']) && !str_contains($subscriber->source ?? '', $data['source'])) {
            // Don't overwrite original source, just log that they came from another place
            $updates['last_activity_at'] = now();
        }

        if (!empty($updates)) {
            $subscriber->update($updates);
        }

        return $subscriber->fresh();
    }

    /**
     * Create new subscriber
     */
    protected function createNew(string $email, array $data): Subscriber
    {
        return Subscriber::create([
            'email' => $email,
            'source' => $data['source'] ?? 'unknown',
            'segment' => strtolower($data['segment'] ?? 'tof'),
            'first_session_id' => $data['first_session_id'] ?? null,
            'first_landing_page' => $data['first_landing_page'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'status' => 'active',
            'subscribed_at' => now(),
        ]);
    }

    /**
     * Dispatch async Klaviyo sync job
     */
    protected function syncToKlaviyo(Subscriber $subscriber, string $source): void
    {
        if (!$this->klaviyo->isEnabled()) {
            return;
        }

        SyncSubscriberToKlaviyo::dispatch($subscriber, $source);
    }

    /**
     * Get subscriber by email
     */
    public function findByEmail(string $email): ?Subscriber
    {
        return Subscriber::where('email', strtolower(trim($email)))->first();
    }

    /**
     * Check if email is already subscribed
     */
    public function isSubscribed(string $email): bool
    {
        $subscriber = $this->findByEmail($email);
        return $subscriber && $subscriber->status === 'active';
    }

    /**
     * Set pp_email cookie for cross-domain tracking
     */
    public function setEmailCookie(string $email): void
    {
        cookie()->queue('pp_email', strtolower(trim($email)), 60 * 24 * 30);
    }
}
