<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Raw JSON-LD <script> block rendered into <head> (operator-controlled,
            // not user input). Used for rich-result structured data on CMS pages.
            $table->text('jsonld')->nullable()->after('css');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('jsonld');
        });
    }
};
