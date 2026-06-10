<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Widen columns that store unpredictable-length external data (ad landing/referer
 * URLs carrying a ~200-char Meta fbclid) from VARCHAR(255) to TEXT, so a long fbclid
 * can never overflow and 500 a customer path (this 500'd /go and cost 112 ad
 * click-throughs on 2026-06-10). None of these columns are indexed.
 * NOTE: user_events.page_url is indexed (cannot be TEXT) — it is clamped in code instead.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $t) {
            $t->text('first_landing_page')->nullable()->change();
        });
        Schema::table('lead_magnet_downloads', function (Blueprint $t) {
            $t->text('source_page')->nullable()->change();
        });
        Schema::table('user_sessions', function (Blueprint $t) {
            $t->text('referrer')->nullable()->change();
            $t->text('entry_url')->nullable()->change();
        });
        Schema::table('outbound_clicks', function (Blueprint $t) {
            $t->text('source_page')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Intentionally no-op: narrowing back to VARCHAR could truncate stored data.
    }
};
