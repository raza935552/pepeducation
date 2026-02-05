<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();

            // Content
            $table->string('headline');
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('button_text')->default('Subscribe');
            $table->string('success_message')->default('Thanks for subscribing!');
            $table->string('success_redirect_url')->nullable();

            // Form Fields Configuration
            $table->json('form_fields')->nullable();
            /*
             * form_fields: [
             *   { name: 'email', type: 'email', required: true, placeholder: 'Enter your email' },
             *   { name: 'phone', type: 'tel', required: false, placeholder: 'Phone (optional)' },
             *   { name: 'name', type: 'text', required: false, placeholder: 'Your name' }
             * ]
             */

            // Trigger Configuration
            $table->json('triggers')->nullable();
            /*
             * triggers: {
             *   time_delay: 15, // seconds, null = disabled
             *   scroll_depth: 50, // percentage, null = disabled
             *   exit_intent: true,
             *   click_selector: null, // CSS selector for click trigger
             *   page_views: null // Show after X page views
             * }
             */

            // Targeting Configuration
            $table->json('targeting')->nullable();
            /*
             * targeting: {
             *   segments: ['tof', 'mof'], // empty = all
             *   pages: [], // empty = all pages
             *   exclude_pages: ['/checkout', '/thank-you'],
             *   devices: ['mobile', 'desktop', 'tablet'], // empty = all
             *   new_visitors_only: false,
             *   returning_visitors_only: false
             * }
             */

            // Display Rules
            $table->json('display_rules')->nullable();
            /*
             * display_rules: {
             *   show_once_per_hours: 24,
             *   max_shows_total: 3,
             *   hide_if_subscribed: true,
             *   hide_if_dismissed: true,
             *   priority: 10 // Higher = show first
             * }
             */

            // Design Configuration
            $table->json('design')->nullable();
            /*
             * design: {
             *   position: 'center', // center, bottom-right, bottom-left, top, bottom
             *   size: 'medium', // small, medium, large, fullscreen
             *   animation: 'fade', // fade, slide, bounce
             *   overlay: true,
             *   overlay_close: true,
             *   colors: {
             *     background: '#ffffff',
             *     text: '#1f2937',
             *     button: '#9A7B4F',
             *     button_text: '#ffffff'
             *   },
             *   border_radius: 16,
             *   show_close_button: true
             * }
             */

            // Klaviyo Integration
            $table->string('klaviyo_list_id')->nullable();
            $table->string('klaviyo_event')->default('Popup Subscribed');

            // Lead Magnet (optional)
            $table->foreignId('lead_magnet_id')->nullable()->constrained()->nullOnDelete();

            // Stats
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('conversions_count')->default(0);
            $table->unsignedBigInteger('dismissals_count')->default(0);

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->timestamps();
        });

        // Popup interactions tracking
        Schema::create('popup_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('popup_id')->constrained()->onDelete('cascade');
            $table->string('session_id', 64)->index();
            $table->foreignId('subscriber_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('interaction_type', ['view', 'dismiss', 'convert']);
            $table->string('dismiss_method')->nullable(); // close_button, backdrop, escape
            $table->integer('time_to_interaction')->nullable(); // Seconds until interaction
            $table->json('form_data')->nullable(); // Captured form data

            $table->timestamp('created_at')->useCurrent();

            $table->index(['popup_id', 'interaction_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popup_interactions');
        Schema::dropIfExists('popups');
    }
};
