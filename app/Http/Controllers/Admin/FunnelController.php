<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lander;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Unified PP → Biolinx funnel. PP owns the top (lander views + CTA clicks);
 * Biolinx owns the mid/bottom (site visits → add-to-cart → checkout → orders →
 * revenue), fetched on demand from its read-only /api/bridge/funnel endpoint
 * (shared-secret). Read-only on both sides — no checkout path touched.
 */
class FunnelController extends Controller
{
    private const TEST_MARKERS = [
        'MONCHK', 'MONITORBASE', 'MONITORTEST', 'FINALCHK', 'LAUNCHCHK',
        'REALISTIC', 'DECODETEST', 'NETCHECK', 'E2EFBCLID', 'TESTFBCLID',
    ];

    public function index(Request $request)
    {
        $period = $request->get('period', '30d');
        if (! in_array($period, ['today', '7d', '30d', '90d', 'all'], true)) {
            $period = '30d';
        }
        $start = match ($period) {
            'today' => now('America/New_York')->startOfDay()->utc(),
            '7d'  => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            'all' => null,
            default => now()->subDays(30),
        };

        // ---- PP top of funnel: lander views + CTA clicks to Biolinx ----
        $vq = DB::table('lander_visits');
        if ($start) $vq->where('created_at', '>=', $start);
        foreach (self::TEST_MARKERS as $m) {
            $vq->where(fn ($q) => $q->whereNull('fbclid')->orWhere('fbclid', 'not like', "%{$m}%"));
        }
        $views = (clone $vq)->selectRaw('lander_slug, count(*) c')->groupBy('lander_slug')->pluck('c', 'lander_slug')->toArray();

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

        // ---- Biolinx mid/bottom funnel (on-demand, cached, graceful) ----
        $bx = $this->fetchBiolinx($period);
        $bxLanders = $bx['landers'];

        // ---- Merge per lander ----
        $titles = Lander::pluck('name', 'slug')->toArray();
        $slugs = array_unique(array_merge(array_keys($views), array_keys($clicks), array_keys($bxLanders)));
        $rows = [];
        foreach ($slugs as $slug) {
            $b = $bxLanders[$slug] ?? ['visits' => 0, 'atc' => 0, 'checkout' => 0, 'orders' => 0, 'revenue' => 0];
            $rows[] = [
                'slug' => $slug,
                'title' => $titles[$slug] ?? $slug,
                'views' => (int) ($views[$slug] ?? 0),
                'clicks' => (int) ($clicks[$slug] ?? 0),
                'bx_visits' => (int) ($b['visits'] ?? 0),
                'atc' => (int) ($b['atc'] ?? 0),
                'checkout' => (int) ($b['checkout'] ?? 0),
                'orders' => (int) ($b['orders'] ?? 0),
                'revenue' => (float) ($b['revenue'] ?? 0),
            ];
        }
        usort($rows, fn ($a, $b) => [$b['revenue'], $b['views']] <=> [$a['revenue'], $a['views']]);

        // ---- Overall 7-step funnel with drop-off ----
        $sum = fn ($k) => array_sum(array_column($rows, $k));
        $steps = [
            ['label' => 'Lander views', 'site' => 'PP', 'value' => $sum('views')],
            ['label' => 'CTA clicks → Biolinx', 'site' => 'PP', 'value' => $sum('clicks')],
            ['label' => 'Biolinx visits', 'site' => 'Biolinx', 'value' => $sum('bx_visits')],
            ['label' => 'Add to cart', 'site' => 'Biolinx', 'value' => $sum('atc')],
            ['label' => 'Checkout', 'site' => 'Biolinx', 'value' => $sum('checkout')],
            ['label' => 'Orders', 'site' => 'Biolinx', 'value' => $sum('orders')],
        ];
        $top = $steps[0]['value'] ?: 0;
        foreach ($steps as $i => &$s) {
            $s['pct_of_top'] = $top ? round($s['value'] / $top * 100, 1) : 0;
            $prev = $i > 0 ? $steps[$i - 1]['value'] : null;
            $s['step_conv'] = $prev === null ? null : ($prev ? round($s['value'] / $prev * 100, 1) : 0);
        }
        unset($s);
        $revenue = $sum('revenue');
        $bridgeOk = $bx['ok'];

        return view('admin.funnel.index', compact('rows', 'steps', 'revenue', 'period', 'bridgeOk'));
    }

    /** Fetch Biolinx per-lander mid-funnel; cache 120s on success, fail soft. */
    private function fetchBiolinx(string $period): array
    {
        $key = "biolinx_funnel:{$period}";
        if ($cached = Cache::get($key)) {
            return ['ok' => true, 'landers' => $cached];
        }
        try {
            $res = Http::timeout(8)
                ->withHeaders(['X-PP-Secret' => (string) config('services.pp_conversions.secret')])
                ->get(rtrim((string) config('biolinx.home_url'), '/') . '/api/bridge/funnel', ['period' => $period]);
            if ($res->ok()) {
                $landers = $res->json('landers') ?? [];
                Cache::put($key, $landers, 120);
                return ['ok' => true, 'landers' => $landers];
            }
        } catch (\Throwable $e) {
            // fail soft — show PP-only data + a notice
        }
        return ['ok' => false, 'landers' => []];
    }
}
