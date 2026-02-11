<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private const ALLOWED_GROUPS = ['integrations', 'tracking', 'scoring', 'general', 'stack_builder'];

    public function index()
    {
        $groups = [];
        foreach (self::ALLOWED_GROUPS as $group) {
            $groups[$group] = Setting::getGroup($group);
        }

        $settings = Setting::whereIn('group', self::ALLOWED_GROUPS)
            ->get()
            ->groupBy('group');

        return view('admin.settings.index', compact('groups', 'settings'));
    }

    public function update(Request $request)
    {
        $allowedGroups = implode(',', self::ALLOWED_GROUPS);

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.group' => "required|string|in:{$allowedGroups}",
            'settings.*.key' => 'required|string|max:100|regex:/^[a-z0-9_]+$/',
            'settings.*.value' => 'nullable|string|max:2000',
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
