<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Gracefully skip if settings table doesn't exist yet (fresh deploy)
        try {
            $enabled = Setting::getValue('general', 'maintenance_enabled', false);
        } catch (\Throwable) {
            return $next($request);
        }

        if (!$enabled) {
            return $next($request);
        }

        // Always allow admin routes, auth routes, and maintenance unlock
        // Note: register is intentionally excluded — no sign-ups during maintenance
        if ($request->is('admin/*', 'login', 'logout', 'maintenance/*')) {
            return $next($request);
        }

        // Allow logged-in admins
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Allow if bypass cookie is set with valid hash (timing-safe comparison)
        $password = Setting::getValue('general', 'maintenance_password', '');
        $cookie = $request->cookie('pp_maintenance_bypass');
        if ($password && $cookie && hash_equals(hash('sha256', $password), $cookie)) {
            return $next($request);
        }

        // Show maintenance page
        $message = Setting::getValue('general', 'maintenance_message', 'We are getting things ready. Check back soon!');

        return response()->view('maintenance', [
            'message' => $message,
        ], 503);
    }
}
