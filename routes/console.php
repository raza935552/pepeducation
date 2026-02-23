<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Klaviyo sync every 5 minutes for missed events
Schedule::command('klaviyo:sync --limit=50')->everyFiveMinutes();

// Publish scheduled blog posts every minute
Schedule::command('blog:publish-scheduled')->everyMinute();

// Mark stale in-progress quiz responses as abandoned (older than 24h)
Schedule::command('quiz:cleanup-abandoned')->daily();
