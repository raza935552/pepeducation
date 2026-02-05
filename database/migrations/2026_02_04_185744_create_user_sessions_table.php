<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->unique()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscriber_id')->nullable()->constrained()->nullOnDelete();

            // Timing
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable();

            // Entry/Exit
            $table->string('entry_url')->nullable();
            $table->string('exit_url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('referrer_domain')->nullable();

            // UTM Parameters
            $table->string('utm_source')->nullable()->index();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable()->index();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();

            // Device Info
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('os')->nullable();
            $table->string('os_version')->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->boolean('is_bot')->default(false);

            // Location
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('country_name')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Engagement Metrics
            $table->integer('pages_viewed')->default(0);
            $table->integer('events_count')->default(0);
            $table->integer('avg_scroll_depth')->nullable();
            $table->integer('engagement_score')->default(0);

            // Segmentation
            $table->enum('segment', ['tof', 'mof', 'bof'])->nullable();
            $table->boolean('is_returning')->default(false);
            $table->integer('session_number')->default(1);

            // Conversion Tracking
            $table->boolean('converted')->default(false);
            $table->string('conversion_type')->nullable(); // quiz, signup, download, purchase
            $table->timestamp('converted_at')->nullable();

            // Status
            $table->boolean('is_bounced')->default(false);
            $table->boolean('synced_to_klaviyo')->default(false);

            $table->timestamps();

            // Indexes
            $table->index(['started_at']);
            $table->index(['segment', 'started_at']);
            $table->index(['converted', 'conversion_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
