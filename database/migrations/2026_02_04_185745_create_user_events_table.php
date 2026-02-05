<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_events', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscriber_id')->nullable()->constrained()->nullOnDelete();

            // Event Info
            $table->string('event_type', 50)->index(); // page_view, click, scroll, quiz_answer, etc.
            $table->string('event_category', 50)->nullable(); // page, interaction, conversion, error
            $table->json('event_data')->nullable(); // All event-specific data

            // Page Context
            $table->string('page_url')->nullable();
            $table->string('page_title')->nullable();
            $table->string('page_type')->nullable(); // home, article, peptide, quiz, calculator

            // Element Context (for clicks/interactions)
            $table->string('element_type')->nullable(); // button, link, form, video
            $table->string('element_id')->nullable();
            $table->string('element_class')->nullable();
            $table->string('element_text')->nullable();
            $table->integer('element_x')->nullable();
            $table->integer('element_y')->nullable();

            // Sequence
            $table->integer('sequence')->default(0); // Order in session
            $table->integer('time_since_session_start')->nullable(); // Seconds
            $table->integer('time_since_last_event')->nullable(); // Seconds

            // Engagement
            $table->integer('scroll_depth')->nullable();
            $table->integer('time_on_page')->nullable();
            $table->integer('engagement_points')->default(0);

            // Sync Status
            $table->boolean('synced_to_klaviyo')->default(false);
            $table->timestamp('synced_at')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index(['event_type', 'created_at']);
            $table->index(['page_url', 'event_type']);
            $table->index(['synced_to_klaviyo', 'event_type']);

            // Foreign key
            $table->foreign('session_id')
                  ->references('session_id')
                  ->on('user_sessions')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_events');
    }
};
