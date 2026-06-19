<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lander;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Content → Revenue: which PP landers actually drive downstream Biolinx revenue
 * (not just clicks). Joins lander views (lander_visits) + CTA hand-offs to Biolinx
 * (outbound_clicks) + mirrored orders/revenue (lander_conversions) per lander, and
 * surfaces revenue-per-view so you can double down on what sells — across ALL
 * traffic (organic + ad), unlike the ad-only Ad Analytics page.
 */
class ContentRevenueController extends Controller
{
    private const TEST_MARKERS = [
        'MONCHK', 'MONITORBASE', 'MONITORTEST', 'FINALCHK', 'LAUNCHCHK',
        'REALISTIC', 'DECODETEST', 'NETCHECK', 'E2EFBCLID', 'TESTFBCLID',
    ];

    public function index(Request $request)
    {
        $period = $request->get('period', '30d');
        $start = match ($period) {
            'today' => now('America/New_York')->startOfDay()->utc(),
            '7d'  => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            'all' => null,
            default => now()->subDays(30),
        };

        // ---- Views per lander (all traffic; test fbclids excluded) ----
        $vq = DB::table('lander_visits');
        if ($start) $vq->where('created_at', '>=', $start);
        foreach (self::TEST_MARKERS as $m) {
            $vq->where(fn ($q) => $q->whereNull('fbclid')->orWhere('fbclid', 'not like', "%{$m}%"));
        }
        $views   = (clone $vq)->selectRaw('lander_slug, count(*) c')->groupBy('lander_slug')->pluck('c', 'lander_slug')->toArray();
        $adViews = (clone $vq)->where('is_ad', true)->selectRaw('lander_slug, count(*) c')->groupBy('lander_slug')->pluck('c', 'lander_slug')->toArray();

        // ---- CTA clicks to Biolinx per lander (slug = strip 'lp-' from link slug) ----
        $cq = DB::table('outbound_clicks as c')->join('outbound_links as l', 'l.id', '=', 'c.outbound_link_id');
        if ($start) $cq->where('c.created_at', '>=', $start);
        foreach (self::TEST_MARKERS as $m) {
            $cq->where('c.final_url', 'not like', "%{$m}%");
        }
        $clicks = [];
        foreach ($cq->get(['l.slug as s']) as $r) {
            $slug = preg_replace('/^lp-/', '', (string) $r->s);
            $clicks[$slug] = ($clicks[$slug] ?? 0) + 1;
        }

        // ---- Orders + revenue per lander (mirrored from Biolinx) ----
        $oq = DB::table('lander_conversions')->whereNotNull('pp_lander');
        if ($start) $oq->where('ordered_at', '>=', $start);
        $conv = $oq->selectRaw('pp_lander, count(*) o, sum(revenue) r')->groupBy('pp_lander')->get()->keyBy('pp_lander');

        // ---- Lander titles ----
        $titles = Lander::pluck('name', 'slug')->toArray();

        // ---- Merge ----
        $slugs = array_unique(array_merge(array_keys($views), array_keys($clicks), $conv->keys()->all()));
        $rows = [];
        foreach ($slugs as $slug) {
            $v = (int) ($views[$slug] ?? 0);
            $cl = (int) ($clicks[$slug] ?? 0);
            $o = $conv->has($slug) ? (int) $conv[$slug]->o : 0;
            $rev = $conv->has($slug) ? (float) $conv[$slug]->r : 0.0;
            $rows[] = [
                'slug' => $slug,
                'title' => $titles[$slug] ?? $slug,
                'views' => $v,
                'ad_views' => (int) ($adViews[$slug] ?? 0),
                'clicks' => $cl,
                'orders' => $o,
                'revenue' => $rev,
                'ctr' => $v ? round($cl / $v * 100, 1) : 0,
                'order_rate' => $cl ? round($o / $cl * 100, 1) : 0,
                'rev_per_view' => $v ? round($rev / $v, 2) : 0,
            ];
        }
        usort($rows, fn ($a, $b) => [$b['revenue'], $b['views']] <=> [$a['revenue'], $a['views']]);

        $totals = [
            'views' => array_sum(array_column($rows, 'views')),
            'clicks' => array_sum(array_column($rows, 'clicks')),
            'orders' => array_sum(array_column($rows, 'orders')),
            'revenue' => array_sum(array_column($rows, 'revenue')),
        ];

        return view('admin.content-revenue.index', compact('rows', 'totals', 'period'));
    }
}
