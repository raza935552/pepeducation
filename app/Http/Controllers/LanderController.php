<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Serves the paid-ad bridge landers (the "Operator Brief" editorial pages).
 *
 * Each lander seasons the shared Meta pixel (via <x-meta-pixel>, which also sets
 * _fbp/_fbc) and its single CTA routes through /go/{slug} (OutboundController),
 * which forwards UTM + the captured fbclid/fbp/fbc (+ email) on to Biolinx so the
 * Purchase CAPI there matches back to the original ad click.
 *
 * Slug => the OutboundLink slug its CTA points at (see database/seeders or the
 * outbound_links table). Both are kept here so the set of valid landers is explicit.
 *
 * EVERY lander load is logged to `lander_visits` AFTER the response is sent
 * (recordVisit → afterResponse), so the admin Ad Analytics dashboard has accurate
 * visit counts (the landers run no analytics JS — only the Meta pixel, which only
 * reports to Meta). This is automatic for any new lander — no per-lander wiring.
 */
class LanderController extends Controller
{
    /** Public lander slug => CTA outbound-link slug. */
    public const LANDERS = [
        '10-years'            => 'lp-10-years',
        'lying'               => 'lp-lying',
        'coas-worthless'      => 'lp-coas-worthless',
        'suppliers-identical' => 'lp-suppliers-identical',
        'vetted-47'           => 'lp-vetted-47',
    ];

    public function show(Request $request, string $slug): View
    {
        // Ad UTMs (Ad → Lander) are captured into the session by CaptureMetaClickIds.
        // Here we also stamp WHICH lander bridged the visit (+ its title), so the CTA
        // hand-off (/go → buildFinalUrl) forwards "Professor Peptides + lander details"
        // alongside the ad data → Biolinx knows: which ad, which PP lander, which page.

        // CMS-driven landers (editable in admin) take precedence.
        $lander = \App\Models\Lander::where('slug', $slug)->where('is_active', true)->first();
        if ($lander) {
            session(['pp_lander' => $slug, 'pp_lander_title' => $lander->c('meta.title') ?: $lander->name]);
            $this->recordVisit($request, $slug);
            return view("landers.templates.{$lander->template}", compact('lander'));
        }

        // Legacy static landers (the original 5).
        abort_unless(array_key_exists($slug, self::LANDERS), 404);

        session(['pp_lander' => $slug, 'pp_lander_title' => null]);
        $this->recordVisit($request, $slug);

        return view("landers.{$slug}");
    }

    /**
     * Log this lander load to `lander_visits` AFTER the response is sent, so it
     * adds zero latency to the page and can never break the render. Values are
     * read NOW (request scope); the DB write is deferred via afterResponse.
     */
    private function recordVisit(Request $request, string $slug): void
    {
        try {
            // Ad UTMs the visitor landed with — session (set by CaptureMetaClickIds)
            // first, falling back to the raw query string on the very first hit.
            $utm = [];
            foreach (['source', 'medium', 'campaign', 'content', 'term'] as $k) {
                $v = session('ad_utm_' . $k) ?: $request->query('utm_' . $k);
                $utm[$k] = $v ? mb_substr((string) $v, 0, 200) : null;
            }
            $fbclid = session('meta_fbclid') ?: $request->query('fbclid');
            $fbclid = $fbclid ? mb_substr((string) $fbclid, 0, 400) : null;

            $row = [
                'lander_slug'  => $slug,
                'session_id'   => $request->hasSession() ? $request->session()->getId() : null,
                'is_ad'        => (! empty($fbclid)) || (! empty($utm['source'])),
                'fbclid'       => $fbclid,
                'utm_source'   => $utm['source'],
                'utm_medium'   => $utm['medium'],
                'utm_campaign' => $utm['campaign'],
                'utm_content'  => $utm['content'],
                'utm_term'     => $utm['term'],
                'referer'      => mb_substr((string) $request->headers->get('referer'), 0, 500) ?: null,
                'ip'           => \App\Support\IpAnon::mask($request->ip()),
                'user_agent'   => mb_substr((string) $request->userAgent(), 0, 500) ?: null,
                'created_at'   => now(),
            ];

            dispatch(function () use ($row) {
                try {
                    \App\Models\LanderVisit::insert($row);
                } catch (\Throwable $e) {
                    // swallow — analytics must never affect the lander
                }
            })->afterResponse();
        } catch (\Throwable $e) {
            // never break the lander render for an analytics hiccup
        }
    }
}
