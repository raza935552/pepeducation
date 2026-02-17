<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results_bank', function (Blueprint $table) {
            $table->id();
            $table->string('health_goal');          // e.g. "fat_loss", "muscle_growth"
            $table->string('experience_level');      // "beginner" or "advanced"
            $table->string('peptide_name');           // e.g. "Tirzepatide", "BPC-157"
            $table->string('peptide_slug')->nullable(); // URL-friendly slug
            $table->decimal('star_rating', 2, 1);    // e.g. 4.8
            $table->string('rating_label');           // e.g. "Excellent Match"
            $table->text('testimonial');              // User testimonial quote
            $table->string('testimonial_author')->nullable(); // "Sarah M., Age 34"
            $table->text('description')->nullable();  // Short peptide description
            $table->json('benefits')->nullable();     // ["benefit1", "benefit2"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['health_goal', 'experience_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results_bank');
    }
};
