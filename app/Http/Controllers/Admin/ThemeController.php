<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ThemeService;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function edit()
    {
        $colors = ThemeService::getThemeColors();
        $defaults = ThemeService::DEFAULTS;

        return view('admin.settings.theme', compact('colors', 'defaults'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'primary'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'bg'        => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'heading'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text'      => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'dark'      => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue('theme', $key, $value);
        }

        ThemeService::clearCache();

        return back()->with('success', 'Theme colors updated. Refresh the site to see changes.');
    }

    public function resetDefaults()
    {
        foreach (ThemeService::DEFAULTS as $key => $value) {
            Setting::setValue('theme', $key, $value);
        }

        ThemeService::clearCache();

        return back()->with('success', 'Theme reset to default colors.');
    }
}
