<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\UserEvent;
use App\Models\UserSession;
use App\Models\TrackingError;
use Illuminate\Console\Command;

class CleanupTrackingData extends Command
{
    protected $signature = 'tracking:cleanup {--days= : Override retention days}';
    protected $description = 'Delete tracking data older than retention period and recalculate engagement tiers';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?? Setting::getValue('tracking', 'data_retention_days', 365));

        $this->info("Cleaning tracking data older than {$days} days...");

        $events = UserEvent::olderThan($days)->delete();
        $this->line("  Deleted {$events} events");

        $sessions = UserSession::olderThan($days)->delete();
        $this->line("  Deleted {$sessions} sessions");

        $errors = TrackingError::where('created_at', '<', now()->subDays(min($days, 90)))->delete();
        $this->line("  Deleted {$errors} tracking errors");

        // Recalculate engagement tiers in batch
        $tiers = Subscriber::recalculateAllTiers();
        $this->line("  Updated {$tiers} subscriber engagement tiers");

        $this->info('Cleanup complete.');

        return self::SUCCESS;
    }
}
