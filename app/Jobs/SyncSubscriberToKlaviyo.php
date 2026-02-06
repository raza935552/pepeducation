<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Services\Klaviyo\KlaviyoService;
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
        $this->onQueue('klaviyo');
    }

    public function handle(KlaviyoService $klaviyo): void
    {
        if (!$klaviyo->isEnabled()) {
            return;
        }

        $klaviyo->syncProfile($this->subscriber);
        $klaviyo->trackSubscribed($this->subscriber, $this->source);
    }
}
