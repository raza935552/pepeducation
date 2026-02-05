<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peptides', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('full_name')->nullable();
            $table->string('abbreviation', 20)->nullable();
            $table->string('type')->nullable();

            // Quick Stats
            $table->string('typical_dose')->nullable();
            $table->string('dose_frequency')->nullable();
            $table->string('route')->nullable();
            $table->json('injection_sites')->nullable();
            $table->string('cycle')->nullable();
            $table->string('storage')->nullable();

            // Research Status
            $table->enum('research_status', ['extensive', 'well', 'emerging', 'limited'])->default('limited');
            $table->boolean('is_published')->default(false);

            // Content (text fields)
            $table->text('overview')->nullable();
            $table->json('key_benefits')->nullable();
            $table->text('mechanism_of_action')->nullable();
            $table->json('what_to_expect')->nullable();
            $table->json('safety_warnings')->nullable();

            // Molecular Info
            $table->decimal('molecular_weight', 10, 2)->nullable();
            $table->integer('amino_acid_length')->nullable();
            $table->text('amino_acid_sequence')->nullable();
            $table->text('molecular_notes')->nullable();

            // Pharmacokinetics
            $table->string('peak_time')->nullable();
            $table->string('half_life')->nullable();
            $table->string('clearance_time')->nullable();

            // Complex data as JSON
            $table->json('protocols')->nullable();
            $table->json('compatible_peptides')->nullable();
            $table->json('reconstitution_steps')->nullable();
            $table->json('quality_indicators')->nullable();
            $table->json('effectiveness_ratings')->nullable();
            $table->json('references')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
        });

        // Pivot table for peptide-category relationship
        Schema::create('category_peptide', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('peptide_id')->constrained()->onDelete('cascade');
            $table->unique(['category_id', 'peptide_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_peptide');
        Schema::dropIfExists('peptides');
    }
};
