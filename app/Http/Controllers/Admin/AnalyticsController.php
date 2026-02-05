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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '7d');
        $startDate = $this->getStartDate($period);

        return view('admin.analytics.index', [
            'period' => $period,
            'overview' => $this->getOverviewStats($startDate),
            'segments' => $this->getSegmentDistribution($startDate),
            'topEvents' => $this->getTopEvents($startDate),
            'ctaStats' => $this->getCTAStats($startDate),
            'quizStats' => $this->getQuizStats($startDate),
            'popupStats' => $this->getPopupStats($startDate),
            'leadMagnetStats' => $this->getLeadMagnetStats($startDate),
            'outboundStats' => $this->getOutboundStats($startDate),
            'recentErrors' => $this->getRecentErrors(),
            'dailyTrend' => $this->getDailyTrend($startDate),
        ]);
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
                ->distinct('ip_address')->count('ip_address'),
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

        // Get CTAs by type
        $byType = UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click')
            ->get()
            ->groupBy(fn($e) => $e->event_data['cta_type'] ?? 'general')
            ->map(fn($g) => $g->count())
            ->toArray();

        // Get CTAs by position
        $byPosition = UserEvent::where('created_at', '>=', $startDate)
            ->where('event_type', 'cta_click')
            ->get()
            ->groupBy(fn($e) => $e->event_data['cta_position'] ?? 'unknown')
            ->map(fn($g) => $g->count())
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
