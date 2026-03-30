<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stack_store_product', function (Blueprint $table) {
            $table->string('availability_status', 20)->default('in_stock')->after('is_in_stock');
        });

        // Migrate existing data: is_in_stock=true → 'in_stock', false → 'out_of_stock'
        DB::table('stack_store_product')->where('is_in_stock', true)->update(['availability_status' => 'in_stock']);
        DB::table('stack_store_product')->where('is_in_stock', false)->update(['availability_status' => 'out_of_stock']);
    }

    public function down(): void
    {
        Schema::table('stack_store_product', function (Blueprint $table) {
            $table->dropColumn('availability_status');
        });
    }
};
