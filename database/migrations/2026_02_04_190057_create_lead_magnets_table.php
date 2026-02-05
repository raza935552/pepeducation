<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_magnets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // File
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->default('pdf'); // pdf, video, zip
            $table->unsignedBigInteger('file_size')->nullable(); // bytes

            // Display
            $table->string('thumbnail')->nullable();
            $table->string('preview_image')->nullable();

            // Targeting
            $table->enum('segment', ['tof', 'mof', 'bof', 'all'])->default('all');

            // Delivery Method
            $table->enum('delivery_method', ['instant', 'email'])->default('email');
            $table->string('download_button_text')->default('Download Now');

            // Landing Page Content (auto-generated page)
            $table->string('landing_headline')->nullable();
            $table->text('landing_description')->nullable();
            $table->json('landing_benefits')->nullable(); // Bullet points

            // Klaviyo Integration
            $table->string('klaviyo_flow_id')->nullable();
            $table->string('klaviyo_event')->default('Downloaded Lead Magnet');
            $table->string('klaviyo_property_name')->nullable(); // e.g., pp_lead_magnet

            // Stats
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('downloads_count')->default(0);

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Download tracking
        Schema::create('lead_magnet_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_magnet_id')->constrained()->onDelete('cascade');
            $table->string('session_id', 64)->index();
            $table->foreignId('subscriber_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // How they got here
            $table->string('source_page')->nullable();
            $table->string('source_popup')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_campaign')->nullable();

            // Delivery
            $table->enum('delivery_method', ['instant', 'email'])->default('email');
            $table->boolean('email_sent')->default(false);
            $table->boolean('downloaded')->default(false);
            $table->timestamp('downloaded_at')->nullable();

            // Sync
            $table->boolean('synced_to_klaviyo')->default(false);

            $table->timestamp('created_at')->useCurrent();

            $table->index(['lead_magnet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_magnet_downloads');
        Schema::dropIfExists('lead_magnets');
    }
};
