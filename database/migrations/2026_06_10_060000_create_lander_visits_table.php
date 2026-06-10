<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Records a row per lander page-load so the admin ad dashboard can report
 * accurate VISITS (the Meta pixel only reports to Meta; the landers run no
 * PP analytics JS). `is_ad` flags loads that carry a Meta click id / ad UTM,
 * so the dashboard can separate paid ad traffic from organic/direct.
 *
 * Written after the response is sent (never blocks the lander render).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lander_visits', function (Blueprint $table) {
            $table->id();
            $table->string('lander_slug', 120)->index();
            $table->string('session_id', 100)->nullable();   // laravel session (join to outbound_clicks)
            $table->boolean('is_ad')->default(false)->index(); // fbclid OR ad utm present
            $table->string('fbclid', 400)->nullable();
            $table->string('utm_source', 200)->nullable();
            $table->string('utm_medium', 200)->nullable();
            $table->string('utm_campaign', 200)->nullable()->index();
            $table->string('utm_content', 200)->nullable();
            $table->string('utm_term', 200)->nullable();
            $table->string('referer', 500)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['lander_slug', 'created_at']);
            $table->index(['is_ad', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lander_visits');
    }
};
