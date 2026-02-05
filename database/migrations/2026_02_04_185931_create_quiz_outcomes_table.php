<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');

            // Outcome Name
            $table->string('name'); // e.g., "TOF Segment", "BPC-157 Recommendation"

            // Condition to trigger this outcome
            $table->json('conditions');
            /*
             * conditions: {
             *   type: 'segment' | 'score' | 'answer',
             *   segment: 'tof', // If type = segment
             *   min_score: 20,  // If type = score
             *   score_type: 'tof', // Which score to check
             *   answer_conditions: [ // If type = answer
             *     { question_id: 1, answer_id: 'opt_1' }
             *   ]
             * }
             */

            // What happens when this outcome triggers
            $table->string('redirect_url')->nullable();
            $table->string('redirect_type')->default('internal'); // internal, external, product

            // Display to user
            $table->string('result_title')->nullable();
            $table->text('result_message')->nullable();
            $table->string('result_image')->nullable();

            // Product Recommendation (for product quiz)
            $table->foreignId('recommended_peptide_id')->nullable()->constrained('peptides')->nullOnDelete();
            $table->string('product_link')->nullable(); // Fast Peptix link

            // Klaviyo Actions
            $table->string('klaviyo_event')->nullable(); // Custom event name
            $table->string('klaviyo_list_id')->nullable(); // Add to specific list
            $table->json('klaviyo_properties')->nullable(); // Additional properties to set

            // Priority (first matching outcome wins)
            $table->integer('priority')->default(0);

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index
            $table->index(['quiz_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_outcomes');
    }
};
