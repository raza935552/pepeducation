<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            'group' => 'tracking',
            'key' => 'cookie_consent_enabled',
            'value' => '0',
            'type' => 'bool',
            'description' => 'Show cookie consent banner',
            'is_public' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'tracking')
            ->where('key', 'cookie_consent_enabled')
            ->delete();
    }
};
