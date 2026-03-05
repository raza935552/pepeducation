<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stack_store_peptide_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stack_store_id')->constrained('stack_stores')->cascadeOnDelete();
            $table->string('peptide_name');
            $table->string('url', 2000);
            $table->foreignId('outbound_link_id')->nullable()->constrained('outbound_links')->nullOnDelete();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_in_stock')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stack_store_peptide_links');
    }
};
