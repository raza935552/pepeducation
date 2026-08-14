<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Conversation memory + attachments for the PP Telegram dev pipeline (mirror of
 * Biolinx). dev_messages = rolling transcript of every group message and uploaded
 * file; dev_requests gains last_bot_message_id (reply-threading) + attachment_path.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dev_messages', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->index();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('dev_request_id')->nullable()->index();
            $table->string('from_name')->nullable();
            $table->string('from_username')->nullable();
            $table->boolean('is_bot')->default(false);
            $table->unsignedBigInteger('reply_to_message_id')->nullable();
            $table->longText('text')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_local_path')->nullable();
            $table->string('file_mime')->nullable();
            $table->timestamps();
            $table->index(['chat_id', 'id']);
        });

        Schema::table('dev_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('last_bot_message_id')->nullable()->after('commit_sha');
            $table->string('attachment_path')->nullable()->after('last_bot_message_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dev_messages');
        Schema::table('dev_requests', function (Blueprint $table) {
            $table->dropColumn(['last_bot_message_id', 'attachment_path']);
        });
    }
};
