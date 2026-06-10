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
            fputcsv($out, ['when', 'landing_url', 'referrer', 'referrer_domain', 'is_ad', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'fbclid', 'device', 'ip']);
            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [
                        optional($r->created_at)->toDateTimeString(),
                        $r->landing_url, $r->referrer, $r->referrer_domain,
                        $r->is_ad ? 'ad' : 'organic',
                        $r->utm_source, $r->utm_medium, $r->utm_campaign, $r->utm_content, $r->utm_term,
                        $r->fbclid, $r->device, $r->ip,
                    ]);
                }
            });
            fclose($out);
        }, 200, $headers);
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
