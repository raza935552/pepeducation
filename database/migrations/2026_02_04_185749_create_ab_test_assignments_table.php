<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ab_test_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ab_test_id')->constrained()->onDelete('cascade');
            $table->string('session_id', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscriber_id')->nullable()->constrained()->nullOnDelete();

            // Assignment
            $table->string('variant'); // A, B, C, etc.

            // Conversion Tracking
            $table->boolean('converted')->default(false);
            $table->string('conversion_type')->nullable();
            $table->json('conversion_data')->nullable();
            $table->timestamp('converted_at')->nullable();

            // Engagement
            $table->integer('page_views')->default(0);
            $table->integer('time_on_page')->nullable();
            $table->integer('scroll_depth')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Unique constraint - one assignment per test per session
            $table->unique(['ab_test_id', 'session_id']);

            // Index for analysis
            $table->index(['ab_test_id', 'variant', 'converted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ab_test_assignments');
    }
};
