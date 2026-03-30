<?php

namespace App\Services\CustomerIo;

use App\Models\CustomerIoSetting;
use App\Services\CustomerIo\Methods\ProfileMethods;
use App\Services\CustomerIo\Methods\EventMethods;
use Illuminate\Support\Facades\Log;

class CustomerIoService
{
    use ProfileMethods;
    use EventMethods;

    protected CustomerIoClient $client;
    protected ?CustomerIoSetting $settings;

    public function __construct(CustomerIoClient $client, ?CustomerIoSetting $settings = null)
    {
        $this->client = $client;
        $this->settings = $settings;
    }

    public static function make(): static
    {
        try {
            $settings = CustomerIoSetting::current();
            $client = new CustomerIoClient($settings);
            return new static($client, $settings);
        } catch (\Exception $e) {
            Log::warning('Customer.io: Failed to initialize service', [
                'error' => $e->getMessage(),
            ]);
            return new static(new CustomerIoClient(null), null);
        }
    }

    public function isEnabled(): bool
    {
        if ($this->settings && $this->settings->is_enabled) {
            return $this->client->hasCredentials();
        }

        return !empty(config('customerio.site_id')) && !empty(config('customerio.api_key'));
    }

    public function shouldTrack(string $event): bool
    {
        if (!$this->isEnabled()) return false;
        if (!$this->settings) return true;

        return match ($event) {
            'quiz_started' => $this->settings->track_quiz_started,
            'quiz_completed' => $this->settings->track_quiz_completed,
            'email_captured' => $this->settings->track_email_captured,
            'quiz_abandoned' => $this->settings->track_quiz_abandoned,
            'lead_magnet_download' => $this->settings->track_lead_magnet_download,
            'outbound_click' => $this->settings->track_outbound_click,
            'stack_completed' => $this->settings->track_stack_completed,
            'subscribed' => $this->settings->track_subscribed,
            'page_tracking' => $this->settings->enable_page_tracking,
            default => true,
        };
    }

    public function testConnection(): CustomerIoResponse
    {
        return $this->client->testConnection();
    }

    public function getClient(): CustomerIoClient
    {
        return $this->client;
    }

    public function getSettings(): ?CustomerIoSetting
    {
        return $this->settings;
    }

    /**
     * Batch sync for pending subscribers (used by artisan command).
     */
    public function syncPendingProfiles(int $limit = 100): array
    {
        $subscribers = \App\Models\Subscriber::where('needs_customerio_sync', true)->limit($limit)->get();
        $synced = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
            if ($this->syncSubscriber($subscriber)) {
                $synced++;
            } else {
                $failed++;
            }
        }

        return ['attempted' => $subscribers->count(), 'synced' => $synced, 'failed' => $failed];
    }

    /**
     * Batch sync for pending quiz responses.
     */
    public function syncPendingQuizResponses(int $limit = 100): array
    {
        $responses = \App\Models\QuizResponse::where('synced_to_marketing', false)
            ->whereNotNull('subscriber_id')
            ->where('status', 'completed')
            ->with('subscriber', 'quiz', 'outcome')
            ->limit($limit)
            ->get();

        $synced = 0;
        $failed = 0;

        foreach ($responses as $response) {
            if ($this->trackQuizCompleted($response)) {
                $synced++;
            } else {
                $failed++;
            }
        }

        return ['attempted' => $responses->count(), 'synced' => $synced, 'failed' => $failed];
    }
}
