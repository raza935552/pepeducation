<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanderConversion;
use App\Models\LanderVisit;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin "Ad Analytics" — accurate paid-traffic reporting for the bridge landers.
 *
 * VISITS  : lander_visits (is_ad = carried fbclid / ad UTM), logged on every
 *           lander load by LanderController::recordVisit().
 * CLICKS  : outbound_clicks — the CTA hand-off to Biolinx. The real ad UTMs +
 *           fbclid live inside final_url (the dedicated columns hold the link's
 *           static UTM), so we ad-filter on final_url LIKE %fbclid=% and parse it.
 * CTR     : ad clicks / ad visits, per lander / campaign / ad.
 * SALES   : tracked on the Biolinx side (order Attribution panel, pp_lander) —
 *           PP cannot see the store DB, so revenue is intentionally out of scope here.
 */
class AdAnalyticsController extends Controller
{
    /** Markers from internal verification/monitor traffic — excluded from reports. */
    private const TEST_MARKERS = [
        'MONCHK', 'MONITORBASE', 'MONITORTEST', 'FINALCHK', 'LAUNCHCHK',
        'REALISTIC', 'DECODETEST', 'NETCHECK', 'E2EFBCLID', 'TESTFBCLID',
    ];

    public function index(Request $request)
    {
        $period = $request->get('period', '24h');
        $start = match ($period) {
            '1h'  => now()->subHour(),
            '24h' => now()->subDay(),
            '7d'  => now()->subDays(7),
            '30d' => now()->subDays(30),
            'all' => null,
            default => now()->subDay(),
        };

        // ---------- VISITS (lander_visits, ad only) ----------
        $visitQ = LanderVisit::query()->where('is_ad', true);
        if ($start) {
            $visitQ->where('created_at', '>=', $start);
        }
        foreach (self::TEST_MARKERS as $m) {
            $visitQ->where(function ($q) use ($m) {
                $q->whereNull('fbclid')->orWhere('fbclid', 'not like', "%{$m}%");
            });
        }

        $visitsByLander   = (clone $visitQ)->selectRaw('lander_slug, count(*) c')->groupBy('lander_slug')->pluck('c', 'lander_slug')->toArray();
        $visitsByCampaign = (clone $visitQ)->selectRaw("COALESCE(NULLIF(utm_campaign,''),'(no campaign)') k, count(*) c")->groupBy('k')->pluck('c', 'k')->toArray();
        $visitsByAd       = (clone $visitQ)->selectRaw("COALESCE(NULLIF(utm_content,''),'(no ad name)') k, count(*) c")->groupBy('k')->pluck('c', 'k')->toArray();
        // Nested campaign → ad visits, for the drilldown.
        $visitsByCampaignAd = [];
        foreach ((clone $visitQ)->selectRaw("COALESCE(NULLIF(utm_campaign,''),'(no campaign)') camp, COALESCE(NULLIF(utm_content,''),'(no ad name)') ad, count(*) c")->groupBy('camp', 'ad')->get() as $row) {
            $visitsByCampaignAd[$row->camp][$row->ad] = (int) $row->c;
        }
        $totalVisits      = (clone $visitQ)->count();
        $uniqueVisitors   = (clone $visitQ)->distinct()->count('session_id');

        // ---------- CLICKS (outbound_clicks → Biolinx, ad only) ----------
        // Visit logging is newer than outbound_clicks, so floor clicks at when the
        // FIRST lander_visit was recorded — otherwise pre-tracking historical clicks
        // would be compared against zero/low visits and distort CTR (>100% or null).
        $trackingStart = LanderVisit::min('created_at');
        $clickFloor = $trackingStart ? \Illuminate\Support\Carbon::parse($trackingStart) : now();
        $clickLowerBound = ($start && $start->gt($clickFloor)) ? $start : $clickFloor;

        $clickQ = DB::table('outbound_clicks as c')
            ->join('outbound_links as l', 'l.id', '=', 'c.outbound_link_id')
            ->where('c.final_url', 'like', '%fbclid=%')
            ->where('c.created_at', '>=', $clickLowerBound);
        foreach (self::TEST_MARKERS as $m) {
            $clickQ->where('c.final_url', 'not like', "%{$m}%");
        }
        $clickRows = $clickQ->get(['l.slug as link_slug', 'c.final_url']);

        $clicksByLander = $clicksByCampaign = $clicksByAd = $clicksByCampaignAd = [];
        $totalClicks = 0;
        foreach ($clickRows as $r) {
            $totalClicks++;
            $lander = preg_replace('/^lp-/', '', (string) $r->link_slug);
            parse_str((string) parse_url($r->final_url, PHP_URL_QUERY), $q);
            $camp = ($q['utm_campaign'] ?? '') !== '' ? $q['utm_campaign'] : '(no campaign)';
            $ad   = ($q['utm_content'] ?? '') !== '' ? $q['utm_content'] : '(no ad name)';
            $clicksByLander[$lander]   = ($clicksByLander[$lander] ?? 0) + 1;
            $clicksByCampaign[$camp]   = ($clicksByCampaign[$camp] ?? 0) + 1;
            $clicksByAd[$ad]           = ($clicksByAd[$ad] ?? 0) + 1;
            $clicksByCampaignAd[$camp][$ad] = ($clicksByCampaignAd[$camp][$ad] ?? 0) + 1;
        }

        // ---------- ALL CLICKS (every CTA hand-off, not just ad/fbclid) ----------
        // So the dashboard is not confusingly 0 during the learning phase. Same floor +
        // test-marker exclusion as ad-clicks, just WITHOUT the fbclid requirement.
        $allClickQ = DB::table('outbound_clicks as c')
            ->join('outbound_links as l', 'l.id', '=', 'c.outbound_link_id')
            ->where('c.created_at', '>=', $clickLowerBound);
        foreach (self::TEST_MARKERS as $m) {
            $allClickQ->where('c.final_url', 'not like', "%{$m}%");
        }
        $clicksAllByLander = $clicksAllByCampaign = $clicksAllByAd = $clicksAllByCampaignAd = [];
        $totalClicksAll = 0;
        foreach ($allClickQ->get(['l.slug as link_slug', 'c.final_url']) as $r) {
            $totalClicksAll++;
            $lander = preg_replace('/^lp-/', '', (string) $r->link_slug);
            parse_str((string) parse_url($r->final_url, PHP_URL_QUERY), $q);
            $camp = ($q['utm_campaign'] ?? '') !== '' ? $q['utm_campaign'] : '(no campaign)';
            $ad   = ($q['utm_content'] ?? '') !== '' ? $q['utm_content'] : '(no ad name)';
            $clicksAllByLander[$lander]   = ($clicksAllByLander[$lander] ?? 0) + 1;
            $clicksAllByCampaign[$camp]   = ($clicksAllByCampaign[$camp] ?? 0) + 1;
            $clicksAllByAd[$ad]           = ($clicksAllByAd[$ad] ?? 0) + 1;
            $clicksAllByCampaignAd[$camp][$ad] = ($clicksAllByCampaignAd[$camp][$ad] ?? 0) + 1;
        }

        // ---------- CONVERSIONS (orders + revenue from Biolinx) ----------
        // Mirrored from Biolinx by the pp:push-conversions bridge. Filtered by the
        // SELECTED period (not the visit-tracking floor) so real revenue shows even
        // for orders that predate visit logging. (CVR vs clicks is only fully
        // apples-to-apples for periods within the tracking window — see note in UI.)
        $convQ = LanderConversion::query();
        if ($start) {
            $convQ->where('ordered_at', '>=', $start);
        }
        $ordersByLander  = (clone $convQ)->whereNotNull('pp_lander')->selectRaw('pp_lander k, count(*) c, sum(revenue) r')->groupBy('k')->get()->keyBy('k');
        $ordersByCampaign = (clone $convQ)->selectRaw("COALESCE(NULLIF(utm_campaign,''),'(no campaign)') k, count(*) c, sum(revenue) r")->groupBy('k')->get()->keyBy('k');
        $ordersByAd       = (clone $convQ)->selectRaw("COALESCE(NULLIF(utm_content,''),'(no ad name)') k, count(*) c, sum(revenue) r")->groupBy('k')->get()->keyBy('k');
        // Nested campaign → ad orders/revenue, for the drilldown.
        $ordersByCampaignAd = [];
        foreach ((clone $convQ)->selectRaw("COALESCE(NULLIF(utm_campaign,''),'(no campaign)') camp, COALESCE(NULLIF(utm_content,''),'(no ad name)') ad, count(*) c, sum(revenue) r")->groupBy('camp', 'ad')->get() as $row) {
            $ordersByCampaignAd[$row->camp][$row->ad] = ['c' => (int) $row->c, 'r' => (float) $row->r];
        }
        $totalOrders   = (clone $convQ)->count();
        $totalRevenue  = (float) (clone $convQ)->sum('revenue');

        // ---------- EMAIL CAPTURES ----------
        // Two figures:
        //  - totalAdEmails : PRECISE — subscribers stamped is_ad at capture (the session
        //    carried a Meta fbclid / paid utm). Accurate from the attribution deploy onward.
        //  - totalOptins   : VOLUME — all lander giveaway opt-ins (giveaway:{slug}:{variant}),
        //    ad or organic. Kept as context + the per-lander breakdown (source encodes slug).
        $totalAdEmails = Subscriber::query()
            ->where('is_ad', true)
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->count();

        $emailRows = Subscriber::query()
            ->where('source', 'like', 'giveaway:%')
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->selectRaw('source, count(*) c')
            ->groupBy('source')
            ->get();
        $emailsByLander = [];
        $totalOptins = 0;
        foreach ($emailRows as $r) {
            $slug = explode(':', (string) $r->source)[1] ?? '(unknown)'; // giveaway:{slug}:{variant}
            $emailsByLander[$slug] = ($emailsByLander[$slug] ?? 0) + (int) $r->c;
            $totalOptins += (int) $r->c;
        }

        // ---------- Merge into report rows (visits + clicks + orders + revenue) ----------
        $perLander   = $this->mergeRows($visitsByLander, $clicksByLander, $ordersByLander, $clicksAllByLander, $emailsByLander);
        $perCampaign = $this->mergeRows($visitsByCampaign, $clicksByCampaign, $ordersByCampaign, $clicksAllByCampaign);
        $perAd       = $this->mergeRows($visitsByAd, $clicksByAd, $ordersByAd, $clicksAllByAd);

        // Campaign → Ad drilldown: specific ads (utm_content) nested under each campaign.
        $drilldown   = $this->buildDrilldown($visitsByCampaignAd, $clicksByCampaignAd, $clicksAllByCampaignAd, $ordersByCampaignAd);

        // ---------- Recent ad activity ----------
        $recent = (clone $visitQ)->orderByDesc('id')->limit(25)
            ->get(['lander_slug', 'utm_campaign', 'utm_content', 'utm_source', 'created_at']);

        $overallCtr = $totalVisits > 0 ? round($totalClicks / $totalVisits * 100, 1) : 0.0;
        $overallCvr = $totalClicks > 0 ? round($totalOrders / $totalClicks * 100, 1) : 0.0;
        $aov        = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0.0;
        $hasRevenue = LanderConversion::exists();

        return view('admin.ad-analytics.index', [
            'period'         => $period,
            'totalVisits'    => $totalVisits,
            'uniqueVisitors' => $uniqueVisitors,
            'totalClicks'    => $totalClicks,
            'totalClicksAll' => $totalClicksAll,
            'overallCtr'     => $overallCtr,
            'totalOrders'    => $totalOrders,
            'totalRevenue'   => $totalRevenue,
            'totalAdEmails'  => $totalAdEmails,
            'totalOptins'    => $totalOptins,
            'overallCvr'     => $overallCvr,
            'aov'            => $aov,
            'hasRevenue'     => $hasRevenue,
            'perLander'      => $perLander,
            'perCampaign'    => $perCampaign,
            'perAd'          => $perAd,
            'drilldown'      => $drilldown,
            'recent'         => $recent,
        ]);
    }

