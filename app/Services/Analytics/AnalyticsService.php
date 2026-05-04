<?php

namespace App\Services\Analytics;

use App\Models\UserSession;
use App\Models\UserEvent;
use App\Models\Subscriber;
use App\Models\QuizResponse;
use App\Models\PopupInteraction;
use App\Models\LeadMagnetDownload;
use App\Models\OutboundClick;
use App\Models\TrackingError;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    // ─── Utility ─────────────────────────────────────────────

    public function cacheTtl(string $period): int
    {
        return match ($period) {
            '24h' => 300,
            '7d' => 1800,
            '30d' => 3600,
            '90d' => 7200,
            default => 1800,
        };
    }

    public function getStartDate(string $period): Carbon
    {
        return match ($period) {
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            default => now()->subDays(7),
        };
    }

    public function getPreviousStartDate(Carbon $startDate, string $period): Carbon
    {
        $days = (int) now()->diffInDays($startDate);
        return $startDate->copy()->subDays($days);
    }

    // ─── Overview Tab ────────────────────────────────────────

    public function getOverviewKPIs(Carbon $startDate): array
    {
        return [
            'sessions' => UserSession::where('created_at', '>=', $startDate)->count(),
            'uniqueVisitors' => UserSession::where('created_at', '>=', $startDate)
                ->where('is_returning', false)->count(),
            'pageViews' => UserEvent::where('created_at', '>=', $startDate)
                ->where('event_type', 'page_view')->count(),
            'avgEngagement' => round(UserSession::where('created_at', '>=', $startDate)
                ->avg('engagement_score') ?? 0, 1),
            'newSubscribers' => Subscriber::where('created_at', '>=', $startDate)->count(),
            'conversions' => UserSession::where('created_at', '>=', $startDate)
                ->where('converted', true)->count(),
        ];
    }

    public function getPreviousPeriodKPIs(Carbon $startDate, string $period): array
    {
        $prevStart = $this->getPreviousStartDate($startDate, $period);
        return $this->getOverviewKPIs($prevStart);
    }

    public function getSegmentDistribution(Carbon $startDate): array
    {
        return UserSession::where('created_at', '>=', $startDate)
            ->whereNotNull('segment')
            ->select('segment', DB::raw('count(*) as count'))
            ->groupBy('segment')
            ->pluck('count', 'segment')
            ->toArray();
    }

    public function getTopEvents(Carbon $startDate): array
    {
        return UserEvent::where('created_at', '>=', $startDate)
            ->select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'event_type')
            ->toArray();
    }

    public function getDailyTrend(Carbon $startDate): array
    {
        $sessions = UserSession::where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $pageViews = UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'page_view')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return [
            'labels' => array_keys($sessions),
            'sessions' => array_values($sessions),
            'pageViews' => array_map(fn ($d) => $pageViews[$d] ?? 0, array_keys($sessions)),
        ];
    }

    // ─── Traffic Tab ─────────────────────────────────────────

    public function getTrafficSources(Carbon $startDate): array
    {
        return UserSession::where('created_at', '>=', $startDate)
            ->whereNotNull('referrer_domain')
            ->where('referrer_domain', '!=', '')
            ->select('referrer_domain', DB::raw('count(*) as sessions'))
            ->groupBy('referrer_domain')
            ->orderByDesc('sessions')
            ->limit(10)
            ->pluck('sessions', 'referrer_domain')
            ->toArray();
    }

    public function getUtmCampaigns(Carbon $startDate): array
    {
        return UserSession::where('created_at', '>=', $startDate)
            ->whereNotNull('utm_source')
            ->where('utm_source', '!=', '')
            ->select(
                'utm_source',
                'utm_medium',
                'utm_campaign',
                DB::raw('count(*) as sessions'),
                DB::raw('SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as conversions')
            )
            ->groupBy('utm_source', 'utm_medium', 'utm_campaign')
            ->orderByDesc('sessions')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'source' => $row->utm_source,
                'medium' => $row->utm_medium ?: '(none)',
                'campaign' => $row->utm_campaign ?: '(none)',
                'sessions' => $row->sessions,
                'conversions' => $row->conversions,
                'cvr' => $row->sessions > 0 ? round($row->conversions / $row->sessions * 100, 1) : 0,
            ])
            ->toArray();
    }

    public function getDeviceBreakdown(Carbon $startDate): array
    {
        return UserSession::where('created_at', '>=', $startDate)
            ->whereNotNull('device_type')
            ->select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();
    }

    public function getBrowserBreakdown(Carbon $startDate): array
    {
        return UserSession::where('created_at', '>=', $startDate)
            ->whereNotNull('browser')
            ->where('browser', '!=', '')
            ->select('browser', DB::raw('count(*) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'browser')
            ->toArray();
    }

    public function getOsBreakdown(Carbon $startDate): array
    {
        return UserSession::where('created_at', '>=', $startDate)
            ->whereNotNull('os')
            ->where('os', '!=', '')
            ->select('os', DB::raw('count(*) as count'))
            ->groupBy('os')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'os')
            ->toArray();
    }

    public function getNewVsReturning(Carbon $startDate): array
    {
        $total = UserSession::where('created_at', '>=', $startDate)->count();
        $returning = UserSession::where('created_at', '>=', $startDate)
            ->where('is_returning', true)->count();
        $new = $total - $returning;

        return [
            'new' => $new,
            'returning' => $returning,
            'total' => $total,
            'newPct' => $total > 0 ? round($new / $total * 100, 1) : 0,
            'returningPct' => $total > 0 ? round($returning / $total * 100, 1) : 0,
        ];
    }

    public function getTopLandingPages(Carbon $startDate): array
    {
        return UserSession::where('created_at', '>=', $startDate)
            ->whereNotNull('entry_url')
            ->select(
                'entry_url',
                DB::raw('count(*) as sessions'),
                DB::raw('SUM(CASE WHEN is_bounced = 1 THEN 1 ELSE 0 END) as bounced')
            )
            ->groupBy('entry_url')
            ->orderByDesc('sessions')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'url' => $row->entry_url,
                'sessions' => $row->sessions,
                'bounceRate' => $row->sessions > 0 ? round($row->bounced / $row->sessions * 100, 1) : 0,
            ])
            ->toArray();
    }

    // ─── Content Tab ─────────────────────────────────────────

    public function getTopPages(Carbon $startDate): array
    {
        return DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'page_view')
            ->whereNotNull('page_url')
            ->select(
                'page_url',
                DB::raw('count(*) as views'),
                DB::raw('AVG(NULLIF(scroll_depth, 0)) as avg_scroll'),
                DB::raw('AVG(NULLIF(time_on_page, 0)) as avg_time')
            )
            ->groupBy('page_url')
            ->orderByDesc('views')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'url' => $row->page_url,
                'views' => $row->views,
                'avgScroll' => round($row->avg_scroll ?? 0),
                'avgTime' => round($row->avg_time ?? 0),
            ])
            ->toArray();
    }

    public function getBlogPerformance(Carbon $startDate): array
    {
        return DB::table('user_events')
            ->where('user_events.created_at', '>=', $startDate)
            ->where('user_events.event_type', 'page_view')
            ->where('user_events.page_url', 'like', '%/blog/%')
            ->where('user_events.page_url', 'not like', '%/blog/category/%')
            ->where('user_events.page_url', 'not like', '%/blog/tag/%')
            ->select(
                'user_events.page_url',
                'user_events.page_title',
                DB::raw('count(*) as views'),
                DB::raw('AVG(NULLIF(user_events.scroll_depth, 0)) as avg_scroll')
            )
            ->groupBy('user_events.page_url', 'user_events.page_title')
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'url' => $row->page_url,
                'title' => $row->page_title ?: basename($row->page_url),
                'views' => $row->views,
                'avgScroll' => round($row->avg_scroll ?? 0),
            ])
            ->toArray();
    }

    public function getScrollDepthDistribution(Carbon $startDate): array
    {
        $buckets = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'scroll')
            ->where('scroll_depth', '>', 0)
            ->select(DB::raw("
                CASE
                    WHEN scroll_depth <= 25 THEN '0-25%'
                    WHEN scroll_depth <= 50 THEN '25-50%'
                    WHEN scroll_depth <= 75 THEN '50-75%'
                    ELSE '75-100%'
                END as bucket
            "), DB::raw('count(*) as count'))
            ->groupBy('bucket')
            ->pluck('count', 'bucket')
            ->toArray();

        return [
            '0-25%' => $buckets['0-25%'] ?? 0,
            '25-50%' => $buckets['25-50%'] ?? 0,
            '50-75%' => $buckets['50-75%'] ?? 0,
            '75-100%' => $buckets['75-100%'] ?? 0,
        ];
    }

    public function getAvgTimeOnPage(Carbon $startDate): array
    {
        return DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'page_view')
            ->where('time_on_page', '>', 0)
            ->whereNotNull('page_url')
            ->select(
                'page_url',
                DB::raw('AVG(time_on_page) as avg_time'),
                DB::raw('count(*) as views')
            )
            ->groupBy('page_url')
            ->having('views', '>=', 3)
            ->orderByDesc('avg_time')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'url' => $row->page_url,
                'avgTime' => round($row->avg_time),
                'views' => $row->views,
            ])
            ->toArray();
    }

    // ─── Funnels Tab ─────────────────────────────────────────

    public function getCTAStats(Carbon $startDate): array
    {
        $ctaClicks = UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click');

        $topCTAs = UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click')
            ->whereNotNull('element_text')
            ->select('element_text', DB::raw('count(*) as clicks'))
            ->groupBy('element_text')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'name' => $item->element_text,
                'clicks' => $item->clicks,
            ])
            ->toArray();

        $byType = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click')
            ->selectRaw("COALESCE(JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.cta_type')), 'general') as cta_type, COUNT(*) as cnt")
            ->groupBy('cta_type')
            ->pluck('cnt', 'cta_type')
            ->toArray();

        $byPosition = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click')
            ->selectRaw("COALESCE(JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.cta_position')), 'unknown') as cta_position, COUNT(*) as cnt")
            ->groupBy('cta_position')
            ->pluck('cnt', 'cta_position')
            ->toArray();

        $bySourcePage = UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click')
            ->select('page_url', DB::raw('count(*) as clicks'))
            ->groupBy('page_url')
            ->orderByDesc('clicks')
            ->limit(10)
            ->pluck('clicks', 'page_url')
            ->toArray();

        return [
            'total' => (clone $ctaClicks)->count(),
            'topCTAs' => $topCTAs,
            'byType' => $byType,
            'byPosition' => $byPosition,
            'bySourcePage' => $bySourcePage,
        ];
    }

    public function getQuizStats(Carbon $startDate): array
    {
        $responses = QuizResponse::where('created_at', '>=', $startDate);

        return [
            'total' => $responses->count(),
            'completed' => (clone $responses)->whereNotNull('completed_at')->count(),
            'byQuiz' => QuizResponse::where('quiz_responses.created_at', '>=', $startDate)
                ->join('quizzes', 'quiz_responses.quiz_id', '=', 'quizzes.id')
                ->select('quizzes.name', DB::raw('count(*) as count'))
                ->groupBy('quizzes.id', 'quizzes.name')
                ->pluck('count', 'name')
                ->toArray(),
        ];
    }

    public function getPopupStats(Carbon $startDate): array
    {
        return [
            'impressions' => PopupInteraction::where('created_at', '>=', $startDate)
                ->where('interaction_type', 'view')->count(),
            'conversions' => PopupInteraction::where('created_at', '>=', $startDate)
                ->where('interaction_type', 'convert')->count(),
            'byPopup' => PopupInteraction::where('popup_interactions.created_at', '>=', $startDate)
                ->join('popups', 'popup_interactions.popup_id', '=', 'popups.id')
                ->select('popups.name', DB::raw('count(*) as count'))
                ->groupBy('popups.id', 'popups.name')
                ->pluck('count', 'name')
                ->toArray(),
        ];
    }

    public function getLeadMagnetStats(Carbon $startDate): array
    {
        return LeadMagnetDownload::where('lead_magnet_downloads.created_at', '>=', $startDate)
            ->join('lead_magnets', 'lead_magnet_downloads.lead_magnet_id', '=', 'lead_magnets.id')
            ->select('lead_magnets.name', DB::raw('count(*) as count'))
            ->groupBy('lead_magnets.id', 'lead_magnets.name')
            ->pluck('count', 'name')
            ->toArray();
    }

    public function getOutboundStats(Carbon $startDate): array
    {
        return OutboundClick::where('outbound_clicks.created_at', '>=', $startDate)
            ->join('outbound_links', 'outbound_clicks.outbound_link_id', '=', 'outbound_links.id')
            ->select('outbound_links.name', DB::raw('count(*) as count'))
            ->groupBy('outbound_links.id', 'outbound_links.name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'name')
            ->toArray();
    }

    public function getConversionFunnel(Carbon $startDate): array
    {
        $sessions = UserSession::where('created_at', '>=', $startDate)->count();

        $quizStarts = UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'quiz_start')
            ->distinct('session_id')->count('session_id');

        $quizCompletes = UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'quiz_complete')
            ->distinct('session_id')->count('session_id');

        $subscribers = Subscriber::where('created_at', '>=', $startDate)->count();

        $shopClicks = OutboundClick::where('created_at', '>=', $startDate)->count();

        $steps = [
            ['label' => 'Visits', 'count' => $sessions],
            ['label' => 'Quiz Started', 'count' => $quizStarts],
            ['label' => 'Quiz Completed', 'count' => $quizCompletes],
            ['label' => 'Subscribed', 'count' => $subscribers],
            ['label' => 'Shop Clicks', 'count' => $shopClicks],
        ];

        $prev = null;
        foreach ($steps as &$step) {
            $step['dropoff'] = $prev !== null && $prev > 0
                ? round((1 - $step['count'] / $prev) * 100, 1)
                : 0;
            $prev = $step['count'];
        }

        return $steps;
    }

    // ─── Engagement Tab ──────────────────────────────────────

    public function getEngagementDistribution(Carbon $startDate): array
    {
        $buckets = UserSession::where('created_at', '>=', $startDate)
            ->select(DB::raw("
                CASE
                    WHEN engagement_score <= 5 THEN '0-5'
                    WHEN engagement_score <= 15 THEN '5-15'
                    WHEN engagement_score <= 30 THEN '15-30'
                    WHEN engagement_score <= 50 THEN '30-50'
                    ELSE '50+'
                END as bucket
            "), DB::raw('count(*) as count'))
            ->groupBy('bucket')
            ->pluck('count', 'bucket')
            ->toArray();

        return [
            '0-5' => $buckets['0-5'] ?? 0,
            '5-15' => $buckets['5-15'] ?? 0,
            '15-30' => $buckets['15-30'] ?? 0,
            '30-50' => $buckets['30-50'] ?? 0,
            '50+' => $buckets['50+'] ?? 0,
        ];
    }

    public function getEngagementTierTrend(Carbon $startDate): array
    {
        $rows = UserSession::where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN engagement_score >= 40 THEN 1 ELSE 0 END) as hot'),
                DB::raw('SUM(CASE WHEN engagement_score >= 15 AND engagement_score < 40 THEN 1 ELSE 0 END) as warm'),
                DB::raw('SUM(CASE WHEN engagement_score < 15 THEN 1 ELSE 0 END) as cold')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $rows->pluck('date')->toArray(),
            'hot' => $rows->pluck('hot')->toArray(),
            'warm' => $rows->pluck('warm')->toArray(),
            'cold' => $rows->pluck('cold')->toArray(),
        ];
    }

    public function getRageClickHotspots(Carbon $startDate): array
    {
        return UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'rage_click')
            ->select(
                'page_url',
                DB::raw('count(*) as count'),
                DB::raw('MAX(created_at) as last_occurred')
            )
            ->groupBy('page_url')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'url' => $row->page_url,
                'count' => $row->count,
                'lastOccurred' => Carbon::parse($row->last_occurred)->diffForHumans(),
            ])
            ->toArray();
    }

    public function getTopEngagedPages(Carbon $startDate): array
    {
        return DB::table('user_events')
            ->join('user_sessions', 'user_events.session_id', '=', 'user_sessions.session_id')
            ->where('user_events.created_at', '>=', $startDate)
            ->where('user_events.event_type', 'page_view')
            ->whereNotNull('user_events.page_url')
            ->select(
                'user_events.page_url',
                DB::raw('AVG(user_sessions.engagement_score) as avg_score'),
                DB::raw('COUNT(DISTINCT user_events.session_id) as sessions')
            )
            ->groupBy('user_events.page_url')
            ->having('sessions', '>=', 3)
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'url' => $row->page_url,
                'avgScore' => round($row->avg_score, 1),
                'sessions' => $row->sessions,
            ])
            ->toArray();
    }

    public function getBounceRate(Carbon $startDate): float
    {
        $total = UserSession::where('created_at', '>=', $startDate)->count();
        if ($total === 0) return 0;

        $bounced = UserSession::where('created_at', '>=', $startDate)
            ->where('is_bounced', true)->count();

        return round($bounced / $total * 100, 1);
    }

    // ─── Errors ──────────────────────────────────────────────

    public function getRecentErrors(): \Illuminate\Support\Collection
    {
        return TrackingError::orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    // ─── Deep Content Insights ───────────────────────────────

    /**
     * Comprehensive blog post performance: views, avg time, scroll depth,
     * buy CTA clicks attributed to that post, and conversion rate (clicks/views).
     */
    public function getEnhancedBlogPerformance(Carbon $startDate, int $limit = 30): array
    {
        // View counts from page_view events
        $views = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'page_view')
            ->where('page_url', 'like', '%/blog/%')
            ->where('page_url', 'not like', '%/blog/category/%')
            ->where('page_url', 'not like', '%/blog/tag/%')
            ->where('page_url', 'not like', '%/blog/author/%')
            ->select('page_url', 'page_title', DB::raw('COUNT(*) as views'))
            ->groupBy('page_url', 'page_title')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();

        // Scroll metrics from scroll events
        $engagement = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'scroll')
            ->where('page_url', 'like', '%/blog/%')
            ->select(
                'page_url',
                DB::raw('AVG(NULLIF(scroll_depth, 0)) as avg_scroll'),
                DB::raw('MAX(scroll_depth) as max_scroll')
            )
            ->groupBy('page_url')
            ->get()
            ->keyBy('page_url');

        // Average dwell time per page derived from event timestamps in same session+url
        // Cap individual session dwell at 1800s (30 minutes) to filter out abandoned tabs
        $timeOnPage = DB::query()
            ->fromSub(function ($q) use ($startDate) {
                $q->from('user_events')
                  ->where('created_at', '>=', $startDate)
                  ->where('page_url', 'like', '%/blog/%')
                  ->whereNotNull('session_id')
                  ->select('session_id', 'page_url', DB::raw('LEAST(1800, TIMESTAMPDIFF(SECOND, MIN(created_at), MAX(created_at))) as max_t'), DB::raw('COUNT(*) as event_count'))
                  ->groupBy('session_id', 'page_url')
                  ->havingRaw('event_count > 1');
            }, 'sub')
            ->select('page_url', DB::raw('AVG(max_t) as avg_time'))
            ->groupBy('page_url')
            ->get()
            ->keyBy('page_url');

        $stats = $views->map(function ($row) use ($engagement, $timeOnPage) {
            $eng = $engagement->get($row->page_url);
            $row->avg_scroll = $eng?->avg_scroll;
            $row->avg_time = $timeOnPage->get($row->page_url)?->avg_time ?? 0;
            return $row;
        });

        // Buy CTA clicks attributed to blog pages in this window (by source_url match)
        $clicksByUrl = DB::table('buy_clicks')
            ->where('created_at', '>=', $startDate)
            ->where('source_url', 'like', '%/blog/%')
            ->select('source_url', DB::raw('COUNT(*) as clicks'))
            ->groupBy('source_url')
            ->pluck('clicks', 'source_url')
            ->toArray();

        // Map URL to the BlogPost row so we can show author + reading time
        $slugs = $stats->map(function ($row) {
            if (preg_match('#/blog/([a-z0-9\-]+)#', $row->page_url, $m)) {
                return $m[1];
            }
            return null;
        })->filter()->unique()->values()->all();

        $posts = \App\Models\BlogPost::whereIn('slug', $slugs)
            ->with('author:id,name,slug')
            ->select('id', 'slug', 'title', 'reading_time', 'created_by', 'published_at')
            ->get()
            ->keyBy('slug');

        return $stats->map(function ($row) use ($clicksByUrl, $posts) {
            $slug = preg_match('#/blog/([a-z0-9\-]+)#', $row->page_url, $m) ? $m[1] : null;
            $post = $slug ? ($posts[$slug] ?? null) : null;
            $clicks = (int) ($clicksByUrl[$row->page_url] ?? 0);
            $views = (int) $row->views;

            return [
                'slug'        => $slug,
                'title'       => $post?->title ?: $row->page_title ?: $slug,
                'url'         => $row->page_url,
                'views'       => $views,
                'avg_scroll'  => round((float) ($row->avg_scroll ?? 0)),
                'avg_time'    => round((float) ($row->avg_time ?? 0)),
                'reading_min' => $post?->reading_time,
                'author'      => $post?->author?->name,
                'buy_clicks'  => $clicks,
                'click_rate'  => $views > 0 ? round(($clicks / $views) * 100, 2) : 0,
                'engagement'  => round(((float) ($row->avg_scroll ?? 0)) * 0.5 + min(100, ((float) ($row->avg_time ?? 0)) / 3) * 0.5),
            ];
        })->toArray();
    }

    /**
     * Comprehensive peptide page performance: same as blog but for /peptides/{slug}.
     */
    public function getEnhancedPeptidePerformance(Carbon $startDate, int $limit = 30): array
    {
        $views = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'page_view')
            ->where('page_url', 'like', '%/peptides/%')
            ->where('page_url', 'not like', '%/peptides/compare%')
            ->select('page_url', 'page_title', DB::raw('COUNT(*) as views'))
            ->groupBy('page_url', 'page_title')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();

        $engagement = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'scroll')
            ->where('page_url', 'like', '%/peptides/%')
            ->select('page_url', DB::raw('AVG(NULLIF(scroll_depth, 0)) as avg_scroll'))
            ->groupBy('page_url')
            ->get()
            ->keyBy('page_url');

        $timeOnPage = DB::query()
            ->fromSub(function ($q) use ($startDate) {
                $q->from('user_events')
                  ->where('created_at', '>=', $startDate)
                  ->where('page_url', 'like', '%/peptides/%')
                  ->whereNotNull('session_id')
                  ->select('session_id', 'page_url', DB::raw('LEAST(1800, TIMESTAMPDIFF(SECOND, MIN(created_at), MAX(created_at))) as max_t'), DB::raw('COUNT(*) as event_count'))
                  ->groupBy('session_id', 'page_url')
                  ->havingRaw('event_count > 1');
            }, 'sub')
            ->select('page_url', DB::raw('AVG(max_t) as avg_time'))
            ->groupBy('page_url')
            ->get()
            ->keyBy('page_url');

        $stats = $views->map(function ($row) use ($engagement, $timeOnPage) {
            $eng = $engagement->get($row->page_url);
            $row->avg_scroll = $eng?->avg_scroll;
            $row->avg_time = $timeOnPage->get($row->page_url)?->avg_time ?? 0;
            return $row;
        });

        $clicksByUrl = DB::table('buy_clicks')
            ->where('created_at', '>=', $startDate)
            ->where('source_url', 'like', '%/peptides/%')
            ->select('source_url', DB::raw('COUNT(*) as clicks'))
            ->groupBy('source_url')
            ->pluck('clicks', 'source_url')
            ->toArray();

        $slugs = $stats->map(function ($row) {
            if (preg_match('#/peptides/([a-z0-9\-]+)#', $row->page_url, $m)) {
                return $m[1];
            }
            return null;
        })->filter()->unique()->values()->all();

        $peptides = \App\Models\Peptide::whereIn('slug', $slugs)
            ->select('id', 'slug', 'name', 'is_published')
            ->get()
            ->keyBy('slug');

        return $stats->map(function ($row) use ($clicksByUrl, $peptides) {
            $slug = preg_match('#/peptides/([a-z0-9\-]+)#', $row->page_url, $m) ? $m[1] : null;
            $peptide = $slug ? ($peptides[$slug] ?? null) : null;
            $clicks = (int) ($clicksByUrl[$row->page_url] ?? 0);
            $views = (int) $row->views;

            return [
                'slug'       => $slug,
                'name'       => $peptide?->name ?: $row->page_title ?: $slug,
                'url'        => $row->page_url,
                'views'      => $views,
                'avg_scroll' => round((float) ($row->avg_scroll ?? 0)),
                'avg_time'   => round((float) ($row->avg_time ?? 0)),
                'buy_clicks' => $clicks,
                'click_rate' => $views > 0 ? round(($clicks / $views) * 100, 2) : 0,
                'published'  => $peptide?->is_published ?? false,
            ];
        })->toArray();
    }

    /**
     * Pages with notable mismatch between traffic and engagement.
     * Returns:
     *   gaps: high views, low engagement (need content quality work)
     *   gems: high engagement, low views (need promotion)
     */
    public function getContentGapsAndGems(Carbon $startDate): array
    {
        $views = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'page_view')
            ->where(function ($q) {
                $q->where('page_url', 'like', '%/blog/%')
                  ->orWhere('page_url', 'like', '%/peptides/%');
            })
            ->where('page_url', 'not like', '%/blog/category/%')
            ->where('page_url', 'not like', '%/blog/tag/%')
            ->where('page_url', 'not like', '%/peptides/compare%')
            ->select('page_url', 'page_title', DB::raw('COUNT(*) as views'))
            ->groupBy('page_url', 'page_title')
            ->having('views', '>=', 5)
            ->get();

        $engagement = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'scroll')
            ->select(
                'page_url',
                DB::raw('AVG(NULLIF(scroll_depth, 0)) as avg_scroll'),
                DB::raw('AVG(NULLIF(time_on_page, 0)) as avg_time')
            )
            ->groupBy('page_url')
            ->get()
            ->keyBy('page_url');

        $rows = $views->map(function ($row) use ($engagement) {
            $eng = $engagement->get($row->page_url);
            $row->avg_scroll = $eng?->avg_scroll;
            $row->avg_time = $eng?->avg_time;
            return $row;
        });

        if ($rows->isEmpty()) {
            return ['gaps' => [], 'gems' => [], 'medianViews' => 0, 'medianEngagement' => 0];
        }

        $viewsArr = $rows->pluck('views')->sort()->values();
        $medianViews = $viewsArr->get(intdiv($viewsArr->count(), 2));

        $engagement = $rows->map(function ($r) {
            return ((float) ($r->avg_scroll ?? 0)) * 0.5 + min(100, ((float) ($r->avg_time ?? 0)) / 3) * 0.5;
        });
        $engArr = $engagement->sort()->values();
        $medianEng = $engArr->get(intdiv($engArr->count(), 2));

        $gaps = [];
        $gems = [];
        foreach ($rows as $i => $r) {
            $eng = ((float) ($r->avg_scroll ?? 0)) * 0.5 + min(100, ((float) ($r->avg_time ?? 0)) / 3) * 0.5;
            $entry = [
                'url' => $r->page_url,
                'title' => $r->page_title ?: basename($r->page_url),
                'views' => (int) $r->views,
                'avg_scroll' => round((float) ($r->avg_scroll ?? 0)),
                'avg_time' => round((float) ($r->avg_time ?? 0)),
                'engagement' => round($eng),
            ];

            if ((int) $r->views > $medianViews && $eng < $medianEng) {
                $gaps[] = $entry;
            }
            if ((int) $r->views < $medianViews && $eng > $medianEng) {
                $gems[] = $entry;
            }
        }

        usort($gaps, fn ($a, $b) => $b['views'] - $a['views']);
        usort($gems, fn ($a, $b) => $b['engagement'] - $a['engagement']);

        return [
            'gaps' => array_slice($gaps, 0, 10),
            'gems' => array_slice($gems, 0, 10),
            'medianViews' => (int) $medianViews,
            'medianEngagement' => round($medianEng),
        ];
    }

    /**
     * Internal site searches that produced zero results (content gap signal).
     */
    public function getInternalSearchGaps(Carbon $startDate): array
    {
        try {
            return DB::table('search_logs')
                ->where('created_at', '>=', $startDate)
                ->where('result_count', 0)
                ->select('query', DB::raw('COUNT(*) as searches'))
                ->groupBy('query')
                ->orderByDesc('searches')
                ->limit(20)
                ->get()
                ->map(fn ($r) => ['query' => $r->query, 'searches' => (int) $r->searches])
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
