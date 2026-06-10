<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * First-touch log: one row per visitor session capturing the EXACT link they
 * arrived on (full landing URL incl. query string) + referrer + ad source.
 * Written server-side after the response by LogVisitorEntry middleware, so it
 * covers EVERY visitor — including lander/ad traffic that runs no analytics JS
 * (those never reach the user_sessions table). For reference/lookup later.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_entries', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->nullable()->index();
            $table->text('landing_url');                 // full URL incl. query — "their link"
            $table->string('path', 255)->nullable()->index();
            $table->text('referrer')->nullable();        // where they came FROM
            $table->string('referrer_domain', 255)->nullable()->index();
            $table->boolean('is_ad')->default(false)->index();
            $table->string('utm_source', 200)->nullable();
            $table->string('utm_medium', 200)->nullable();
            $table->string('utm_campaign', 200)->nullable()->index();
            $table->string('utm_content', 200)->nullable();
            $table->string('utm_term', 200)->nullable();
            $table->string('fbclid', 400)->nullable();
            $table->string('device', 20)->nullable();    // mobile | desktop | bot
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_entries');
    }
};
