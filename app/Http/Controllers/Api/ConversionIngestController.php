<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LanderConversion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Server-to-server ingest for Biolinx → PP order conversions (the revenue bridge).
 * Biolinx's pp:push-conversions command POSTs attributed orders here; we upsert
 * by biolinx_order_id so repeated pushes are idempotent. Auth is a shared secret
 * (X-PP-Secret header) — no user session.
 */
class ConversionIngestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $secret = (string) config('services.pp_conversions.secret');
        $given = (string) $request->header('X-PP-Secret', '');
        if ($secret === '' || ! hash_equals($secret, $given)) {
            return response()->json(['ok' => false, 'error' => 'unauthorized'], 401);
        }

        $rows = $request->input('conversions', []);
        if (! is_array($rows)) {
            return response()->json(['ok' => false, 'error' => 'invalid payload'], 422);
        }

        $upserted = 0;
        foreach ($rows as $r) {
            $orderId = isset($r['biolinx_order_id']) ? (string) $r['biolinx_order_id'] : '';
            if ($orderId === '') {
                continue;
            }
            try {
                LanderConversion::updateOrCreate(
                    ['biolinx_order_id' => $orderId],
                    [
                        'pp_lander'    => $this->str($r['pp_lander'] ?? null, 120),
                        'utm_source'   => $this->str($r['utm_source'] ?? null, 200),
                        'utm_medium'   => $this->str($r['utm_medium'] ?? null, 200),
                        'utm_campaign' => $this->str($r['utm_campaign'] ?? null, 200),
                        'utm_content'  => $this->str($r['utm_content'] ?? null, 200),
                        'utm_term'     => $this->str($r['utm_term'] ?? null, 200),
                        'fbclid'       => $this->str($r['fbclid'] ?? null, 400),
                        'revenue'      => round((float) ($r['revenue'] ?? 0), 2),
                        'currency'     => $this->str($r['currency'] ?? 'USD', 8) ?: 'USD',
                        'order_type'   => $this->str($r['order_type'] ?? null, 20),
                        'status'       => $this->str($r['status'] ?? null, 30),
                        'ordered_at'   => ! empty($r['ordered_at']) ? Carbon::parse($r['ordered_at']) : null,
                    ]
                );
                $upserted++;
            } catch (\Throwable $e) {
                // skip the bad row, keep ingesting the rest
            }
        }

        return response()->json(['ok' => true, 'upserted' => $upserted]);
    }

    private function str($v, int $max): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        return mb_substr((string) $v, 0, $max);
    }
}
