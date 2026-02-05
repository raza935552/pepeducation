<?php

namespace App\Console\Commands;

use App\Services\Klaviyo\KlaviyoService;
use Illuminate\Console\Command;

class SyncKlaviyoCommand extends Command
{
    protected $signature = 'klaviyo:sync {--limit=100}';
    protected $description = 'Sync pending profiles and quiz responses to Klaviyo';

    public function handle(KlaviyoService $klaviyo): int
    {
        if (!$klaviyo->isEnabled()) {
            $this->warn('Klaviyo is not enabled. Check your API keys.');
            return 0;
        }

        $limit = (int) $this->option('limit');

        $this->info('Syncing pending profiles...');
        $profiles = $klaviyo->syncPendingProfiles($limit);
        $this->info("Synced {$profiles} profiles.");

        $this->info('Syncing pending quiz responses...');
        $responses = $klaviyo->syncPendingQuizResponses($limit);
        $this->info("Synced {$responses} quiz responses.");

        return 0;
    }
}
