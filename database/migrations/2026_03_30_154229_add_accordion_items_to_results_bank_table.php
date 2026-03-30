<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results_bank', function (Blueprint $table) {
            $table->json('accordion_items')->nullable()->after('display_fields');
        });
    }

    public function down(): void
    {
        Schema::table('results_bank', function (Blueprint $table) {
            $table->dropColumn('accordion_items');
        });
    }
};
