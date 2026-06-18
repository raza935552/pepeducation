<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCommunityEnabled
{
    /**
     * Gate the whole community behind a feature flag.
     * While disabled it is invisible (404) to everyone except admins,
     * so we can dark-launch + seed before going live.
     *
     * Also tags every community response as noindex (defence-in-depth with
     * robots.txt + the meta tag) so it can never be cached or indexed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = (bool) Setting::getValue('community', 'enabled', false);
        $user = $request->user();

        if (! $enabled && ! ($user && $user->isAdmin())) {
            abort(404);
        }

        // Lightweight presence: refresh last_seen_at at most every 5 minutes.
        if ($user && (! $user->last_seen_at || $user->last_seen_at->lt(now()->subMinutes(5)))) {
            $user->forceFill(['last_seen_at' => now()])->saveQuietly();
        }

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');

        return $response;
    }
}
