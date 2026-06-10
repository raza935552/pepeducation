<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A/B test plumbing: tag every lander visit and every outbound click with the
 * assigned variant ('A' = control / user-built, 'B' = AI-built), so the admin
 * A/B scoreboard can compute visits + CTR-to-Biolinx per variant per lander.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lander_visits', function (Blueprint $table) {
            $table->string('variant', 1)->nullable()->index()->after('lander_slug');
        });
        Schema::table('outbound_clicks', function (Blueprint $table) {
            $table->string('variant', 1)->nullable()->index()->after('session_id');
        });
    }

    public function down(): void
    {
        Schema::table('lander_visits', function (Blueprint $table) {
            $table->dropColumn('variant');
        });
        Schema::table('outbound_clicks', function (Blueprint $table) {
            $table->dropColumn('variant');
        });
    }
};
