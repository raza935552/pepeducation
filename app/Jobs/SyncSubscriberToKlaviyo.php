<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Services\CustomerIo\CustomerIoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSubscriberToKlaviyo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public Subscriber $subscriber,
        public string $source = 'unknown',
    ) {
        $this->onQueue('customerio');
    }

    public function handle(CustomerIoService $customerIo): void
    {
        if (!$customerIo->isEnabled()) {
            return;
        }

        // Refresh subscriber to get latest data (may have changed since queued)
        $this->subscriber->refresh();

        $profileId = $customerIo->syncProfile($this->subscriber);

        if (!$profileId) {
            logger()->warning('Customer.io sync job: profile creation failed, will retry', [
                'subscriber_id' => $this->subscriber->id,
                'attempt' => $this->attempts(),
            ]);
            $this->release($this->backoff[$this->attempts() - 1] ?? 60);
            return;
        }

        // Subscribe to list with email marketing consent
        $customerIo->subscribeToList($this->subscriber);

        $customerIo->trackSubscribed($this->subscriber, $this->source);
    }
}
