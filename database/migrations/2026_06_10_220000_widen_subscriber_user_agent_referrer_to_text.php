<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Follow-up to 2026_06_10_200000: two more unbounded external-data columns on
 * subscribers were left at VARCHAR(255) and still threw "Data too long" on
 * subscribe — user_agent (long/bot UAs; written raw in SubscriberService and
 * over-truncated to 500 at two other write sites) and first_referrer (referer
 * URLs carrying a ~200-char Meta fbclid). Widen to TEXT. Neither is indexed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $t) {
            $t->text('user_agent')->nullable()->change();
            $t->text('first_referrer')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Intentionally no-op: narrowing back to VARCHAR could truncate stored data.
    }
};
