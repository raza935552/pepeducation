<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_seed')->default(false)->index()->after('is_suspended');
            $table->timestamp('last_seen_at')->nullable()->after('is_seed');
            $table->unsignedInteger('forum_posts_count')->default(0)->after('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_seed', 'last_seen_at', 'forum_posts_count']);
        });
    }
};
