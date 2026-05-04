<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('credentials')->nullable()->after('bio');
            $table->string('expertise')->nullable()->after('credentials');
            $table->string('twitter_url')->nullable()->after('expertise');
            $table->string('linkedin_url')->nullable()->after('twitter_url');
            $table->boolean('is_public_author')->default(false)->after('linkedin_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['slug', 'credentials', 'expertise', 'twitter_url', 'linkedin_url', 'is_public_author']);
        });
    }
};
