<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            [
                'group' => 'general',
                'key' => 'maintenance_enabled',
                'value' => '0',
                'type' => 'bool',
                'description' => 'Enable maintenance mode',
            ],
            [
                'group' => 'general',
                'key' => 'maintenance_password',
                'value' => '',
                'type' => 'string',
                'description' => 'QA bypass password',
            ],
            [
                'group' => 'general',
                'key' => 'maintenance_message',
                'value' => 'We are getting things ready. Check back soon!',
                'type' => 'string',
                'description' => 'Maintenance page message',
            ],
        ];

        foreach ($settings as $setting) {
            // Update type if row already exists (from manual seeding), or insert new
            $existing = DB::table('settings')
                ->where('group', $setting['group'])
                ->where('key', $setting['key'])
                ->first();

            if ($existing) {
                DB::table('settings')
                    ->where('id', $existing->id)
                    ->update(['type' => $setting['type'], 'description' => $setting['description']]);
            } else {
                DB::table('settings')->insert(array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'general')
            ->whereIn('key', ['maintenance_enabled', 'maintenance_password', 'maintenance_message'])
            ->delete();
    }
};