    /**
     * Build the campaign → ad drilldown: each campaign with its total visits/clicks/
     * orders/revenue, plus the specific ads (utm_content) inside it. Inputs are nested
     * maps keyed [campaign][ad]. Sorted by revenue then visits, at both levels.
     */
    private function buildDrilldown(array $visitsCA, array $clicksCA, array $clicksAllCA, array $ordersCA): array
    {
        $campaigns = array_unique(array_merge(
            array_keys($visitsCA), array_keys($clicksCA), array_keys($clicksAllCA), array_keys($ordersCA)
        ));

        $out = [];
        foreach ($campaigns as $camp) {
            $v  = $visitsCA[$camp] ?? [];
            $c  = $clicksCA[$camp] ?? [];
            $ca = $clicksAllCA[$camp] ?? [];
            $o  = $ordersCA[$camp] ?? [];
            $adKeys = array_unique(array_merge(array_keys($v), array_keys($c), array_keys($ca), array_keys($o)));

            $ads = [];
            $tv = $tc = $tca = $to = 0;
            $tr = 0.0;
            foreach ($adKeys as $ad) {
                $vv  = $v[$ad] ?? 0;
                $cc  = $c[$ad] ?? 0;
                $caa = $ca[$ad] ?? 0;
                $oo  = isset($o[$ad]) ? $o[$ad]['c'] : 0;
                $rr  = isset($o[$ad]) ? $o[$ad]['r'] : 0.0;
                $ads[] = [
                    'key'        => $ad,
                    'visits'     => $vv,
                    'clicks'     => $cc,
                    'clicks_all' => $caa,
                    'ctr'        => $vv > 0 ? round($cc / $vv * 100, 1) : ($cc > 0 ? null : 0.0),
                    'orders'     => $oo,
                    'revenue'    => $rr,
                    'cvr'        => $cc > 0 ? round($oo / $cc * 100, 1) : ($oo > 0 ? null : 0.0),
                ];
                $tv += $vv; $tc += $cc; $tca += $caa; $to += $oo; $tr += $rr;
            }
            usort($ads, fn ($a, $b) => ($b['revenue'] <=> $a['revenue']) ?: ($b['visits'] <=> $a['visits']));

            $out[] = [
                'campaign'   => $camp,
                'visits'     => $tv,
                'clicks'     => $tc,
                'clicks_all' => $tca,
                'ctr'        => $tv > 0 ? round($tc / $tv * 100, 1) : ($tc > 0 ? null : 0.0),
                'orders'     => $to,
                'revenue'    => $tr,
                'cvr'        => $tc > 0 ? round($to / $tc * 100, 1) : ($to > 0 ? null : 0.0),
                'ads'        => $ads,
            ];
        }

        usort($out, fn ($a, $b) => ($b['revenue'] <=> $a['revenue']) ?: ($b['visits'] <=> $a['visits']));
        return $out;
    }

