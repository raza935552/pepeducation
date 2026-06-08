<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LanderController as PublicLanderController;
use App\Models\Lander;
use App\Models\OutboundLink;
use Illuminate\Http\Request;

/**
 * Admin manager for the bridge landers. CMS landers (the `landers` table) are fully
 * editable here — content is a structured tree of fixed slots, so marketing edits copy
 * / links / images without ever touching layout. The 5 legacy static landers are listed
 * read-only (view link only) until/if they're migrated into the CMS.
 */
class LanderController extends Controller
{
    public function index()
    {
        $cmsLanders = Lander::orderBy('name')->get();
        $staticLanders = collect(PublicLanderController::LANDERS)->keys();

        return view('admin.landers.index', compact('cmsLanders', 'staticLanders'));
    }

    public function edit(Lander $lander)
    {
        // UTM lives on the CTA OutboundLink — surface it so it's editable in one place.
        $outbound = $lander->outbound_slug ? OutboundLink::where('slug', $lander->outbound_slug)->first() : null;

        return view('admin.landers.edit', compact('lander', 'outbound'));
    }

    public function update(Request $request, Lander $lander)
    {
        $data = $request->validate([
            'name' => 'required|string|max:160',
            'is_active' => 'nullable|boolean',
            'noindex' => 'nullable|boolean',
            'content' => 'required|array',
            // CTA UTM (on the OutboundLink)
            'utm_source' => 'nullable|string|max:80',
            'utm_medium' => 'nullable|string|max:80',
            'utm_campaign' => 'nullable|string|max:120',
            'utm_content' => 'nullable|string|max:120',
        ]);

        // Merge the posted content over the existing tree so a missing field never
        // wipes data (and the fixed slot shape is preserved → layout stays intact).
        $merged = $this->mergeContent($lander->content ?? [], $data['content']);

        $lander->update([
            'name' => $data['name'],
            'is_active' => $request->boolean('is_active'),
            'noindex' => $request->boolean('noindex'),
            'content' => $merged,
        ]);

        // Push UTM to the CTA OutboundLink (one source of truth for tracking).
        if ($lander->outbound_slug && ($ob = OutboundLink::where('slug', $lander->outbound_slug)->first())) {
            $ob->update([
                'utm_source' => $data['utm_source'] ?? $ob->utm_source,
                'utm_medium' => $data['utm_medium'] ?? $ob->utm_medium,
                'utm_campaign' => $data['utm_campaign'] ?? $ob->utm_campaign,
                'utm_content' => $data['utm_content'] ?? $ob->utm_content,
            ]);
        }

        return redirect()->route('admin.landers.edit', $lander)->with('success', 'Lander saved.');
    }

    /** Recursively overlay $new onto $old (arrays merged by key; scalars replaced). */
    private function mergeContent(array $old, array $new): array
    {
        foreach ($new as $key => $value) {
            if (is_array($value) && isset($old[$key]) && is_array($old[$key])) {
                $old[$key] = $this->mergeContent($old[$key], $value);
            } else {
                $old[$key] = $value;
            }
        }
        return $old;
    }
}
