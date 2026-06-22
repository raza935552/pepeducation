<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peptides', function (Blueprint $table) {
            // Higher = more popular / best-selling → shown first. 0 = long tail
            // (falls back to alphabetical). Admin-controllable.
            $table->integer('popularity')->default(0)->index()->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('peptides', function (Blueprint $table) {
            $table->dropColumn('popularity');
        });
    }
};
