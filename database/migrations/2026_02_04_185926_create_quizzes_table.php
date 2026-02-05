<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['segmentation', 'product', 'custom'])->default('custom');

            // Display Settings
            $table->string('title')->nullable(); // Displayed title
            $table->text('intro_text')->nullable();
            $table->string('intro_image')->nullable();
            $table->string('completion_title')->nullable();
            $table->text('completion_text')->nullable();

            // Behavior Settings
            $table->json('settings')->nullable();
            /*
             * settings: {
             *   require_email: true,
             *   email_step: 'before_results' | 'after_results',
             *   show_progress_bar: true,
             *   allow_back: true,
             *   shuffle_questions: false,
             *   shuffle_answers: false,
             *   auto_advance: true,
             *   show_question_numbers: true
             * }
             */

            // Design Settings
            $table->json('design')->nullable();
            /*
             * design: {
             *   primary_color: '#9A7B4F',
             *   background_color: '#FDFCFA',
             *   button_style: 'rounded',
             *   animation: 'fade' | 'slide'
             * }
             */

            // Klaviyo Integration
            $table->string('klaviyo_list_id')->nullable();
            $table->string('klaviyo_start_event')->default('Started Quiz');
            $table->string('klaviyo_complete_event')->default('Completed Quiz');

            // Stats
            $table->unsignedBigInteger('starts_count')->default(0);
            $table->unsignedBigInteger('completions_count')->default(0);

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
