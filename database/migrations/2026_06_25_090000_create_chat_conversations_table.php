<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('visitor_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('open');   // open | closed
            $table->string('mode')->default('live');     // live | offline
            $table->boolean('human_requested')->default(false);
            $table->timestamp('human_requested_at')->nullable();
            $table->unsignedTinyInteger('rating')->nullable(); // 1 up, 0 down
            $table->timestamp('rated_at')->nullable();
            $table->timestamp('transcript_sent_at')->nullable();
            $table->foreignId('assigned_to')->nullable();
            $table->unsignedInteger('unread_for_admin')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('landing_page', 500)->nullable();
            $table->timestamps();

            $table->index(['status', 'last_message_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
