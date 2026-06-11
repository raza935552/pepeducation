<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Precise per-email ad attribution for the PP subscriber list.
 *
 * The first-touch utm_* + session columns already exist; this adds the two missing
 * signals so a captured email can be PROVABLY tied to an ad click:
 *   - first_fbclid : the Meta click id present in the session at capture time
 *   - is_ad        : true when the capture session carried a Meta ad signal
 *                    (fbclid, or a paid/Meta utm_source) — same rule as lander_visits.is_ad
 *
 * Both are nullable / default-false and back-fill nothing, so existing rows are
 * untouched; attribution is accurate from the first capture after deploy onward.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (! Schema::hasColumn('subscribers', 'first_fbclid')) {
                $table->string('first_fbclid', 512)->nullable()->after('first_utm_content');
            }
            if (! Schema::hasColumn('subscribers', 'is_ad')) {
                $table->boolean('is_ad')->default(false)->index()->after('first_fbclid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (Schema::hasColumn('subscribers', 'is_ad')) {
                $table->dropColumn('is_ad');
            }
            if (Schema::hasColumn('subscribers', 'first_fbclid')) {
                $table->dropColumn('first_fbclid');
            }
        });
    }
};
