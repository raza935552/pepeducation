<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Stack Goals
        Schema::create('stack_goals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('icon')->nullable();
            $table->string('image')->nullable();
            $table->string('color')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Stack Products
        Schema::create('stack_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('dosing_info')->nullable();
            $table->json('key_benefits')->nullable();
            $table->string('external_url')->nullable();
            $table->foreignId('outbound_link_id')->nullable()->constrained('outbound_links')->nullOnDelete();
            $table->foreignId('related_peptide_id')->nullable()->constrained('peptides')->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 3. Goal-Product Pivot
        Schema::create('goal_stack_product', function (Blueprint $table) {
            $table->foreignId('stack_goal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stack_product_id')->constrained()->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->unique(['stack_goal_id', 'stack_product_id']);
        });

        // 4. Stack Bundles
        Schema::create('stack_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('stack_goal_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('bundle_price', 10, 2);
            $table->string('external_url')->nullable();
            $table->foreignId('outbound_link_id')->nullable()->constrained('outbound_links')->nullOnDelete();
            $table->boolean('is_professor_pick')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 5. Stack Bundle Items
        Schema::create('stack_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stack_bundle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stack_product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->integer('order')->default(0);
            $table->unique(['stack_bundle_id', 'stack_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_bundle_items');
        Schema::dropIfExists('stack_bundles');
        Schema::dropIfExists('goal_stack_product');
        Schema::dropIfExists('stack_products');
        Schema::dropIfExists('stack_goals');
    }
};
