<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results_bank', function (Blueprint $table) {
            $table->json('display_fields')->nullable()->after('is_active');
        });

        // Make rating fields nullable (they were required before)
        Schema::table('results_bank', function (Blueprint $table) {
            $table->decimal('star_rating', 2, 1)->nullable()->change();
            $table->string('rating_label')->nullable()->change();
            $table->text('testimonial')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('results_bank', function (Blueprint $table) {
            $table->dropColumn('display_fields');
            $table->decimal('star_rating', 2, 1)->nullable(false)->change();
            $table->string('rating_label')->nullable(false)->change();
            $table->text('testimonial')->nullable(false)->change();
        });
    }
};
