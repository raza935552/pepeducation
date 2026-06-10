<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index session_id so the "Unique Visitors" DISTINCT COUNT in AdAnalyticsController
 * stays fast as lander_visits grows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lander_visits', function (Blueprint $table) {
            $table->index(['is_ad', 'session_id'], 'lv_isad_session_idx');
        });
    }

    public function down(): void
    {
        Schema::table('lander_visits', function (Blueprint $table) {
            $table->dropIndex('lv_isad_session_idx');
        });
    }
};
