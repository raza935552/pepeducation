<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Peptide;
use App\Models\Subscriber;
use App\Services\Klaviyo\EventService;
use Illuminate\Http\Request;

class PeptideController extends Controller
{
    public function index(Request $request)
    {
        $query = Peptide::with('categories')->published();

        // Search
        if ($rawSearch = $request->get('search')) {
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

        // Track viewed product to Klaviyo (if subscriber identified)
        $this->trackViewedProduct($peptide);

        return view('peptides.show', compact('peptide', 'relatedPeptides'));
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
