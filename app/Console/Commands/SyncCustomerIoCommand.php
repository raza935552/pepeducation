<?php

namespace App\Console\Commands;

use App\Services\CustomerIo\CustomerIoService;
use Illuminate\Console\Command;

class SyncCustomerIoCommand extends Command
{
    protected $signature = 'customerio:sync {--limit=100}';
    protected $description = 'Sync pending profiles and quiz responses to Customer.io';

    public function handle(): int
    {
        $customerIo = CustomerIoService::make();

        if (!$customerIo->isEnabled()) {
            $this->warn('Customer.io is not enabled. Check your credentials.');
            return 0;
        }

        $limit = (int) $this->option('limit');

        $this->info('Syncing pending profiles...');
        $profiles = $customerIo->syncPendingProfiles($limit);
        $this->info("Profiles: {$profiles['synced']}/{$profiles['attempted']} synced" .
            ($profiles['failed'] ? ", {$profiles['failed']} failed" : ''));

        $this->info('Syncing pending quiz responses...');
        $responses = $customerIo->syncPendingQuizResponses($limit);
        $this->info("Responses: {$responses['synced']}/{$responses['attempted']} synced" .
            ($responses['failed'] ? ", {$responses['failed']} failed" : ''));

        if ($profiles['failed'] > 0 || $responses['failed'] > 0) {
            $this->warn('Some items failed to sync. Check logs for details.');
        }

        return 0;
    }
}
