<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();

            // Source
            $table->string('source'); // klaviyo, fastpeptix, stripe
            $table->string('event_type'); // email.opened, order.completed, etc.
            $table->string('webhook_id')->nullable(); // External webhook ID

            // Matching
            $table->string('email')->nullable()->index();
            $table->foreignId('subscriber_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 64)->nullable();

            // Payload
            $table->json('payload'); // Full webhook payload
            $table->json('extracted_data')->nullable(); // Parsed useful data

            // Processing
            $table->enum('status', ['received', 'processed', 'failed', 'ignored'])->default('received');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();

            // For Klaviyo email events
            $table->string('campaign_id')->nullable();
            $table->string('flow_id')->nullable();
            $table->string('message_id')->nullable();

            // For FastPeptix events
            $table->string('order_id')->nullable();
            $table->decimal('order_value', 10, 2)->nullable();
            $table->json('products')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['source', 'event_type', 'created_at']);
            $table->index(['status', 'source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
