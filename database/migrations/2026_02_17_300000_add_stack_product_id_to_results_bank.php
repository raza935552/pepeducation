<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results_bank', function (Blueprint $table) {
            $table->foreignId('stack_product_id')
                ->nullable()
                ->after('peptide_slug')
                ->constrained('stack_products')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('results_bank', function (Blueprint $table) {
            $table->dropConstrainedForeignId('stack_product_id');
        });
    }
};
