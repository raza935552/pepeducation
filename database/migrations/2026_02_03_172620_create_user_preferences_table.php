<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Notification preferences
            $table->boolean('notify_edit_status')->default(true);
            $table->boolean('notify_marketing')->default(false);
            $table->boolean('notify_weekly_digest')->default(false);

            // Privacy preferences
            $table->boolean('data_usage_opt_in')->default(false);

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
