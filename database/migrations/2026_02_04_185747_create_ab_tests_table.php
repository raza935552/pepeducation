<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Test Configuration
            $table->string('test_type')->default('split'); // split, multivariate
            $table->json('variants'); // [{name: "A", weight: 50}, {name: "B", weight: 50}]
            $table->string('target_element')->nullable(); // CSS selector or page
            $table->json('target_pages')->nullable(); // Which pages this test runs on

            // Traffic Allocation
            $table->integer('traffic_percentage')->default(100); // % of traffic included

            // Goals
            $table->string('primary_goal')->nullable(); // conversion, click, scroll
            $table->json('goal_config')->nullable(); // Goal-specific settings

            // Results
            $table->json('results')->nullable(); // Cached results
            $table->string('winner')->nullable(); // Winning variant

            // Status
            $table->enum('status', ['draft', 'running', 'paused', 'completed'])->default('draft');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ab_tests');
    }
};
