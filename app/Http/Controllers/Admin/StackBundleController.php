<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutboundLink;
use App\Models\StackBundle;
use App\Models\StackBundleItem;
use App\Models\StackGoal;
use App\Models\StackProduct;
use App\Models\StackStore;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StackBundleController extends Controller
{
    public function index(Request $request)
    {
        $query = StackBundle::with('goal', 'items.product');

        if ($rawSearch = $request->get('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $rawSearch);
            $query->where('name', 'like', "%{$search}%");
        }

        if ($goalId = $request->get('goal')) {
            $query->where('stack_goal_id', $goalId);
        }

        $bundles = $query->ordered()->paginate(15)->withQueryString();
        $goals = StackGoal::ordered()->get();

        return view('admin.stack-bundles.index', compact('bundles', 'goals'));
    }

    public function create()
    {
        $goals = StackGoal::active()->ordered()->get();
        $outboundLinks = OutboundLink::active()->orderBy('name')->get();
        $stores = StackStore::active()->ordered()->get();

        return view('admin.stack-bundles.create', compact('goals', 'outboundLinks', 'stores'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateBundle($request);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('stack-bundles', 'public');
        }

        $validated['is_professor_pick'] = $request->boolean('is_professor_pick');
        $validated['is_active'] = $request->boolean('is_active');

        $bundle = StackBundle::create($validated);
        $this->syncStorePricing($bundle, $request->get('store_prices', []));

        return redirect()->route('admin.stack-bundles.edit', $bundle)
            ->with('success', 'Bundle created. Now add products below.');
    }

    public function edit(StackBundle $stackBundle)
    {
        $stackBundle->load('items.product', 'goal', 'stores');
        $goals = StackGoal::active()->ordered()->get();
        $outboundLinks = OutboundLink::active()->orderBy('name')->get();
        $products = StackProduct::active()->ordered()->get();
        $stores = StackStore::active()->ordered()->get();

        return view('admin.stack-bundles.edit', compact('stackBundle', 'goals', 'outboundLinks', 'products', 'stores'));
    }

    public function update(Request $request, StackBundle $stackBundle)
    {
        $validated = $this->validateBundle($request, $stackBundle->id);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('stack-bundles', 'public');
        }

        $validated['is_professor_pick'] = $request->boolean('is_professor_pick');
        $validated['is_active'] = $request->boolean('is_active');

        $stackBundle->update($validated);
        $this->syncStorePricing($stackBundle, $request->get('store_prices', []));

        return redirect()->route('admin.stack-bundles.edit', $stackBundle)
            ->with('success', 'Bundle updated successfully.');
    }

    public function destroy(StackBundle $stackBundle)
    {
        $stackBundle->delete();

        return redirect()->route('admin.stack-bundles.index')
            ->with('success', 'Bundle deleted successfully.');
    }

    // Bundle Item Management
    public function storeItem(Request $request, StackBundle $stackBundle)
    {
        $validated = $request->validate([
            'stack_product_id' => 'required|exists:stack_products,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        // Prevent duplicate products in same bundle
        $exists = $stackBundle->items()->where('stack_product_id', $validated['stack_product_id'])->exists();
        if ($exists) {
            return back()->with('error', 'This product is already in the bundle.');
        }

        $validated['order'] = $stackBundle->items()->count();
        $stackBundle->items()->create($validated);

        return back()->with('success', 'Product added to bundle.');
    }

    public function updateItem(Request $request, StackBundle $stackBundle, StackBundleItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
            'order' => 'nullable|integer|min:0',
        ]);

        $item->update($validated);

        return back()->with('success', 'Item updated.');
    }

    public function destroyItem(StackBundle $stackBundle, StackBundleItem $item)
    {
        $item->delete();

        return back()->with('success', 'Product removed from bundle.');
    }

    protected function syncStorePricing(StackBundle $bundle, array $storePrices): void
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
        $bundle->stores()->sync($syncData);
    }

    protected function validateBundle(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stack_bundles,slug' . ($ignoreId ? ",{$ignoreId}" : ''),
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|max:2048',
            'stack_goal_id' => 'nullable|exists:stack_goals,id',
            'bundle_price' => 'required|numeric|min:0',
            'external_url' => 'nullable|url|max:2000',
            'outbound_link_id' => 'nullable|exists:outbound_links,id',
            'is_professor_pick' => 'nullable',
            'is_active' => 'nullable',
            'order' => 'nullable|integer|min:0',
        ]);
    }
}
