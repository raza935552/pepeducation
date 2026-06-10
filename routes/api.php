<?php

use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\JourneyController;
use App\Http\Controllers\Api\ConversionIngestController;
use Illuminate\Support\Facades\Route;

// Biolinx → PP conversion bridge (revenue per lander/campaign for Ad Analytics).
// Secret-verified inside the controller (X-PP-Secret header); no session/CSRF.
Route::post('/pp/conversions', [ConversionIngestController::class, 'store']);

// Tracking API
Route::middleware([\App\Http\Middleware\ValidateTrackingOrigin::class])->group(function () {
    Route::post('/tracking', [TrackingController::class, 'track']);
    Route::get('/tracking/session', [TrackingController::class, 'getSession']);
});

// Journey API (for shop integration)
Route::get('/journey/{sessionId}', [JourneyController::class, 'show']);
