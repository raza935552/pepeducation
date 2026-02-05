<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index(); // integrations, tracking, general
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, bool, json, encrypted
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Can be exposed to frontend
            $table->timestamps();

            $table->unique(['group', 'key']);
        });

        // Insert default settings
        $this->seedDefaultSettings();
    }

    private function seedDefaultSettings(): void
    {
        $settings = [
            // Klaviyo Integration
            ['group' => 'integrations', 'key' => 'klaviyo_public_key', 'value' => '', 'type' => 'string', 'description' => 'Klaviyo Public API Key'],
            ['group' => 'integrations', 'key' => 'klaviyo_private_key', 'value' => '', 'type' => 'encrypted', 'description' => 'Klaviyo Private API Key'],
            ['group' => 'integrations', 'key' => 'klaviyo_default_list_id', 'value' => '', 'type' => 'string', 'description' => 'Default Klaviyo List ID'],
            ['group' => 'integrations', 'key' => 'klaviyo_enabled', 'value' => 'false', 'type' => 'bool', 'description' => 'Enable Klaviyo Integration'],

            // Fast Peptix Integration
            ['group' => 'integrations', 'key' => 'fastpeptix_domain', 'value' => 'https://fastpeptix.com', 'type' => 'string', 'description' => 'Fast Peptix Domain'],
            ['group' => 'integrations', 'key' => 'fastpeptix_webhook_secret', 'value' => '', 'type' => 'encrypted', 'description' => 'Fast Peptix Webhook Secret'],
            ['group' => 'integrations', 'key' => 'fastpeptix_session_param', 'value' => 'pp_session', 'type' => 'string', 'description' => 'Session Parameter Name'],

            // Tracking Pixels
            ['group' => 'tracking', 'key' => 'ga4_measurement_id', 'value' => '', 'type' => 'string', 'description' => 'Google Analytics 4 Measurement ID'],
            ['group' => 'tracking', 'key' => 'facebook_pixel_id', 'value' => '', 'type' => 'string', 'description' => 'Facebook Pixel ID'],
            ['group' => 'tracking', 'key' => 'tiktok_pixel_id', 'value' => '', 'type' => 'string', 'description' => 'TikTok Pixel ID'],

            // Tracking Settings
            ['group' => 'tracking', 'key' => 'track_scroll_depth', 'value' => 'true', 'type' => 'bool', 'description' => 'Track Scroll Depth'],
            ['group' => 'tracking', 'key' => 'track_time_on_page', 'value' => 'true', 'type' => 'bool', 'description' => 'Track Time on Page'],
            ['group' => 'tracking', 'key' => 'track_clicks', 'value' => 'true', 'type' => 'bool', 'description' => 'Track All Clicks'],
            ['group' => 'tracking', 'key' => 'track_rage_clicks', 'value' => 'true', 'type' => 'bool', 'description' => 'Track Rage Clicks'],
            ['group' => 'tracking', 'key' => 'track_form_interactions', 'value' => 'true', 'type' => 'bool', 'description' => 'Track Form Field Interactions'],
            ['group' => 'tracking', 'key' => 'track_errors', 'value' => 'true', 'type' => 'bool', 'description' => 'Track JavaScript Errors'],
            ['group' => 'tracking', 'key' => 'session_timeout_minutes', 'value' => '30', 'type' => 'string', 'description' => 'Session Timeout (minutes)'],

            // Engagement Scoring
            ['group' => 'scoring', 'key' => 'points_page_view', 'value' => '1', 'type' => 'string', 'description' => 'Points per Page View'],
            ['group' => 'scoring', 'key' => 'points_scroll_75', 'value' => '2', 'type' => 'string', 'description' => 'Points for 75% Scroll'],
            ['group' => 'scoring', 'key' => 'points_time_60s', 'value' => '2', 'type' => 'string', 'description' => 'Points for 60s on Page'],
            ['group' => 'scoring', 'key' => 'points_peptide_view', 'value' => '3', 'type' => 'string', 'description' => 'Points per Peptide View'],
            ['group' => 'scoring', 'key' => 'points_calculator_use', 'value' => '5', 'type' => 'string', 'description' => 'Points for Calculator Use'],
            ['group' => 'scoring', 'key' => 'points_quiz_start', 'value' => '3', 'type' => 'string', 'description' => 'Points for Quiz Start'],
            ['group' => 'scoring', 'key' => 'points_quiz_complete', 'value' => '10', 'type' => 'string', 'description' => 'Points for Quiz Complete'],
            ['group' => 'scoring', 'key' => 'points_lead_magnet', 'value' => '8', 'type' => 'string', 'description' => 'Points for Lead Magnet Download'],
            ['group' => 'scoring', 'key' => 'points_product_click', 'value' => '10', 'type' => 'string', 'description' => 'Points for Product Click'],
            ['group' => 'scoring', 'key' => 'points_return_visit', 'value' => '5', 'type' => 'string', 'description' => 'Points for Return Visit'],
            ['group' => 'scoring', 'key' => 'tier_hot_threshold', 'value' => '40', 'type' => 'string', 'description' => 'Hot Lead Threshold'],
            ['group' => 'scoring', 'key' => 'tier_warm_threshold', 'value' => '15', 'type' => 'string', 'description' => 'Warm Lead Threshold'],

            // General
            ['group' => 'general', 'key' => 'site_name', 'value' => 'Professor Peptides', 'type' => 'string', 'description' => 'Site Name'],
            ['group' => 'general', 'key' => 'default_utm_source', 'value' => 'professorpeptides', 'type' => 'string', 'description' => 'Default UTM Source'],
        ];

        foreach ($settings as $setting) {
            \DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
