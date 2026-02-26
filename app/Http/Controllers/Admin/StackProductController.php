<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutboundLink;
use App\Models\Peptide;
use App\Models\StackGoal;
use App\Models\StackProduct;
use App\Models\StackStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StackProductController extends Controller
{
    public function index(Request $request)
    {
        $query = StackProduct::with('goals');

        if ($rawSearch = $request->get('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $rawSearch);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        if ($goalId = $request->get('goal')) {
            $query->whereHas('goals', fn ($q) => $q->where('stack_goals.id', $goalId));
        }

        if ($request->get('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->get('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $products = $query->ordered()->paginate(15)->withQueryString();
        $goals = StackGoal::ordered()->get();

        return view('admin.stack-products.index', compact('products', 'goals'));
    }

    public function create()
    {
        $goals = StackGoal::active()->ordered()->get();
        $outboundLinks = OutboundLink::active()->orderBy('name')->get();
        $peptides = Peptide::published()->orderBy('name')->get();
        $stores = StackStore::active()->ordered()->get();

        return view('admin.stack-products.create', compact('goals', 'outboundLinks', 'peptides', 'stores'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('stack-products', 'public');
        }

        $validated['key_benefits'] = array_filter($request->get('key_benefits', []));
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        $product = StackProduct::create($validated);
        $product->goals()->sync($request->get('goals', []));
        $this->syncStorePricing($product, $request->get('store_prices', []));

        return redirect()->route('admin.stack-products.index')
            ->with('success', 'Stack product created successfully.');
    }

    public function edit(StackProduct $stackProduct)
    {
        $stackProduct->load(['goals', 'stores']);
        $goals = StackGoal::active()->ordered()->get();
        $outboundLinks = OutboundLink::active()->orderBy('name')->get();
        $peptides = Peptide::published()->orderBy('name')->get();
        $stores = StackStore::active()->ordered()->get();

        return view('admin.stack-products.edit', compact('stackProduct', 'goals', 'outboundLinks', 'peptides', 'stores'));
    }

    public function update(Request $request, StackProduct $stackProduct)
    {
        $validated = $this->validateProduct($request, $stackProduct->id);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('stack-products', 'public');
        }

        $validated['key_benefits'] = array_filter($request->get('key_benefits', []));
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        $stackProduct->update($validated);
        $stackProduct->goals()->sync($request->get('goals', []));
        $this->syncStorePricing($stackProduct, $request->get('store_prices', []));

        return redirect()->route('admin.stack-products.index')
            ->with('success', 'Stack product updated successfully.');
    }

    public function destroy(StackProduct $stackProduct)
    {
        $stackProduct->delete();

        return redirect()->route('admin.stack-products.index')
            ->with('success', 'Stack product deleted successfully.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate(['image' => 'required|image|max:2048']);
        $path = $request->file('image')->store('stack-products', 'public');

        return response()->json(['url' => Storage::url($path), 'path' => $path]);
    }

    protected function syncStorePricing(StackProduct $product, array $storePrices): void
    {
        $syncData = [];
        foreach ($storePrices as $storeId => $data) {
            if (empty($data['price'])) continue;
            $syncData[$storeId] = [
                'price' => $data['price'],
                'url' => $data['url'] ?: null,
                'outbound_link_id' => $data['outbound_link_id'] ?: null,
                'is_in_stock' => isset($data['is_in_stock']),
                'is_recommended' => isset($data['is_recommended']) ? true : null,
            ];
        }
        $product->stores()->sync($syncData);
    }

    protected function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stack_products,slug' . ($ignoreId ? ",{$ignoreId}" : ''),
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|max:2048',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'dosing_info' => 'nullable|string|max:255',
            'key_benefits' => 'nullable|array',
            'key_benefits.*' => 'nullable|string|max:255',
            'external_url' => 'nullable|url|max:2000',
            'outbound_link_id' => 'nullable|exists:outbound_links,id',
            'related_peptide_id' => 'nullable|exists:peptides,id',
            'is_featured' => 'nullable',
            'is_active' => 'nullable',
            'order' => 'nullable|integer|min:0',
        ]);
    }
}
