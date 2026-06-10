<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mirror of Biolinx orders that were attributed to a PP lander / ad campaign.
 * Pushed from Biolinx (pp:push-conversions command) to a secret-verified PP
 * endpoint, keyed by biolinx_order_id (idempotent upsert). Lets the Ad Analytics
 * dashboard show ORDERS + REVENUE + conversion rate per lander / campaign / ad —
 * closing the loop from ad visit → click → sale. Revenue data: keep long-term
 * (not in the tracking-cleanup retention sweep).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lander_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('biolinx_order_id', 64)->unique();
            $table->string('pp_lander', 120)->nullable()->index();
            $table->string('utm_source', 200)->nullable();
            $table->string('utm_medium', 200)->nullable();
            $table->string('utm_campaign', 200)->nullable()->index();
            $table->string('utm_content', 200)->nullable();
            $table->string('utm_term', 200)->nullable();
            $table->string('fbclid', 400)->nullable();
            $table->decimal('revenue', 12, 2)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->string('order_type', 20)->nullable();   // initial | upsell | downsell
            $table->string('status', 30)->nullable();
            $table->timestamp('ordered_at')->nullable()->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lander_conversions');
    }
};
