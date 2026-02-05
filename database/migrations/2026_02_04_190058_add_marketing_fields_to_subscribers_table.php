<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            // Contact Info
            $table->string('phone')->nullable()->after('email');

            // Segmentation
            $table->enum('segment', ['tof', 'mof', 'bof'])->nullable()->after('status');
            $table->boolean('quiz_completed')->default(false);
            $table->timestamp('quiz_completed_at')->nullable();

            // Klaviyo Sync
            $table->string('klaviyo_id')->nullable()->index();
            $table->timestamp('klaviyo_synced_at')->nullable();
            $table->json('klaviyo_properties')->nullable(); // Cached properties

            // First Touch Attribution
            $table->string('first_session_id', 64)->nullable();
            $table->string('first_utm_source')->nullable();
            $table->string('first_utm_medium')->nullable();
            $table->string('first_utm_campaign')->nullable();
            $table->string('first_utm_content')->nullable();
            $table->string('first_referrer')->nullable();
            $table->string('first_landing_page')->nullable();

            // Engagement
            $table->integer('total_sessions')->default(0);
            $table->integer('total_page_views')->default(0);
            $table->integer('engagement_score')->default(0);
            $table->enum('engagement_tier', ['hot', 'warm', 'cold'])->default('cold');
            $table->timestamp('last_activity_at')->nullable();

            // Lead Magnets
            $table->json('lead_magnets_downloaded')->nullable();

            // Product Interest
            $table->json('peptides_viewed')->nullable();
            $table->string('primary_interest')->nullable(); // Most viewed category
            $table->boolean('clicked_to_shop')->default(false);
            $table->integer('shop_clicks')->default(0);
            $table->timestamp('first_shop_click_at')->nullable();

            // Conversion Status
            $table->boolean('is_customer')->default(false);
            $table->timestamp('first_purchase_at')->nullable();
            $table->decimal('lifetime_value', 10, 2)->default(0);

            // Device/Location (from first visit)
            $table->string('device_type')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'segment',
                'quiz_completed',
                'quiz_completed_at',
                'klaviyo_id',
                'klaviyo_synced_at',
                'klaviyo_properties',
                'first_session_id',
                'first_utm_source',
                'first_utm_medium',
                'first_utm_campaign',
                'first_utm_content',
                'first_referrer',
                'first_landing_page',
                'total_sessions',
                'total_page_views',
                'engagement_score',
                'engagement_tier',
                'last_activity_at',
                'lead_magnets_downloaded',
                'peptides_viewed',
                'primary_interest',
                'clicked_to_shop',
                'shop_clicks',
                'first_shop_click_at',
                'is_customer',
                'first_purchase_at',
                'lifetime_value',
                'device_type',
                'country',
                'region',
                'city',
            ]);
        });
    }
};
