<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Peptides - queried on every public page
        Schema::table('peptides', function (Blueprint $table) {
            $table->index('is_published');
        });

        // Pages - catch-all route fires on every unknown URL
        Schema::table('pages', function (Blueprint $table) {
            $table->index('status');
        });

        // Subscribers - dashboard stats + listing filters
        Schema::table('subscribers', function (Blueprint $table) {
            $table->index('status');
            $table->index('source');
        });

        // Popups - queried on EVERY public page load
        if (Schema::hasColumn('popups', 'is_active')) {
            Schema::table('popups', function (Blueprint $table) {
                $table->index('is_active');
            });
        }

        // Quizzes - quiz page loads
        if (Schema::hasColumn('quizzes', 'is_active')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->index('is_active');
            });
        }

        // Lead magnets - landing page loads
        if (Schema::hasColumn('lead_magnets', 'is_active')) {
            Schema::table('lead_magnets', function (Blueprint $table) {
                $table->index('is_active');
            });
        }

        // Outbound links - redirect lookups
        if (Schema::hasColumn('outbound_links', 'is_active')) {
            Schema::table('outbound_links', function (Blueprint $table) {
                $table->index('is_active');
            });
        }

        // User sessions - IP lookups for returning visitor detection
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->index('ip_address');
        });

        // Supporters - footer rendering on every page
        Schema::table('supporters', function (Blueprint $table) {
            $table->index(['status', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::table('peptides', function (Blueprint $table) {
            $table->dropIndex(['is_published']);
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['source']);
        });
        if (Schema::hasColumn('popups', 'is_active')) {
            Schema::table('popups', function (Blueprint $table) {
                $table->dropIndex(['is_active']);
            });
        }
        if (Schema::hasColumn('quizzes', 'is_active')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropIndex(['is_active']);
            });
        }
        if (Schema::hasColumn('lead_magnets', 'is_active')) {
            Schema::table('lead_magnets', function (Blueprint $table) {
                $table->dropIndex(['is_active']);
            });
        }
        if (Schema::hasColumn('outbound_links', 'is_active')) {
            Schema::table('outbound_links', function (Blueprint $table) {
                $table->dropIndex(['is_active']);
            });
        }
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->dropIndex(['ip_address']);
        });
        Schema::table('supporters', function (Blueprint $table) {
            $table->dropIndex(['status', 'display_order']);
        });
    }
};
