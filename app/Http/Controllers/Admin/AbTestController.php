<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lander;
use App\Models\LanderVisit;
use App\Models\OutboundClick;
use App\Models\OutboundLink;
use App\Models\Subscriber;
use Illuminate\Http\Request;

/**
 * A/B test scoreboard: control "A" (user-built) vs "B" (AI-built) for the two
 * paid-ad landers. Reads the variant tags written on lander_visits + outbound_clicks
 * + giveaway subscriber source, and reports visits, CTR-to-Biolinx, and email-capture
 * rate per variant, with a two-proportion significance check on CTR (the primary metric).
 */
class AbTestController extends Controller
{
    public function index(Request $request)
    {
        $days  = max(1, (int) $request->query('days', 7));
        $start = now()->subDays($days);

        // Every active lander that has a B template (a test is possible) — not a
        // hardcoded pair. The outbound-link slug follows the lp-{slug} convention;
        // each row's `enabled` flag shows which are actually split 50/50 right now.
        $tests = Lander::where('is_active', true)->orderBy('slug')->get()
            ->filter(fn ($l) => view()->exists("landers.templates.{$l->template}-b"))
            ->mapWithKeys(fn ($l) => [$l->slug => 'lp-' . $l->slug]);

        $rows = [];
        foreach ($tests as $slug => $outSlug) {
            $lander = Lander::where('slug', $slug)->first();
            $linkId = OutboundLink::where('slug', $outSlug)->value('id');

            $variants = [];
            foreach (['A', 'B'] as $v) {
                $visits = LanderVisit::where('lander_slug', $slug)->where('variant', $v)
                    ->where('created_at', '>=', $start)->count();
                $clicks = $linkId
                    ? OutboundClick::where('variant', $v)->where('outbound_link_id', $linkId)
                        ->where('created_at', '>=', $start)->count()
                    : 0;
                $emails = Subscriber::where('source', 'giveaway:' . $slug . ':' . $v)
                    ->where('created_at', '>=', $start)->count();

                $variants[$v] = [
                    'visits'     => $visits,
                    'clicks'     => $clicks,
                    'emails'     => $emails,
                    'ctr'        => $visits > 0 ? $clicks / $visits : 0.0,
                    'email_rate' => $visits > 0 ? $emails / $visits : 0.0,
                ];
            }

            $rows[] = [
                'slug'      => $slug,
                'name'      => $lander?->name ?: $slug,
                'enabled'   => (bool) ($lander && $lander->c('ab_test.enabled')),
                'has_b'     => $lander ? view()->exists("landers.templates.{$lander->template}-b") : false,
                'variants'  => $variants,
                'ctr_test'  => $this->twoProportion(
                    $variants['A']['clicks'], $variants['A']['visits'],
                    $variants['B']['clicks'], $variants['B']['visits']
                ),
                'total_visits' => $variants['A']['visits'] + $variants['B']['visits'],
            ];
        }

        return view('admin.ab-test.index', compact('rows', 'days'));
    }

    /**
     * Two-proportion z-test on CTR (B vs A). Returns the leader, |z|, a plain-English
     * confidence band, and whether the minimum sample is met. Deliberately conservative.
     */
    private function twoProportion(int $cA, int $nA, int $cB, int $nB): array
    {
        $minPerArm = 100; // don't call a winner before each arm has a fair sample
        if ($nA === 0 || $nB === 0) {
            return ['verdict' => 'No data yet', 'leader' => null, 'z' => 0.0, 'confidence' => '—', 'enough' => false];
        }

        $pA = $cA / $nA;
        $pB = $cB / $nB;
        $pPool = ($cA + $cB) / ($nA + $nB);
        $se = sqrt(max(1e-9, $pPool * (1 - $pPool) * (1 / $nA + 1 / $nB)));
        $z = $se > 0 ? ($pB - $pA) / $se : 0.0;
        $absZ = abs($z);

        $confidence = $absZ >= 2.58 ? '99%+' : ($absZ >= 1.96 ? '95%' : ($absZ >= 1.64 ? '90%' : '<90% (not significant)'));
        $leader = $pB > $pA ? 'B' : ($pA > $pB ? 'A' : null);
        $enough = ($nA >= $minPerArm && $nB >= $minPerArm);

        $verdict = ! $enough
            ? 'Collecting data (need ~' . $minPerArm . '/arm)'
            : ($absZ >= 1.96
                ? ('Variant ' . $leader . ' winning (' . $confidence . ')')
                : 'Too close to call');

        return compact('verdict', 'leader', 'z', 'confidence', 'enough');
    }
}
