<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Services\CustomerIo\CustomerIoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSubscriberToCustomerIo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public Subscriber $subscriber,
        public string $source = 'unknown',
    ) {
        $this->onQueue('marketing');
    }

    public function handle(): void
    {
        $customerIo = CustomerIoService::make();

        if (!$customerIo->isEnabled()) {
            return;
        }

        $this->subscriber->refresh();

        $profileId = $customerIo->syncSubscriber($this->subscriber);

        if (!$profileId) {
            logger()->warning('Customer.io sync job: profile creation failed, will retry', [
                'subscriber_id' => $this->subscriber->id,
                'attempt' => $this->attempts(),
            ]);
            $this->release($this->backoff[$this->attempts() - 1] ?? 60);
            return;
        }

        $customerIo->trackSubscribed($this->subscriber, $this->source);
    }
}
