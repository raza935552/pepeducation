<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            // Slide type: determines how this "question" renders
            // Default 'question' ensures all existing records keep working
            $table->string('slide_type', 50)->default('question')->after('quiz_id');

            // Content fields for non-question slides (intermission, loading, etc.)
            $table->text('content_title')->nullable()->after('question_image');
            $table->text('content_body')->nullable()->after('content_title');
            $table->string('content_source')->nullable()->after('content_body');

            // Auto-advance for loading screens
            $table->unsignedInteger('auto_advance_seconds')->nullable()->after('content_source');

            // CTA for reveal/bridge slides
            $table->string('cta_text')->nullable()->after('auto_advance_seconds');
            $table->string('cta_url')->nullable()->after('cta_text');

            // Dynamic content mapping for dynamic intermissions
            $table->string('dynamic_content_key')->nullable()->after('cta_url');
            $table->json('dynamic_content_map')->nullable()->after('dynamic_content_key');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn([
                'slide_type',
                'content_title',
                'content_body',
                'content_source',
                'auto_advance_seconds',
                'cta_text',
                'cta_url',
                'dynamic_content_key',
                'dynamic_content_map',
            ]);
        });
    }
};
