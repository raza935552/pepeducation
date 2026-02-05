<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First create outbound_links table
        Schema::create('outbound_links', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('destination_url');
            $table->foreignId('peptide_id')->nullable()->constrained()->nullOnDelete();

            // UTM Configuration
            $table->string('utm_source')->default('professorpeptides');
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();

            // Options
            $table->boolean('append_segment')->default(true);
            $table->boolean('append_session')->default(true);
            $table->boolean('append_email')->default(true);
            $table->boolean('append_quiz_data')->default(true);
            $table->boolean('track_klaviyo')->default(true);

            // Stats
            $table->unsignedBigInteger('click_count')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        // Then create outbound_clicks table
        Schema::create('outbound_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outbound_link_id')->constrained()->onDelete('cascade');
            $table->string('session_id', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscriber_id')->nullable()->constrained()->nullOnDelete();

            // Full URL sent to Fast Peptix
            $table->text('final_url');

            // All data passed in query params (for our records)
            $table->json('passed_data'); // Everything we sent

            // Attribution Data Passed
            $table->string('pp_session')->nullable(); // Our session ID
            $table->string('pp_email_hash')->nullable(); // Hashed email for matching
            $table->enum('pp_segment', ['tof', 'mof', 'bof'])->nullable();
            $table->integer('pp_engagement_score')->nullable();

            // Quiz Data Passed
            $table->string('pp_health_goal')->nullable();
            $table->string('pp_experience')->nullable();
            $table->string('pp_timeline')->nullable();
            $table->string('pp_recommended_peptide')->nullable();

            // UTM Passed
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();

            // Source Context
            $table->string('source_page')->nullable();
            $table->string('source_section')->nullable(); // hero, sidebar, inline, quiz_result
            $table->string('click_element')->nullable(); // button text or element

            // Sync
            $table->boolean('synced_to_klaviyo')->default(false);

            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index(['created_at']);
            $table->index(['pp_segment', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_clicks');
        Schema::dropIfExists('outbound_links');
    }
};
