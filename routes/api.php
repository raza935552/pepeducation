<?php

use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\JourneyController;
use App\Http\Controllers\Api\ConversionController;
use Illuminate\Support\Facades\Route;

// Tracking API
Route::post('/tracking', [TrackingController::class, 'track']);
Route::get('/tracking/session', [TrackingController::class, 'getSession']);

// Journey API (for shop integration)
Route::get('/journey/{sessionId}', [JourneyController::class, 'show']);

// Conversion Webhook (for shops to report purchases)
Route::post('/conversions', [ConversionController::class, 'store']);
