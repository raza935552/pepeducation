<?php

use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\JourneyController;
use App\Http\Controllers\Api\ConversionController;
use Illuminate\Support\Facades\Route;

// Tracking API - higher limit for frequent JS events
Route::middleware(['throttle:120,1', \App\Http\Middleware\ValidateTrackingOrigin::class])->group(function () {
    Route::post('/tracking', [TrackingController::class, 'track']);
    Route::get('/tracking/session', [TrackingController::class, 'getSession']);
});

// Journey API (for shop integration) - lower limit
Route::middleware(['throttle:30,1'])->group(function () {
    Route::get('/journey/{sessionId}', [JourneyController::class, 'show']);
});

// Conversion Webhook - strict limit
Route::middleware(['throttle:20,1'])->group(function () {
    Route::post('/conversions', [ConversionController::class, 'store']);
});
