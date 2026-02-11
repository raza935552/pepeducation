<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add is_recommended to stack_stores (global recommendation flag)
        Schema::table('stack_stores', function (Blueprint $table) {
            $table->boolean('is_recommended')->default(false)->after('is_active');
        });

        // Add is_recommended to stack_store_product (per-product override, nullable three-state)
        Schema::table('stack_store_product', function (Blueprint $table) {
            $table->boolean('is_recommended')->nullable()->after('is_in_stock');
        });

        // Create stack_store_bundle pivot table
        Schema::create('stack_store_bundle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stack_store_id')->constrained('stack_stores')->cascadeOnDelete();
            $table->foreignId('stack_bundle_id')->constrained('stack_bundles')->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->string('url', 2000)->nullable();
            $table->foreignId('outbound_link_id')->nullable()->constrained('outbound_links')->nullOnDelete();
            $table->boolean('is_in_stock')->default(true);
            $table->boolean('is_recommended')->nullable();
            $table->timestamps();

            $table->unique(['stack_store_id', 'stack_bundle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_store_bundle');

        Schema::table('stack_store_product', function (Blueprint $table) {
            $table->dropColumn('is_recommended');
        });

        Schema::table('stack_stores', function (Blueprint $table) {
            $table->dropColumn('is_recommended');
        });
    }
};
