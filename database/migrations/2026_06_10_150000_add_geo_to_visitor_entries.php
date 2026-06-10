<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Geo columns for the Visitor Log — resolved from IP (lazily, when an admin views
 * the log) and persisted so they show in the table + CSV and don't re-resolve.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitor_entries', function (Blueprint $table) {
            $table->string('country', 80)->nullable()->after('user_agent');
            $table->string('country_code', 2)->nullable()->after('country');
            $table->string('region', 80)->nullable()->after('country_code');
            $table->string('city', 120)->nullable()->after('region');
            $table->boolean('geo_resolved')->default(false)->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('visitor_entries', function (Blueprint $table) {
            $table->dropColumn(['country', 'country_code', 'region', 'city', 'geo_resolved']);
        });
    }
};
