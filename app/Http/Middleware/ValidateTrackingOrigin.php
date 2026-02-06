<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTrackingOrigin
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->header('Origin') ?? $request->header('Referer');

        if (!$origin) {
            // Allow sendBeacon requests (no Origin header in some browsers)
            if ($request->header('Content-Type') === 'text/plain;charset=UTF-8') {
                return $next($request);
            }
        }

        if ($origin) {
            $allowed = parse_url(config('app.url'), PHP_URL_HOST);
            $requestHost = parse_url($origin, PHP_URL_HOST);

            if ($allowed && $requestHost && $requestHost !== $allowed) {
                return response()->json(['error' => 'Invalid origin'], 403);
            }
        }

        return $next($request);
    }
}
