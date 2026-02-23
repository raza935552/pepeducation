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
        Schema::table('quiz_outcomes', function (Blueprint $table) {
            $table->unsignedInteger('shown_count')->default(0)->after('priority');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_outcomes', function (Blueprint $table) {
            $table->dropColumn('shown_count');
        });
    }
};