    /**
     * Combine visit + click + conversion maps into sorted rows.
     * $orders is keyed map of objects with ->c (count) and ->r (revenue sum).
     */
    private function mergeRows(array $visits, array $clicks, $orders = null, array $clicksAll = [], array $emails = []): array
    {
        $orderKeys = $orders ? array_keys($orders->toArray()) : [];
        $keys = array_unique(array_merge(array_keys($visits), array_keys($clicks), array_keys($clicksAll), $orderKeys, array_keys($emails)));
        $rows = [];
        foreach ($keys as $k) {
            $v = $visits[$k] ?? 0;
            $c = $clicks[$k] ?? 0;
            $ca = $clicksAll[$k] ?? 0;
            $em = $emails[$k] ?? 0;
            $o = ($orders && isset($orders[$k])) ? (int) $orders[$k]->c : 0;
            $rev = ($orders && isset($orders[$k])) ? (float) $orders[$k]->r : 0.0;
            $rows[] = [
                'key'        => $k,
                'visits'     => $v,
                'clicks'     => $c,
                'clicks_all' => $ca,
                'emails'     => $em,
                'ctr'     => $v > 0 ? round($c / $v * 100, 1) : ($c > 0 ? null : 0.0),
                'orders'  => $o,
                'revenue' => $rev,
                'cvr'     => $c > 0 ? round($o / $c * 100, 1) : ($o > 0 ? null : 0.0),
            ];
        }
        // Sort by revenue first (money matters most), then visits.
        usort($rows, fn ($a, $b) => ($b['revenue'] <=> $a['revenue']) ?: ($b['visits'] <=> $a['visits']));
        return $rows;
    }
}
