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
            // Skip empty encrypted fields (user left blank = keep existing value)
            $existing = Setting::where('group', $setting['group'])->where('key', $setting['key'])->first();
            if ($existing && $existing->type === 'encrypted' && empty($setting['value'])) {
                continue;
            }

            Setting::setValue($setting['group'], $setting['key'], $setting['value']);
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    public function testKlaviyo()
    {
        $client = new \App\Services\Klaviyo\KlaviyoClient();

        if (!$client->isEnabled()) {
            return response()->json(['success' => false, 'message' => 'Klaviyo is not enabled. Turn on the toggle and save settings first.']);
        }

        $response = $client->get('/lists/');

        if ($response) {
            return response()->json([
                'success' => true,
                'message' => 'Klaviyo connection successful!',
                'lists' => collect($response['data'] ?? [])->map(fn($l) => [
                    'id' => $l['id'],
                    'name' => $l['attributes']['name'] ?? 'Unknown',
                ])->toArray(),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to connect to Klaviyo. Check your Private API Key.']);
    }
}
