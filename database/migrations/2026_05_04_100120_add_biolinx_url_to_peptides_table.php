<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('peptides', function (Blueprint $table) {
            $table->string('biolinx_url', 500)->nullable()->after('references');
        });
    }

    public function down(): void
    {
        Schema::table('peptides', function (Blueprint $table) {
            $table->dropColumn('biolinx_url');
        });
    }
};
