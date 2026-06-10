<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Logs the FIRST page each visitor lands on — the exact incoming link (full URL
 * with query string), referrer, and ad source (utm/fbclid) — once per session,
 * to the visitor_entries table. Written AFTER the response (afterResponse), fully
 * guarded, so it adds no latency and can never break a request.
 *
 * Covers EVERY visitor server-side, including lander/ad traffic that runs no
 * analytics JS (and so never appears in user_sessions). Reference log only.
 */
class LogVisitorEntry
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // Only real page views: GET, has a session, not already logged this session.
            if (! $request->isMethod('GET') || ! $request->hasSession()) {
                return $response;
            }
            $session = $request->session();
            if ($session->get('visitor_entry_logged')) {
                return $response;
            }

            // Skip non-page paths (admin, api, webhooks, assets, sitemap, etc.).
            $path = ltrim($request->path(), '/');
            $skip = ['admin', 'api', 'webhooks', 'go', 'storage', 'build', 'vendor', 'livewire', 'sitemap', 'robots.txt', 'favicon.ico', 'up'];
            foreach ($skip as $p) {
                if ($path === $p || str_starts_with($path, $p . '/') || str_starts_with($path, $p . '.')) {
                    return $response;
                }
            }
            // Only log HTML responses (skip JSON/redirects/files).
            $ct = (string) $response->headers->get('content-type');
            if ($ct !== '' && ! str_contains($ct, 'text/html')) {
                return $response;
            }

            // Mark first as logged immediately so concurrent requests don't double-log.
            $session->put('visitor_entry_logged', true);

            $ref = (string) $request->headers->get('referer');
            $refHost = $ref ? parse_url($ref, PHP_URL_HOST) : null;
            $ua = (string) $request->userAgent();
            $isBot = (bool) preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|crawler|fetch/i', $ua);
            $isMobile = (bool) preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $ua);

            // Ad UTMs: session (CaptureMetaClickIds) first, then this request's query.
            $utm = [];
            foreach (['source', 'medium', 'campaign', 'content', 'term'] as $k) {
                $v = session('ad_utm_' . $k) ?: $request->query('utm_' . $k);
                $utm[$k] = $v ? mb_substr((string) $v, 0, 200) : null;
            }
            $fbclid = session('meta_fbclid') ?: $request->query('fbclid');
            $fbclid = $fbclid ? mb_substr((string) $fbclid, 0, 400) : null;

            $row = [
                'session_id'      => $session->getId(),
                'landing_url'     => mb_substr($request->fullUrl(), 0, 2000),
                'path'            => mb_substr($path === '' ? '/' : $path, 0, 255),
                'referrer'        => $ref ? mb_substr($ref, 0, 2000) : null,
                'referrer_domain' => $refHost ? mb_substr($refHost, 0, 255) : null,
                'is_ad'           => (! empty($fbclid)) || (! empty($utm['source'])),
                'utm_source'      => $utm['source'],
                'utm_medium'      => $utm['medium'],
                'utm_campaign'    => $utm['campaign'],
                'utm_content'     => $utm['content'],
                'utm_term'        => $utm['term'],
                'fbclid'          => $fbclid,
                'device'          => $isBot ? 'bot' : ($isMobile ? 'mobile' : 'desktop'),
                'ip'              => \App\Support\IpAnon::mask($request->ip()),
                'user_agent'      => mb_substr($ua, 0, 500) ?: null,
                'created_at'      => now(),
            ];

            dispatch(function () use ($row) {
                try {
                    \App\Models\VisitorEntry::insert($row);
                } catch (\Throwable $e) {
                    // swallow — logging must never affect the request
                }
            })->afterResponse();
        } catch (\Throwable $e) {
            // never break a request for the visitor log
        }

        return $response;
    }
}
