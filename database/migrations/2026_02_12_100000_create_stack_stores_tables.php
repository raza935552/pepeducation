<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stack_stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('website_url', 2000)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('stack_store_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stack_store_id')->constrained('stack_stores')->cascadeOnDelete();
            $table->foreignId('stack_product_id')->constrained('stack_products')->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->string('url', 2000)->nullable();
            $table->foreignId('outbound_link_id')->nullable()->constrained('outbound_links')->nullOnDelete();
            $table->boolean('is_in_stock')->default(true);
            $table->timestamps();

            $table->unique(['stack_store_id', 'stack_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_store_product');
        Schema::dropIfExists('stack_stores');
    }
};
