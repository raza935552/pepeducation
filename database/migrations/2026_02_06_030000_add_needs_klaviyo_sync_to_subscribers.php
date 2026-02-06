<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->boolean('needs_klaviyo_sync')->default(true)->after('klaviyo_synced_at');
            $table->index('needs_klaviyo_sync');
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropIndex(['needs_klaviyo_sync']);
            $table->dropColumn('needs_klaviyo_sync');
        });
    }
};
