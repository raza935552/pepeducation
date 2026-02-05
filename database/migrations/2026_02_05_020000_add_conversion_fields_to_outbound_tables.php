<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add conversion fields to outbound_clicks
        Schema::table('outbound_clicks', function (Blueprint $table) {
            $table->boolean('converted')->default(false)->after('synced_to_klaviyo');
            $table->timestamp('converted_at')->nullable()->after('converted');
            $table->decimal('conversion_value', 10, 2)->nullable()->after('converted_at');
            $table->string('order_id')->nullable()->after('conversion_value');

            // Add index for conversion queries
            $table->index(['converted', 'created_at']);
            $table->index('pp_session');
        });

        // Add conversions_count to outbound_links
        Schema::table('outbound_links', function (Blueprint $table) {
            $table->unsignedBigInteger('conversions_count')->default(0)->after('click_count');
        });
    }

    public function down(): void
    {
        Schema::table('outbound_clicks', function (Blueprint $table) {
            $table->dropIndex(['converted', 'created_at']);
            $table->dropIndex(['pp_session']);
            $table->dropColumn(['converted', 'converted_at', 'conversion_value', 'order_id']);
        });

        Schema::table('outbound_links', function (Blueprint $table) {
            $table->dropColumn('conversions_count');
        });
    }
};
