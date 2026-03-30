<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customerio_settings', function (Blueprint $table) {
            $table->id();
            $table->text('site_id')->nullable();
            $table->text('api_key')->nullable();
            $table->string('region', 10)->default('us');
            $table->boolean('is_enabled')->default(false);
            $table->boolean('track_quiz_started')->default(true);
            $table->boolean('track_quiz_completed')->default(true);
            $table->boolean('track_email_captured')->default(true);
            $table->boolean('track_quiz_abandoned')->default(true);
            $table->boolean('track_lead_magnet_download')->default(true);
            $table->boolean('track_outbound_click')->default(true);
            $table->boolean('track_stack_completed')->default(true);
            $table->boolean('track_subscribed')->default(true);
            $table->boolean('enable_page_tracking')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customerio_settings');
    }
};
