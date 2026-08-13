<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dev_requests', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('telegram');
            $table->string('external_id')->unique();
            $table->string('chat_id')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_username')->nullable();
            $table->text('message');
            $table->string('risk')->default('unknown');
            $table->string('status')->default('pending');
            $table->text('result')->nullable();
            $table->string('commit_sha')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dev_requests');
    }
};
