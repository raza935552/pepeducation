<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use App\Models\UserEvent;
use App\Models\Subscriber;
use App\Models\QuizResponse;
use App\Models\PopupInteraction;
use App\Models\LeadMagnetDownload;
use App\Models\OutboundClick;
use App\Models\TrackingError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '7d');
        $startDate = $this->getStartDate($period);
        $ttl = $this->cacheTtl($period);

        $cached = fn(string $key, callable $cb) => Cache::remember("analytics:{$period}:{$key}", $ttl, $cb);

        return view('admin.analytics.index', [
            'period' => $period,
            'overview' => $cached('overview', fn() => $this->getOverviewStats($startDate)),
            'segments' => $cached('segments', fn() => $this->getSegmentDistribution($startDate)),
            'topEvents' => $cached('topEvents', fn() => $this->getTopEvents($startDate)),
            'ctaStats' => $cached('ctaStats', fn() => $this->getCTAStats($startDate)),
            'quizStats' => $cached('quizStats', fn() => $this->getQuizStats($startDate)),
            'popupStats' => $cached('popupStats', fn() => $this->getPopupStats($startDate)),
            'leadMagnetStats' => $cached('leadMagnets', fn() => $this->getLeadMagnetStats($startDate)),
            'outboundStats' => $cached('outbound', fn() => $this->getOutboundStats($startDate)),
            'recentErrors' => $cached('errors', fn() => $this->getRecentErrors()),
            'dailyTrend' => $cached('trend', fn() => $this->getDailyTrend($startDate)),
        ]);
    }

    private function cacheTtl(string $period): int
    {
        return match($period) {
            '24h' => 300,    // 5 minutes
            '7d' => 1800,    // 30 minutes
            '30d' => 3600,   // 1 hour
            '90d' => 7200,   // 2 hours
            default => 1800,
        };
    }

    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            default => now()->subDays(7),
        };
    }

    private function getOverviewStats(Carbon $startDate): array
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

    private function getSegmentDistribution(Carbon $startDate): array
    {
        return UserSession::where('created_at', '>=', $startDate)
            ->whereNotNull('segment')
            ->select('segment', DB::raw('count(*) as count'))
            ->groupBy('segment')
            ->pluck('count', 'segment')
            ->toArray();
    }

    private function getTopEvents(Carbon $startDate): array
    {
        return UserEvent::where('created_at', '>=', $startDate)
            ->select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'event_type')
            ->toArray();
    }

    private function getCTAStats(Carbon $startDate): array
    {
        $ctaClicks = UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click');

        // Get top CTAs by clicks (using element_text as the CTA name)
        $topCTAs = UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click')
            ->whereNotNull('element_text')
            ->select('element_text', DB::raw('count(*) as clicks'))
            ->groupBy('element_text')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->element_text,
                    'clicks' => $item->clicks,
                ];
            })
            ->toArray();

        // Get CTAs by type (DB-level aggregation, not in-memory)
        $byType = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click')
            ->selectRaw("COALESCE(JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.cta_type')), 'general') as cta_type, COUNT(*) as cnt")
            ->groupBy('cta_type')
            ->pluck('cnt', 'cta_type')
            ->toArray();

        // Get CTAs by position (DB-level aggregation)
        $byPosition = DB::table('user_events')
            ->where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click')
            ->selectRaw("COALESCE(JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.cta_position')), 'unknown') as cta_position, COUNT(*) as cnt")
            ->groupBy('cta_position')
            ->pluck('cnt', 'cta_position')
            ->toArray();

        // Get CTAs by source page
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

    private function getQuizStats(Carbon $startDate): array
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

    private function getPopupStats(Carbon $startDate): array
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

    private function getLeadMagnetStats(Carbon $startDate): array
    {
        return LeadMagnetDownload::where('lead_magnet_downloads.created_at', '>=', $startDate)
            ->join('lead_magnets', 'lead_magnet_downloads.lead_magnet_id', '=', 'lead_magnets.id')
            ->select('lead_magnets.name', DB::raw('count(*) as count'))
            ->groupBy('lead_magnets.id', 'lead_magnets.name')
            ->pluck('count', 'name')
            ->toArray();
    }

    private function getOutboundStats(Carbon $startDate): array
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

    private function getRecentErrors(): \Illuminate\Support\Collection
    {
        return TrackingError::orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    private function getDailyTrend(Carbon $startDate): array
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
            'pageViews' => array_map(fn($d) => $pageViews[$d] ?? 0, array_keys($sessions)),
        ];
    }
}
