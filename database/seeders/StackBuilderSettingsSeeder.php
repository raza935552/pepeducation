<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class StackBuilderSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'hero_title', 'value' => 'Build Your Peptide Stack', 'type' => 'string', 'description' => 'Main heading on the stack builder page'],
            ['key' => 'hero_subtitle', 'value' => 'Select your goal and we\'ll recommend the perfect peptide combination for you.', 'type' => 'string', 'description' => 'Subtitle text below the main heading'],
            ['key' => 'professor_picks_title', 'value' => 'Professor\'s Picks', 'type' => 'string', 'description' => 'Section title for curated bundles'],
            ['key' => 'products_title', 'value' => 'Recommended Products', 'type' => 'string', 'description' => 'Section title for individual products'],
            ['key' => 'browse_all_title', 'value' => 'I Know What I Need', 'type' => 'string', 'description' => 'Section title for browsing all products'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['group' => 'stack_builder', 'key' => $setting['key']],
                array_merge($setting, ['group' => 'stack_builder', 'is_public' => false])
            );
        }
    }
}
