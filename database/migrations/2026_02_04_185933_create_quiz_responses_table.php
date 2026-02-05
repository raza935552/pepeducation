<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->string('session_id', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscriber_id')->nullable()->constrained()->nullOnDelete();

            // Response Data
            $table->json('answers'); // All answers with question IDs
            /*
             * answers: {
             *   'q_1': { answer_id: 'opt_2', klaviyo_value: 'heard_not_tried' },
             *   'q_2': { answer_id: 'opt_1', klaviyo_value: 'weight_loss' },
             *   'q_3': { answer_ids: ['opt_1', 'opt_3'], klaviyo_value: ['gut', 'joint'] }
             * }
             */

            // Calculated Scores
            $table->integer('score_tof')->default(0);
            $table->integer('score_mof')->default(0);
            $table->integer('score_bof')->default(0);
            $table->integer('total_score')->default(0);

            // Determined Segment
            $table->enum('segment', ['tof', 'mof', 'bof'])->nullable();

            // Outcome
            $table->foreignId('outcome_id')->nullable()->constrained('quiz_outcomes')->nullOnDelete();
            $table->string('outcome_name')->nullable();

            // Product Recommendation (if product quiz)
            $table->foreignId('recommended_peptide_id')->nullable()->constrained('peptides')->nullOnDelete();

            // All Klaviyo Properties Generated
            $table->json('klaviyo_properties')->nullable();
            /*
             * klaviyo_properties: {
             *   pp_segment: 'mof',
             *   pp_health_goal: 'weight_loss',
             *   pp_experience_level: 'heard_not_tried',
             *   pp_timeline: 'within_30_days',
             *   pp_concerns: ['gut', 'joint'],
             *   pp_recommended_peptide: 'semaglutide'
             * }
             */

            // Captured Email (if quiz captures email)
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Timing
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('questions_answered')->default(0);

            // Status
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->boolean('synced_to_klaviyo')->default(false);

            // Source
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['quiz_id', 'status']);
            $table->index(['segment', 'created_at']);
            $table->index(['synced_to_klaviyo', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_responses');
    }
};
