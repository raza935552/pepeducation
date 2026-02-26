<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix question_type from restrictive enum to varchar.
     * The original enum('single','multiple','scale','text','email') didn't match
     * the values used in forms ('single_choice','multiple_choice').
     */
    public function up(): void
    {
        // Change enum to varchar to remove constraint (cross-DB compatible)
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->string('question_type', 50)->default('single_choice')->change();
        });

        // Normalize existing values to match the form convention
        DB::table('quiz_questions')->where('question_type', 'single')->update(['question_type' => 'single_choice']);
        DB::table('quiz_questions')->where('question_type', 'multiple')->update(['question_type' => 'multiple_choice']);
    }

    public function down(): void
    {
        DB::table('quiz_questions')->where('question_type', 'single_choice')->update(['question_type' => 'single']);
        DB::table('quiz_questions')->where('question_type', 'multiple_choice')->update(['question_type' => 'multiple']);

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->string('question_type', 50)->default('single')->change();
        });
    }
};
