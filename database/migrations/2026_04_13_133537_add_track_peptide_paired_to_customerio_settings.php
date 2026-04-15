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
        Schema::table('customerio_settings', function (Blueprint $table) {
            $table->boolean('track_peptide_paired')->default(true)->after('track_subscribed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customerio_settings', function (Blueprint $table) {
            $table->dropColumn('track_peptide_paired');
        });
    }
};
