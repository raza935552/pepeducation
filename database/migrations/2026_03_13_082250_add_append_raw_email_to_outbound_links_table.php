<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('outbound_links', function (Blueprint $table) {
            $table->boolean('append_raw_email')->default(false)->after('append_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outbound_links', function (Blueprint $table) {
            $table->dropColumn('append_raw_email');
        });
    }
};
