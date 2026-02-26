<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $correctPassword = Setting::getValue('general', 'maintenance_password', '');

        if (!$correctPassword || !hash_equals($correctPassword, $request->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $cookie = cookie(
            'pp_maintenance_bypass',
            hash('sha256', $correctPassword),
            60 * 24,     // 24 hours
            '/',         // path
            null,        // domain (use default)
            true,        // secure (HTTPS only)
            true,        // httpOnly
            false,       // raw
            'Lax'        // sameSite
        );

        return redirect('/')
            ->withCookie($cookie);
    }
}
