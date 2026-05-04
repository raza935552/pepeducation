<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('buy_clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('peptide_id')->nullable()->index();
            $table->string('context', 40)->nullable()->index();
            $table->string('destination', 60)->nullable();
            $table->string('source_url', 500)->nullable();
            $table->string('target_url', 500)->nullable();
            $table->boolean('has_product')->default(false);
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_short', 60)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buy_clicks');
    }
};
