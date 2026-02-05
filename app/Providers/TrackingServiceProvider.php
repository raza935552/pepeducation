<?php

namespace App\Providers;

use App\Services\Tracking\TrackingManager;
use Illuminate\Support\ServiceProvider;

class TrackingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TrackingManager::class, function ($app) {
            return new TrackingManager($app['request']);
        });

        // Alias for convenience
        $this->app->alias(TrackingManager::class, 'tracking');
    }

    public function boot(): void
    {
        //
    }
}
