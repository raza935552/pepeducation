<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');

            // Question Content
            $table->text('question_text');
            $table->text('question_subtext')->nullable();
            $table->string('question_image')->nullable();

            // Question Type
            $table->enum('question_type', [
                'single',      // Single choice
                'multiple',    // Multiple choice (checkboxes)
                'scale',       // 1-5 or 1-10 scale
                'text',        // Free text input
                'email',       // Email capture
            ])->default('single');

            // Klaviyo Property Mapping
            $table->string('klaviyo_property')->nullable(); // e.g., 'pp_health_goal'

            // Answer Options (for single/multiple)
            $table->json('options')->nullable();
            /*
             * options: [
             *   {
             *     id: 'opt_1',
             *     text: 'Weight loss / Fat burning',
             *     subtext: 'Lose weight effectively',
             *     image: null,
             *     klaviyo_value: 'weight_loss',
             *     score_tof: 0,
             *     score_mof: 5,
             *     score_bof: 10,
             *     tags: ['weight', 'fat_loss'],
             *     skip_to_question: null, // For skip logic
             *   }
             * ]
             */

            // Scale Options (for scale type)
            $table->json('scale_config')->nullable();
            /*
             * scale_config: {
             *   min: 1,
             *   max: 10,
             *   min_label: 'Not at all',
             *   max_label: 'Extremely',
             *   klaviyo_value_map: { 1-3: 'low', 4-7: 'medium', 8-10: 'high' }
             * }
             */

            // Display Settings
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('allow_multiple')->default(false);
            $table->integer('max_selections')->nullable();

            // Conditional Logic
            $table->json('show_conditions')->nullable();
            /*
             * show_conditions: {
             *   type: 'and' | 'or',
             *   conditions: [
             *     { question_id: 1, answer_id: 'opt_1' }
             *   ]
             * }
             */

            $table->timestamps();

            // Index
            $table->index(['quiz_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
