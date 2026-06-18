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

// Community seed-content drip — no-ops unless community.drip_enabled is on.
// Keeps the forum looking active before/while real traction builds.
Schedule::command('community:drip --count=1')->everySixHours()->withoutOverlapping();

// Drain the database queue each minute (reply notifications, etc.) as a safety
// net in case no long-running queue worker is supervised.
Schedule::command('queue:work --stop-when-empty --max-time=55 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();

// Auto-generate + PUBLISH fresh, NON-DUPLICATE blog posts on the 1st of each month (4am).
// Brainstorms new topics vs the current titles each run, so content never repeats.
// Posts go live automatically; compliance/research-framing is enforced in the prompt.
// (Drop --publish to switch back to draft-for-review.)
Schedule::command('blog:auto-generate --count=4 --publish')->monthlyOn(1, '04:00')->withoutOverlapping();
