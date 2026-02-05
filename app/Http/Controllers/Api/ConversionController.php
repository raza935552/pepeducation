<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OutboundClick;
use App\Models\UserSession;
use App\Models\Setting;
use App\Services\Klaviyo\EventService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ConversionController extends Controller
{
    /**
     * Record a conversion from external shop
     *
     * POST /api/conversions
     */
    public function store(Request $request): JsonResponse
    {
        // Validate API key
        if (!$this->validateApiKey($request)) {
            return response()->json([
                'error' => 'Invalid or missing API key',
                'code' => 'INVALID_API_KEY',
            ], 401);
        }

        // Validate request
        $validated = $request->validate([
            'pp_session' => 'required|string|max:64',
            'order_id' => 'required|string|max:255',
            'order_value' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'products' => 'nullable|array',
            'products.*.name' => 'string|max:255',
            'products.*.sku' => 'string|max:100',
            'products.*.quantity' => 'integer|min:1',
            'products.*.price' => 'numeric|min:0',
        ]);

        // Find the outbound click by pp_session
        $click = OutboundClick::where('pp_session', $validated['pp_session'])->first();

        if (!$click) {
            return response()->json([
                'error' => 'Session not found',
                'code' => 'SESSION_NOT_FOUND',
            ], 404);
        }

        // Check if already converted
        if ($click->converted) {
            return response()->json([
                'error' => 'Conversion already recorded',
                'code' => 'ALREADY_CONVERTED',
                'conversion' => [
                    'order_id' => $click->order_id ?? 'unknown',
                    'converted_at' => $click->converted_at?->toIso8601String(),
                ],
            ], 409);
        }

        // Mark click as converted
        $click->update([
            'converted' => true,
            'converted_at' => now(),
            'conversion_value' => $validated['order_value'],
            'order_id' => $validated['order_id'],
        ]);

        // Increment link conversions
        $click->outboundLink?->increment('conversions_count');

        // Update user session
        $session = UserSession::where('session_id', $click->session_id)->first();
        if ($session) {
            $session->update([
                'converted' => true,
                'conversion_type' => 'purchase',
                'converted_at' => now(),
            ]);
        }

        // Track to Klaviyo
        $this->trackToKlaviyo($click, $validated);

        Log::info('Conversion recorded', [
            'pp_session' => $validated['pp_session'],
            'order_id' => $validated['order_id'],
            'order_value' => $validated['order_value'],
            'click_id' => $click->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversion recorded successfully',
            'conversion' => [
                'click_id' => $click->id,
                'order_id' => $validated['order_id'],
                'order_value' => $validated['order_value'],
                'segment' => $click->pp_segment,
                'engagement_score' => $click->pp_engagement_score,
                'converted_at' => now()->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Validate shop API key
     */
    protected function validateApiKey(Request $request): bool
    {
        $providedKey = $request->header('X-API-Key') ?? $request->input('api_key');

        if (!$providedKey) {
            return false;
        }

        // Use journey API key for now (can add shop-specific keys later)
        $storedKey = Setting::getValue('integrations', 'journey_api_key');

        return $storedKey && hash_equals($storedKey, $providedKey);
    }

    /**
     * Track purchase event to Klaviyo
     */
    protected function trackToKlaviyo(OutboundClick $click, array $data): void
    {
        if (!$click->subscriber_id) {
            return;
        }

        try {
            $eventService = app(EventService::class);
            $subscriber = $click->subscriber;

            if (!$subscriber) {
                return;
            }

            $eventService->track($subscriber, 'Purchased', [
                'order_id' => $data['order_id'],
                'order_value' => $data['order_value'],
                'currency' => $data['currency'] ?? 'USD',
                'products' => $data['products'] ?? [],
                'segment' => $click->pp_segment,
                'engagement_score' => $click->pp_engagement_score,
                'health_goal' => $click->pp_health_goal,
                'recommended_peptide' => $click->pp_recommended_peptide,
                'source_link' => $click->outboundLink?->name,
                'time_to_convert' => $click->created_at
                    ? now()->diffInMinutes($click->created_at)
                    : null,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to track conversion to Klaviyo', [
                'error' => $e->getMessage(),
                'click_id' => $click->id,
            ]);
        }
    }
}
