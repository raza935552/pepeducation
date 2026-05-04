<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Peptide;
use App\Models\Subscriber;
use App\Services\CustomerIo\CustomerIoService;
use Illuminate\Http\Request;

class PeptideController extends Controller
{
    public function index(Request $request)
    {
        $query = Peptide::with('categories')->published();

        // Search
        $rawSearch = $request->get('search');
        if ($rawSearch) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $rawSearch);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('overview', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($category = $request->get('category')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $category));
        }

        // Filter by research status
        if ($research = $request->get('research')) {
            $query->where('research_status', $research);
        }

        $peptides = $query->orderBy('name')->paginate(12);
        $categories = Category::withCount(['peptides' => fn($q) => $q->published()])
            ->having('peptides_count', '>', 0)
            ->orderBy('name')
            ->get();

        // Log search query for analytics (only if user actually searched)
        if ($rawSearch && trim($rawSearch) !== '') {
            try {
                \Illuminate\Support\Facades\DB::table('search_logs')->insert([
                    'query' => mb_substr(trim($rawSearch), 0, 200),
                    'source' => 'peptides_index',
                    'result_count' => $peptides->total(),
                    'ip_hash' => hash('sha256', $request->ip().config('app.key')),
                    'user_agent_short' => mb_substr($request->userAgent() ?? '', 0, 60),
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Silent fail - analytics shouldn't break user search
            }
        }

        return view('peptides.index', compact('peptides', 'categories'));
    }

    public function show(Peptide $peptide)
    {
        if (!$peptide->is_published) {
            abort(404);
        }

        $peptide->load('categories');

        $relatedPeptides = Peptide::published()
            ->with('categories')
            ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $peptide->categories->pluck('id')))
            ->where('peptides.id', '!=', $peptide->id)
            ->limit(4)
            ->get();

        $relatedPosts = \App\Models\BlogPost::published()
            ->whereHas('peptides', fn($q) => $q->where('peptides.id', $peptide->id))
            ->latest('published_at')
            ->limit(4)
            ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'reading_time', 'published_at']);

        // Track viewed product to Customer.io (if subscriber identified)
        $this->trackViewedProduct($peptide);

        // Save last-viewed peptide slug as a cookie for the 404 page personalization
        cookie()->queue(cookie('pp_last_peptide', $peptide->slug, 60 * 24 * 30, '/'));

        return view('peptides.show', compact('peptide', 'relatedPeptides', 'relatedPosts'));
    }

    protected function trackViewedProduct(Peptide $peptide): void
    {
        $email = request()->cookie('pp_email');
        if (!$email) return;

        $subscriber = Subscriber::where('email', $email)->first();
        if (!$subscriber) return;

        try {
            app(EventService::class)->track($subscriber, 'Viewed Product', [
                'ProductName' => $peptide->name,
                'ProductID' => $peptide->slug,
                'Categories' => $peptide->categories->pluck('name')->toArray(),
                'URL' => route('peptides.show', $peptide),
            ]);
        } catch (\Exception $e) {
            // Silent fail — don't break page load
        }
    }
}
