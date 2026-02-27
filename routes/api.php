<?php

use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\JourneyController;
use App\Http\Controllers\Api\ConversionController;
use Illuminate\Support\Facades\Route;

// Tracking API
Route::middleware([\App\Http\Middleware\ValidateTrackingOrigin::class])->group(function () {
    Route::post('/tracking', [TrackingController::class, 'track']);
    Route::get('/tracking/session', [TrackingController::class, 'getSession']);
});

// Journey API (for shop integration)
Route::get('/journey/{sessionId}', [JourneyController::class, 'show']);

// Conversion Webhook
Route::post('/conversions', [ConversionController::class, 'store']);
