<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outbound_clicks', function (Blueprint $table) {
            $table->renameColumn('pp_experience', 'pp_experience_level');
            $table->boolean('pp_quiz_completed')->nullable()->after('pp_recommended_peptide');
        });
    }

    public function down(): void
    {
        Schema::table('outbound_clicks', function (Blueprint $table) {
            $table->renameColumn('pp_experience_level', 'pp_experience');
            $table->dropColumn('pp_quiz_completed');
        });
    }
};
