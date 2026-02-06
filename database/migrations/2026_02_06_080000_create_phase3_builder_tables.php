<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Page version history
        Schema::create('page_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('title');
            $table->json('content')->nullable();
            $table->longText('html')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at');

            $table->index(['page_id', 'version']);
        });

        // Form submissions from page builder forms
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
            $table->string('form_name', 100)->default('default');
            $table->json('data');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at');

            $table->index(['page_id', 'form_name']);
        });

        // Reusable saved sections
        Schema::create('saved_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('content');
            $table->string('category', 50)->default('custom');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // A/B variant support for pages
        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('variant_of')->nullable()->after('template')
                ->constrained('pages')->nullOnDelete();
            $table->unsignedTinyInteger('variant_weight')->default(50)->after('variant_of');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('variant_of');
            $table->dropColumn('variant_weight');
        });
        Schema::dropIfExists('saved_sections');
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('page_versions');
    }
};
