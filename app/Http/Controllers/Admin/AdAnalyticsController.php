<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanderVisit;
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

        $clicksByLander = $clicksByCampaign = $clicksByAd = [];
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
        }

        // ---------- Merge into report rows (visits + clicks + CTR) ----------
        $perLander   = $this->mergeRows($visitsByLander, $clicksByLander);
        $perCampaign = $this->mergeRows($visitsByCampaign, $clicksByCampaign);
        $perAd       = $this->mergeRows($visitsByAd, $clicksByAd);

        // ---------- Recent ad activity ----------
        $recent = (clone $visitQ)->orderByDesc('id')->limit(25)
            ->get(['lander_slug', 'utm_campaign', 'utm_content', 'utm_source', 'created_at']);

        $overallCtr = $totalVisits > 0 ? round($totalClicks / $totalVisits * 100, 1) : 0.0;

        return view('admin.ad-analytics.index', [
            'period'         => $period,
            'totalVisits'    => $totalVisits,
            'uniqueVisitors' => $uniqueVisitors,
            'totalClicks'    => $totalClicks,
            'overallCtr'     => $overallCtr,
            'perLander'      => $perLander,
            'perCampaign'    => $perCampaign,
            'perAd'          => $perAd,
            'recent'         => $recent,
        ]);
    }

    /** Combine visit + click maps into sorted rows with CTR. */
    private function mergeRows(array $visits, array $clicks): array
    {
        $keys = array_unique(array_merge(array_keys($visits), array_keys($clicks)));
        $rows = [];
        foreach ($keys as $k) {
            $v = $visits[$k] ?? 0;
            $c = $clicks[$k] ?? 0;
            $rows[] = [
                'key'    => $k,
                'visits' => $v,
                'clicks' => $c,
                'ctr'    => $v > 0 ? round($c / $v * 100, 1) : ($c > 0 ? null : 0.0),
            ];
        }
        usort($rows, fn ($a, $b) => ($b['visits'] <=> $a['visits']) ?: ($b['clicks'] <=> $a['clicks']));
        return $rows;
    }
}
