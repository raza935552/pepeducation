<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── subscribers ─────────────────────────────────────────
        Schema::table('subscribers', function (Blueprint $table) {
            $table->renameColumn('klaviyo_id', 'customerio_id');
            $table->renameColumn('klaviyo_synced_at', 'customerio_synced_at');
            $table->renameColumn('klaviyo_properties', 'customerio_properties');
        });

        // needs_klaviyo_sync has an index — drop it, rename, re-add
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropIndex(['needs_klaviyo_sync']);
            $table->renameColumn('needs_klaviyo_sync', 'needs_customerio_sync');
        });
        Schema::table('subscribers', function (Blueprint $table) {
            $table->index('needs_customerio_sync');
        });

        // ── quiz_responses ──────────────────────────────────────
        // synced_to_klaviyo has a composite index with status
        Schema::table('quiz_responses', function (Blueprint $table) {
            $table->dropIndex(['synced_to_klaviyo', 'status']);
            $table->renameColumn('klaviyo_properties', 'marketing_properties');
            $table->renameColumn('synced_to_klaviyo', 'synced_to_marketing');
        });
        Schema::table('quiz_responses', function (Blueprint $table) {
            $table->index(['synced_to_marketing', 'status']);
        });

        // ── quiz_questions ──────────────────────────────────────
        if (Schema::hasColumn('quiz_questions', 'klaviyo_property')) {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->renameColumn('klaviyo_property', 'marketing_property');
            });
        }

        // ── quiz_outcomes ───────────────────────────────────────
        if (Schema::hasColumn('quiz_outcomes', 'klaviyo_event')) {
            Schema::table('quiz_outcomes', function (Blueprint $table) {
                $table->renameColumn('klaviyo_event', 'marketing_event');
            });
        }
        if (Schema::hasColumn('quiz_outcomes', 'klaviyo_list_id')) {
            Schema::table('quiz_outcomes', function (Blueprint $table) {
                $table->renameColumn('klaviyo_list_id', 'marketing_list_id');
            });
        }
        if (Schema::hasColumn('quiz_outcomes', 'klaviyo_properties')) {
            Schema::table('quiz_outcomes', function (Blueprint $table) {
                $table->renameColumn('klaviyo_properties', 'marketing_properties');
            });
        }

        // ── quizzes ─────────────────────────────────────────────
        if (Schema::hasColumn('quizzes', 'klaviyo_list_id')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->renameColumn('klaviyo_list_id', 'marketing_list_id');
            });
        }
        if (Schema::hasColumn('quizzes', 'klaviyo_start_event')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->renameColumn('klaviyo_start_event', 'marketing_start_event');
            });
        }
        if (Schema::hasColumn('quizzes', 'klaviyo_complete_event')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->renameColumn('klaviyo_complete_event', 'marketing_complete_event');
            });
        }

        // ── lead_magnets ────────────────────────────────────────
        if (Schema::hasColumn('lead_magnets', 'klaviyo_flow_id')) {
            Schema::table('lead_magnets', function (Blueprint $table) {
                $table->renameColumn('klaviyo_flow_id', 'marketing_flow_id');
            });
        }
        if (Schema::hasColumn('lead_magnets', 'klaviyo_event')) {
            Schema::table('lead_magnets', function (Blueprint $table) {
                $table->renameColumn('klaviyo_event', 'marketing_event');
            });
        }
        if (Schema::hasColumn('lead_magnets', 'klaviyo_property_name')) {
            Schema::table('lead_magnets', function (Blueprint $table) {
                $table->renameColumn('klaviyo_property_name', 'marketing_property_name');
            });
        }

        // ── lead_magnet_downloads ───────────────────────────────
        if (Schema::hasColumn('lead_magnet_downloads', 'synced_to_klaviyo')) {
            Schema::table('lead_magnet_downloads', function (Blueprint $table) {
                $table->renameColumn('synced_to_klaviyo', 'synced_to_marketing');
            });
        }

        // ── popups ──────────────────────────────────────────────
        if (Schema::hasColumn('popups', 'klaviyo_list_id')) {
            Schema::table('popups', function (Blueprint $table) {
                $table->renameColumn('klaviyo_list_id', 'marketing_list_id');
            });
        }
        if (Schema::hasColumn('popups', 'klaviyo_event')) {
            Schema::table('popups', function (Blueprint $table) {
                $table->renameColumn('klaviyo_event', 'marketing_event');
            });
        }

        // ── outbound_links ──────────────────────────────────────
        if (Schema::hasColumn('outbound_links', 'track_klaviyo')) {
            Schema::table('outbound_links', function (Blueprint $table) {
                $table->renameColumn('track_klaviyo', 'track_marketing');
            });
        }

        // ── outbound_clicks ─────────────────────────────────────
        if (Schema::hasColumn('outbound_clicks', 'synced_to_klaviyo')) {
            Schema::table('outbound_clicks', function (Blueprint $table) {
                $table->renameColumn('synced_to_klaviyo', 'synced_to_marketing');
            });
        }

        // ── user_sessions ───────────────────────────────────────
        if (Schema::hasColumn('user_sessions', 'synced_to_klaviyo')) {
            Schema::table('user_sessions', function (Blueprint $table) {
                $table->renameColumn('synced_to_klaviyo', 'synced_to_marketing');
            });
        }

        // ── user_events ─────────────────────────────────────────
        // synced_to_klaviyo has a composite index with event_type
        if (Schema::hasColumn('user_events', 'synced_to_klaviyo')) {
            Schema::table('user_events', function (Blueprint $table) {
                $table->dropIndex(['synced_to_klaviyo', 'event_type']);
                $table->renameColumn('synced_to_klaviyo', 'synced_to_marketing');
            });
            Schema::table('user_events', function (Blueprint $table) {
                $table->index(['synced_to_marketing', 'event_type']);
            });
        }
    }

    public function down(): void
    {
        // ── subscribers ─────────────────────────────────────────
        Schema::table('subscribers', function (Blueprint $table) {
            $table->renameColumn('customerio_id', 'klaviyo_id');
            $table->renameColumn('customerio_synced_at', 'klaviyo_synced_at');
            $table->renameColumn('customerio_properties', 'klaviyo_properties');
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropIndex(['needs_customerio_sync']);
            $table->renameColumn('needs_customerio_sync', 'needs_klaviyo_sync');
        });
        Schema::table('subscribers', function (Blueprint $table) {
            $table->index('needs_klaviyo_sync');
        });

        // ── quiz_responses ──────────────────────────────────────
        Schema::table('quiz_responses', function (Blueprint $table) {
            $table->dropIndex(['synced_to_marketing', 'status']);
            $table->renameColumn('marketing_properties', 'klaviyo_properties');
            $table->renameColumn('synced_to_marketing', 'synced_to_klaviyo');
        });
        Schema::table('quiz_responses', function (Blueprint $table) {
            $table->index(['synced_to_klaviyo', 'status']);
        });

        // ── quiz_questions ──────────────────────────────────────
        if (Schema::hasColumn('quiz_questions', 'marketing_property')) {
            Schema::table('quiz_questions', function (Blueprint $table) {
                $table->renameColumn('marketing_property', 'klaviyo_property');
            });
        }

        // ── quiz_outcomes ───────────────────────────────────────
        if (Schema::hasColumn('quiz_outcomes', 'marketing_event')) {
            Schema::table('quiz_outcomes', function (Blueprint $table) {
                $table->renameColumn('marketing_event', 'klaviyo_event');
            });
        }
        if (Schema::hasColumn('quiz_outcomes', 'marketing_list_id')) {
            Schema::table('quiz_outcomes', function (Blueprint $table) {
                $table->renameColumn('marketing_list_id', 'klaviyo_list_id');
            });
        }
        if (Schema::hasColumn('quiz_outcomes', 'marketing_properties')) {
            Schema::table('quiz_outcomes', function (Blueprint $table) {
                $table->renameColumn('marketing_properties', 'klaviyo_properties');
            });
        }

        // ── quizzes ─────────────────────────────────────────────
        if (Schema::hasColumn('quizzes', 'marketing_list_id')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->renameColumn('marketing_list_id', 'klaviyo_list_id');
            });
        }
        if (Schema::hasColumn('quizzes', 'marketing_start_event')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->renameColumn('marketing_start_event', 'klaviyo_start_event');
            });
        }
        if (Schema::hasColumn('quizzes', 'marketing_complete_event')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->renameColumn('marketing_complete_event', 'klaviyo_complete_event');
            });
        }

        // ── lead_magnets ────────────────────────────────────────
        if (Schema::hasColumn('lead_magnets', 'marketing_flow_id')) {
            Schema::table('lead_magnets', function (Blueprint $table) {
                $table->renameColumn('marketing_flow_id', 'klaviyo_flow_id');
            });
        }
        if (Schema::hasColumn('lead_magnets', 'marketing_event')) {
            Schema::table('lead_magnets', function (Blueprint $table) {
                $table->renameColumn('marketing_event', 'klaviyo_event');
            });
        }
        if (Schema::hasColumn('lead_magnets', 'marketing_property_name')) {
            Schema::table('lead_magnets', function (Blueprint $table) {
                $table->renameColumn('marketing_property_name', 'klaviyo_property_name');
            });
        }

        // ── lead_magnet_downloads ───────────────────────────────
        if (Schema::hasColumn('lead_magnet_downloads', 'synced_to_marketing')) {
            Schema::table('lead_magnet_downloads', function (Blueprint $table) {
                $table->renameColumn('synced_to_marketing', 'synced_to_klaviyo');
            });
        }

        // ── popups ──────────────────────────────────────────────
        if (Schema::hasColumn('popups', 'marketing_list_id')) {
            Schema::table('popups', function (Blueprint $table) {
                $table->renameColumn('marketing_list_id', 'klaviyo_list_id');
            });
        }
        if (Schema::hasColumn('popups', 'marketing_event')) {
            Schema::table('popups', function (Blueprint $table) {
                $table->renameColumn('marketing_event', 'klaviyo_event');
            });
        }

        // ── outbound_links ──────────────────────────────────────
        if (Schema::hasColumn('outbound_links', 'track_marketing')) {
            Schema::table('outbound_links', function (Blueprint $table) {
                $table->renameColumn('track_marketing', 'track_klaviyo');
            });
        }

        // ── outbound_clicks ─────────────────────────────────────
        if (Schema::hasColumn('outbound_clicks', 'synced_to_marketing')) {
            Schema::table('outbound_clicks', function (Blueprint $table) {
                $table->renameColumn('synced_to_marketing', 'synced_to_klaviyo');
            });
        }

        // ── user_sessions ───────────────────────────────────────
        if (Schema::hasColumn('user_sessions', 'synced_to_marketing')) {
            Schema::table('user_sessions', function (Blueprint $table) {
                $table->renameColumn('synced_to_marketing', 'synced_to_klaviyo');
            });
        }

        // ── user_events ─────────────────────────────────────────
        if (Schema::hasColumn('user_events', 'synced_to_marketing')) {
            Schema::table('user_events', function (Blueprint $table) {
                $table->dropIndex(['synced_to_marketing', 'event_type']);
                $table->renameColumn('synced_to_marketing', 'synced_to_klaviyo');
            });
            Schema::table('user_events', function (Blueprint $table) {
                $table->index(['synced_to_klaviyo', 'event_type']);
            });
        }
    }
};
