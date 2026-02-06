<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\UserEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PageAnalyticsController extends Controller
{
    public function show(Page $page): JsonResponse
    {
        $slug = $page->slug;

        // Build URL patterns to match (with or without trailing slash)
        $urlPatterns = ["/{$slug}", "/{$slug}/", "/p/{$slug}", "/p/{$slug}/"];

        $stats = DB::table('user_events')
            ->where('event_type', 'page_view')
            ->where(function ($q) use ($urlPatterns) {
                foreach ($urlPatterns as $url) {
                    $q->orWhere('page_url', 'LIKE', '%' . $url);
                }
            })
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT session_id) as unique_visitors')
            ->selectRaw('AVG(time_on_page) as avg_time')
            ->selectRaw('AVG(scroll_depth) as avg_scroll')
            ->first();

        // Views over last 7 days
        $daily = DB::table('user_events')
            ->where('event_type', 'page_view')
            ->where(function ($q) use ($urlPatterns) {
                foreach ($urlPatterns as $url) {
                    $q->orWhere('page_url', 'LIKE', '%' . $url);
                }
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as views')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('views', 'date');

        // Variant stats if this page has variants
        $variantStats = [];
        if ($page->variants()->exists()) {
            $allIds = [$page->id, ...$page->variants()->pluck('id')->toArray()];
            $allPages = Page::whereIn('id', $allIds)->get(['id', 'title', 'slug', 'variant_weight']);

            foreach ($allPages as $p) {
                $pUrls = ["/{$p->slug}", "/{$p->slug}/"];
                $pStats = DB::table('user_events')
                    ->where('event_type', 'page_view')
                    ->where(function ($q) use ($pUrls) {
                        foreach ($pUrls as $url) {
                            $q->orWhere('page_url', 'LIKE', '%' . $url);
                        }
                    })
                    ->selectRaw('COUNT(*) as views, COUNT(DISTINCT session_id) as visitors')
                    ->first();

                $variantStats[] = [
                    'id' => $p->id,
                    'title' => $p->title,
                    'weight' => $p->variant_weight,
                    'views' => $pStats->views ?? 0,
                    'visitors' => $pStats->visitors ?? 0,
                ];
            }
        }

        return response()->json([
            'views' => $stats->views ?? 0,
            'unique_visitors' => $stats->unique_visitors ?? 0,
            'avg_time' => round($stats->avg_time ?? 0),
            'avg_scroll' => round($stats->avg_scroll ?? 0),
            'daily' => $daily,
            'variants' => $variantStats,
        ]);
    }
}
