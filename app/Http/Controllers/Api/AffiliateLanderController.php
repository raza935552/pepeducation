<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lander;
use App\Models\OutboundLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Biolinx -> PP bridge for affiliate pre-landers. Biolinx is the system of
 * record (application, code, iDev id, bundle, images); PP only renders the
 * page. Upsert keyed by the affiliate's iDev id, so a rebuild updates the same
 * page and URL. Auth: the shared X-PP-Secret already used by the conversion
 * bridge. Always creates the matching outbound link (lp-{slug}) so every CTA
 * carries UTM + fbclid + the discount/idev params through /go.
 */
class AffiliateLanderController extends Controller
{
    public function store(Request $request)
    {
        $secret = (string) config('services.pp_conversions.secret');
        if ($secret === '' || ! hash_equals($secret, (string) $request->header('X-PP-Secret', ''))) {
            return response()->json(['ok' => false, 'error' => 'unauthorized'], 401);
        }

        $data = $request->validate([
            'idev_id' => 'required|integer|min:1',
            'slug' => 'required|string|max:100',
            'name' => 'required|string|max:160',
            'content' => 'required|array',
            'active' => 'nullable|boolean',
        ]);

        $idevId = (string) $data['idev_id'];
        $lander = Lander::where('template', 'affiliate-guide')->where('content->affiliate->idev_id', $idevId)->first();

        $slug = Str::slug($data['slug']) ?: 'affiliate-' . $idevId;
        if (! $lander || $lander->slug !== $slug) {
            // Keep the slug unique; a different affiliate may already own it.
            $base = $slug;
            for ($i = 0; Lander::where('slug', $slug)->when($lander, fn ($q) => $q->where('id', '!=', $lander->id))->exists(); $i++) {
                $slug = $base . '-' . $idevId . ($i ? $i : '');
            }
        }
        if ($lander && $lander->slug !== $slug) {
            $slug = $lander->slug; // never move a live URL on rebuild
        }

        $outboundSlug = 'lp-' . $slug;
        OutboundLink::updateOrCreate(
            ['slug' => $outboundSlug],
            ['name' => 'Lander: ' . $data['name'], 'destination_url' => 'https://biolinxlabs.com', 'utm_source' => 'affiliate', 'utm_medium' => 'referral', 'utm_campaign' => $slug,
             'append_segment' => true, 'append_session' => true, 'append_email' => true, 'append_quiz_data' => true, 'is_active' => true]
        );

        $content = $data['content'];
        $content['affiliate']['idev_id'] = $idevId;
        $lander = Lander::updateOrCreate(
            ['id' => $lander?->id ?? 0],
            ['slug' => $slug, 'name' => $data['name'], 'template' => 'affiliate-guide', 'outbound_slug' => $outboundSlug, 'is_active' => $data['active'] ?? true, 'noindex' => true, 'content' => $content]
        );

        Log::info('affiliate lander upserted from Biolinx', ['idev_id' => $idevId, 'slug' => $slug, 'lander_id' => $lander->id]);

        return response()->json(['ok' => true, 'id' => $lander->id, 'slug' => $lander->slug, 'url' => $lander->url]);
    }
}
