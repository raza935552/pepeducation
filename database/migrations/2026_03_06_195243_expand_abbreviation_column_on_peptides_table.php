<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peptides', function (Blueprint $table) {
            $table->string('abbreviation', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('peptides', function (Blueprint $table) {
            $table->string('abbreviation', 20)->nullable()->change();
        });
    }
};
