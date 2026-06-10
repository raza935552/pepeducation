<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Customer.io sync every 5 minutes for missed events
Schedule::command('customerio:sync --limit=50')->everyFiveMinutes();

// Publish scheduled blog posts every minute
Schedule::command('blog:publish-scheduled')->everyMinute();

// Mark stale in-progress quiz responses as abandoned (older than 24h)
Schedule::command('quiz:cleanup-abandoned')->daily();

// Auto-generate fresh, NON-DUPLICATE blog drafts on the 1st of each month (4am).
// Brainstorms new topics vs the current titles each run, so content never repeats.
// Drafts only — review + publish in Admin → Blog (add --publish to auto-publish).
Schedule::command('blog:auto-generate --count=4')->monthlyOn(1, '04:00')->withoutOverlapping();
