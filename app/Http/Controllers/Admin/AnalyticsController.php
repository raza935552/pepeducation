<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analytics) {}

    public function index(Request $request)
    {
        $period = $request->get('period', '7d');
        $startDate = $this->analytics->getStartDate($period);
        $ttl = $this->analytics->cacheTtl($period);
        $c = fn (string $k, callable $cb) => Cache::remember("analytics:{$period}:{$k}", $ttl, $cb);

        return view('admin.analytics.index', [
            'period' => $period,
            // Overview
            'overview'     => $c('overview', fn () => $this->analytics->getOverviewKPIs($startDate)),
            'prevOverview' => $c('prevOverview', fn () => $this->analytics->getPreviousPeriodKPIs($startDate, $period)),
            'segments'     => $c('segments', fn () => $this->analytics->getSegmentDistribution($startDate)),
            'topEvents'    => $c('topEvents', fn () => $this->analytics->getTopEvents($startDate)),
            'dailyTrend'   => $c('trend', fn () => $this->analytics->getDailyTrend($startDate)),
            // Traffic
            'trafficSources'  => $c('sources', fn () => $this->analytics->getTrafficSources($startDate)),
            'utmCampaigns'    => $c('utm', fn () => $this->analytics->getUtmCampaigns($startDate)),
            'deviceBreakdown' => $c('devices', fn () => $this->analytics->getDeviceBreakdown($startDate)),
            'browserBreakdown'=> $c('browsers', fn () => $this->analytics->getBrowserBreakdown($startDate)),
            'osBreakdown'     => $c('os', fn () => $this->analytics->getOsBreakdown($startDate)),
            'newVsReturning'  => $c('nvr', fn () => $this->analytics->getNewVsReturning($startDate)),
            'topLandingPages' => $c('landing', fn () => $this->analytics->getTopLandingPages($startDate)),
            // Content
            'topPages'        => $c('topPages', fn () => $this->analytics->getTopPages($startDate)),
            'blogPerformance' => $c('blogPerf', fn () => $this->analytics->getBlogPerformance($startDate)),
            'scrollDepth'     => $c('scroll', fn () => $this->analytics->getScrollDepthDistribution($startDate)),
            'avgTimeOnPage'   => $c('timeOnPage', fn () => $this->analytics->getAvgTimeOnPage($startDate)),
            // Funnels
            'ctaStats'        => $c('cta', fn () => $this->analytics->getCTAStats($startDate)),
            'quizStats'       => $c('quiz', fn () => $this->analytics->getQuizStats($startDate)),
            'popupStats'      => $c('popup', fn () => $this->analytics->getPopupStats($startDate)),
            'leadMagnetStats' => $c('leadMag', fn () => $this->analytics->getLeadMagnetStats($startDate)),
            'outboundStats'   => $c('outbound', fn () => $this->analytics->getOutboundStats($startDate)),
            'conversionFunnel'=> $c('funnel', fn () => $this->analytics->getConversionFunnel($startDate)),
            // Engagement
            'engagementDist'      => $c('engDist', fn () => $this->analytics->getEngagementDistribution($startDate)),
            'engagementTierTrend' => $c('engTier', fn () => $this->analytics->getEngagementTierTrend($startDate)),
            'rageClicks'          => $c('rage', fn () => $this->analytics->getRageClickHotspots($startDate)),
            'topEngagedPages'     => $c('topEng', fn () => $this->analytics->getTopEngagedPages($startDate)),
            'bounceRate'          => $c('bounce', fn () => $this->analytics->getBounceRate($startDate)),
            // Errors
            'recentErrors' => $c('errors', fn () => $this->analytics->getRecentErrors()),
        ]);
    }
}
