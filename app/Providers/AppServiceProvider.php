<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\Klaviyo\KlaviyoClient::class);
        $this->app->singleton(\App\Services\Klaviyo\ProfileService::class);
        $this->app->singleton(\App\Services\Klaviyo\EventService::class);
        $this->app->singleton(\App\Services\Klaviyo\KlaviyoService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        // Bypass all throttle middleware on .test domains
        if (str_contains(request()->getHost() ?? '', '.test')) {
            RateLimiter::for('throttle', fn () => Limit::none());
        }
    }

    /** Check if current request is on a .test domain (for Livewire rate limits). */
    public static function isTestEnv(): bool
    {
        return str_contains(request()->getHost() ?? '', '.test');
    }
}
