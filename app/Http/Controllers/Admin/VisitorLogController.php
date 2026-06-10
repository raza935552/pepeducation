<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorEntry;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin "Visitor Log" — a browsable, searchable, exportable reference of every
 * visitor's first touch: the exact link they arrived on, where they came from
 * (referrer), the ad source (utm/fbclid), device, IP, and time.
 */
class VisitorLogController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filtered($request);

        $entries = $query->orderByDesc('id')->paginate(50)->withQueryString();

        // Lazily resolve IP → location for the rows on this page (cached + persisted),
        // so geo only ever happens when an admin views the log — never on a visitor request.
        $this->enrichGeo($entries->getCollection());

        // Lightweight summary for the current filter
        $base = $this->filtered($request);
        $total = (clone $base)->count();
        $ad = (clone $base)->where('is_ad', true)->count();

        return view('admin.visitor-log.index', [
            'entries'  => $entries,
            'total'    => $total,
            'ad'       => $ad,
            'organic'  => $total - $ad,
            'q'        => (string) $request->get('q', ''),
            'source'   => (string) $request->get('source', 'all'),
            'device'   => (string) $request->get('device', 'all'),
            'period'   => (string) $request->get('period', '7d'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->filtered($request)->orderByDesc('id');

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="visitor-log-' . now()->format('Ymd-His') . '.csv"',
        ];

        return response()->stream(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['when', 'landing_url', 'referrer', 'referrer_domain', 'is_ad', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'fbclid', 'device', 'ip', 'city', 'region', 'country']);
            $query->chunk(500, function ($rows) use ($out) {
                $this->enrichGeo($rows);
                foreach ($rows as $r) {
                    fputcsv($out, [
                        optional($r->created_at)->toDateTimeString(),
                        $r->landing_url, $r->referrer, $r->referrer_domain,
                        $r->is_ad ? 'ad' : 'organic',
                        $r->utm_source, $r->utm_medium, $r->utm_campaign, $r->utm_content, $r->utm_term,
                        $r->fbclid, $r->device, $r->ip,
                        $r->city, $r->region, $r->country,
                    ]);
                }
            });
            fclose($out);
        }, 200, $headers);
    }

    /**
     * Resolve IP → location for a collection of rows that haven't been resolved yet,
     * persist it, and set it on the in-memory models for display. Cached per IP, so
     * repeat IPs cost nothing. Best-effort — never throws.
     */
    private function enrichGeo($rows): void
    {
        $pending = collect($rows)->filter(fn ($r) => ! $r->geo_resolved && $r->ip);
        if ($pending->isEmpty()) {
            return;
        }

        $geo = \App\Support\IpGeo::resolveMany($pending->pluck('ip')->all());

        foreach ($pending as $r) {
            $g = $geo[$r->ip] ?? null;
            // Mark resolved when we got a result, or the IP can never resolve (private/invalid).
            $isResolvable = filter_var($r->ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
            if ($g === null && $isResolvable) {
                continue; // transient failure — retry on next view
            }
            $g = $g ?: \App\Support\IpGeo::empty();
            \App\Models\VisitorEntry::where('id', $r->id)->update([
                'country'      => $g['country'],
                'country_code' => $g['country_code'],
                'region'       => $g['region'],
                'city'         => $g['city'],
                'geo_resolved' => true,
            ]);
            $r->country = $g['country'];
            $r->country_code = $g['country_code'];
            $r->region = $g['region'];
            $r->city = $g['city'];
            $r->geo_resolved = true;
        }
    }

    /** Shared filter builder for index + export. */
    private function filtered(Request $request)
    {
        $query = VisitorEntry::query();

        if ($period = $request->get('period', '7d')) {
            $start = match ($period) {
                '1h'  => now()->subHour(),
                '24h' => now()->subDay(),
                '7d'  => now()->subDays(7),
                '30d' => now()->subDays(30),
                'all' => null,
                default => now()->subDays(7),
            };
            if ($start) {
                $query->where('created_at', '>=', $start);
            }
        }

        $source = $request->get('source', 'all');
        if ($source === 'ad') {
            $query->where('is_ad', true);
        } elseif ($source === 'organic') {
            $query->where('is_ad', false);
        }

        if (($device = $request->get('device', 'all')) && $device !== 'all') {
            $query->where('device', $device);
        }

        if ($q = trim((string) $request->get('q', ''))) {
            $query->where(function ($w) use ($q) {
                $w->where('landing_url', 'like', "%{$q}%")
                    ->orWhere('referrer', 'like', "%{$q}%")
                    ->orWhere('referrer_domain', 'like', "%{$q}%")
                    ->orWhere('utm_campaign', 'like', "%{$q}%")
                    ->orWhere('utm_source', 'like', "%{$q}%")
                    ->orWhere('ip', 'like', "%{$q}%");
            });
        }

        return $query;
    }
}
