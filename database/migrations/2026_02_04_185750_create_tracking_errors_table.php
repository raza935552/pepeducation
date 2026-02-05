<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_errors', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Error Type
            $table->string('error_type'); // js_error, 404, form_error, api_error
            $table->string('error_code')->nullable();
            $table->text('error_message');
            $table->text('stack_trace')->nullable();

            // Context
            $table->string('page_url');
            $table->string('page_title')->nullable();
            $table->string('component')->nullable(); // Which component/feature

            // Browser Info
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('device_type')->nullable();

            // User Action Context
            $table->string('user_action')->nullable(); // What they were trying to do
            $table->json('form_data')->nullable(); // Sanitized form data if applicable

            // Frequency
            $table->integer('occurrence_count')->default(1);
            $table->timestamp('first_occurred_at');
            $table->timestamp('last_occurred_at');

            // Status
            $table->enum('status', ['new', 'acknowledged', 'resolved', 'ignored'])->default('new');
            $table->text('resolution_notes')->nullable();

            $table->timestamps();

            // Index for grouping similar errors (without TEXT columns)
            $table->index(['error_type', 'page_url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_errors');
    }
};
