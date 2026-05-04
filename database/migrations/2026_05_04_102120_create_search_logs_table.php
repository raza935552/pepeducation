<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query')->index();
            $table->string('source', 30)->nullable()->index();
            $table->unsignedInteger('result_count')->default(0);
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_short', 60)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
