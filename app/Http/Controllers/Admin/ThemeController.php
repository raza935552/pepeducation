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
        $rules = [];
        foreach (array_keys(ThemeService::DEFAULTS) as $key) {
            $rules[$key] = 'required|string|regex:/^#[0-9A-Fa-f]{6}$/';
        }
        $validated = $request->validate($rules);

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
