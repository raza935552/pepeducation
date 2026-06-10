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
            // A/B test: 'A' = control (this template), 'B' = the AI-built {template}-b.
            // null = no test running for this lander (everyone gets A).
            $variant = $this->resolveVariant($request, $lander);
            session(['pp_lander' => $slug, 'pp_lander_title' => $lander->c('meta.title') ?: $lander->name, 'lander_variant' => $variant]);
            $this->recordVisit($request, $slug, $variant);

            $template = "landers.templates.{$lander->template}";
            if ($variant === 'B') {
                // Prefer a per-lander B override ({template}-b-{slug}, e.g. its own bold copy),
                // else fall back to the shared content-driven B design for that template.
                $perLander = "{$template}-b-{$lander->slug}";
                $template = view()->exists($perLander) ? $perLander : "{$template}-b";
            }
            return view($template, compact('lander'));
        }

        // Legacy static landers (the original 5).
        abort_unless(array_key_exists($slug, self::LANDERS), 404);

        session(['pp_lander' => $slug, 'pp_lander_title' => null, 'lander_variant' => null]);
        $this->recordVisit($request, $slug, null);

        return view("landers.{$slug}");
    }

    /**
     * Log this lander load to `lander_visits` AFTER the response is sent, so it
     * adds zero latency to the page and can never break the render. Values are
     * read NOW (request scope); the DB write is deferred via afterResponse.
     */
    /**
     * Resolve the A/B variant for a CMS lander.
     *  - Returns null when there is no test (everyone gets the A control).
     *  - `?v=a` / `?v=b` is always honored (lets us QA B before flipping the test live).
     *  - When ab_test.enabled is set on the lander AND a {template}-b view exists,
     *    visitors are split 50/50 and pinned to their variant by a 30-day cookie.
     */
    private function resolveVariant(Request $request, \App\Models\Lander $lander): ?string
    {
        if (! view()->exists("landers.templates.{$lander->template}-b")) {
            return null; // no B template -> no test possible
        }

        // QA override (works even before the test is enabled, for previewing B).
        $q = strtoupper((string) $request->query('v', ''));
        if (in_array($q, ['A', 'B'], true)) {
            return $q;
        }

        // Real 50/50 split only once the test is switched on for this lander.
        if (! $lander->c('ab_test.enabled')) {
            return null;
        }

        $cookieName = 'pp_ab_' . $lander->slug;
        $cookie = $request->cookie($cookieName);
        if (in_array($cookie, ['A', 'B'], true)) {
            return $cookie;
        }

        $variant = random_int(0, 1) === 1 ? 'B' : 'A';
        \Illuminate\Support\Facades\Cookie::queue($cookieName, $variant, 43200); // 30 days
        return $variant;
    }

    private function recordVisit(Request $request, string $slug, ?string $variant = null): void
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
                'variant'      => $variant,
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
