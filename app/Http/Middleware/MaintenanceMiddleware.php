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
        // Check if maintenance mode is enabled
        if (!Setting::getValue('general', 'maintenance_enabled', false)) {
            return $next($request);
        }

        // Always allow admin routes and auth routes
        if ($request->is('admin/*', 'login', 'logout', 'register', 'maintenance/*')) {
            return $next($request);
        }

        // Allow logged-in admins
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Allow if bypass cookie is set with valid hash
        $password = Setting::getValue('general', 'maintenance_password', '');
        if ($password && $request->cookie('pp_maintenance_bypass') === hash('sha256', $password)) {
            return $next($request);
        }

        // Show maintenance page
        $message = Setting::getValue('general', 'maintenance_message', 'We are getting things ready. Check back soon!');

        return response()->view('maintenance', [
            'message' => $message,
        ], 503);
    }
}
