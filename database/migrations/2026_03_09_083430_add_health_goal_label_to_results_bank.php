<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results_bank', function (Blueprint $table) {
            $table->string('health_goal_label')->nullable()->after('health_goal');
        });
    }

    public function down(): void
    {
        Schema::table('results_bank', function (Blueprint $table) {
            $table->dropColumn('health_goal_label');
        });
    }
};
