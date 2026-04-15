<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerIoSetting;
use App\Services\CustomerIo\CustomerIoClient;
use Illuminate\Http\Request;

class CustomerIoSettingController extends Controller
{
    public function edit()
    {
        $settings = CustomerIoSetting::getOrCreate();
        return view('admin.settings.customerio', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = CustomerIoSetting::getOrCreate();

        $validated = $request->validate([
            'is_enabled' => 'boolean',
            'region' => 'required|in:us,eu',
            'track_quiz_started' => 'boolean',
            'track_quiz_completed' => 'boolean',
            'track_email_captured' => 'boolean',
            'track_quiz_abandoned' => 'boolean',
            'track_lead_magnet_download' => 'boolean',
            'track_outbound_click' => 'boolean',
            'track_stack_completed' => 'boolean',
            'track_subscribed' => 'boolean',
            'track_peptide_paired' => 'boolean',
            'enable_page_tracking' => 'boolean',
        ]);

        if ($request->filled('site_id')) {
            $request->validate(['site_id' => 'string|max:64|regex:/^[a-f0-9]+$/']);
            $settings->site_id = $request->input('site_id');
        }
        if ($request->filled('api_key')) {
            $request->validate(['api_key' => 'string|max:64|regex:/^[a-f0-9]+$/']);
            $settings->api_key = $request->input('api_key');
        }

        $settings->fill($validated);
        $settings->save();

        return back()->with('success', 'Customer.io settings updated.');
    }

    public function test(Request $request)
    {
        $settings = CustomerIoSetting::getOrCreate();

        if ($request->filled('site_id')) {
            $settings->site_id = $request->input('site_id');
        }
        if ($request->filled('api_key')) {
            $settings->api_key = $request->input('api_key');
        }
        if ($request->filled('region')) {
            $settings->region = $request->input('region');
        }

        $client = new CustomerIoClient($settings);

        if (!$client->hasCredentials()) {
            return response()->json(['success' => false, 'message' => 'No credentials configured.']);
        }

        $response = $client->testConnection();

        if ($response->isSuccess()) {
            return response()->json(['success' => true, 'message' => 'Customer.io connection successful!']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Connection failed: ' . ($response->getError() ?? 'Unknown error'),
        ]);
    }
}
