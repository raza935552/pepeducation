<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $groups = [
            'integrations' => Setting::getGroup('integrations'),
            'tracking' => Setting::getGroup('tracking'),
            'scoring' => Setting::getGroup('scoring'),
            'general' => Setting::getGroup('general'),
        ];

        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('groups', 'settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.group' => 'required|string',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $setting) {
            Setting::setValue($setting['group'], $setting['key'], $setting['value']);
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    public function testKlaviyo()
    {
        $klaviyo = new \App\Services\Klaviyo\KlaviyoService();

        if (!$klaviyo->isEnabled()) {
            return response()->json(['success' => false, 'message' => 'Klaviyo is not enabled']);
        }

        // Try to get lists to verify connection
        $client = new \App\Services\Klaviyo\KlaviyoClient();
        $response = $client->get('/lists/');

        if ($response) {
            return response()->json([
                'success' => true,
                'message' => 'Klaviyo connection successful',
                'lists' => collect($response['data'] ?? [])->map(fn($l) => [
                    'id' => $l['id'],
                    'name' => $l['attributes']['name'] ?? 'Unknown',
                ])->toArray(),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to connect to Klaviyo']);
    }
}
