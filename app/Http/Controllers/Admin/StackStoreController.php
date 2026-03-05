<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StackStore;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StackStoreController extends Controller
{
    public function index(Request $request)
    {
        $query = StackStore::query();

        if ($rawSearch = $request->get('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $rawSearch);
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->get('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->get('status') === 'inactive') {
            $query->where('is_active', false);
        }

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        $stores = $query->ordered()->paginate(15)->withQueryString();

        return view('admin.stack-stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stack-stores.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateStore($request);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('stack-stores', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_recommended'] = $request->boolean('is_recommended');

        $store = StackStore::create($validated);
        $this->syncPeptideLinks($store, $request->get('peptide_links', []));

        return redirect()->route('admin.stack-stores.index')
            ->with('success', 'Vendor created successfully.');
    }

    public function edit(StackStore $stackStore)
    {
        $stackStore->load('peptideLinks');
        return view('admin.stack-stores.edit', compact('stackStore'));
    }

    public function update(Request $request, StackStore $stackStore)
    {
        $validated = $this->validateStore($request, $stackStore->id);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('stack-stores', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_recommended'] = $request->boolean('is_recommended');
        $stackStore->update($validated);
        $this->syncPeptideLinks($stackStore, $request->get('peptide_links', []));

        return redirect()->route('admin.stack-stores.index')
            ->with('success', 'Vendor updated successfully.');
    }

    public function destroy(StackStore $stackStore)
    {
        $stackStore->delete();

        return redirect()->route('admin.stack-stores.index')
            ->with('success', 'Vendor deleted successfully.');
    }

    protected function validateStore(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stack_stores,slug' . ($ignoreId ? ",{$ignoreId}" : ''),
            'website_url' => 'nullable|url|max:2000',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:2048',
            'category' => 'nullable|string|in:research_grade,telehealth,affordable',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
            'is_recommended' => 'nullable',
            'peptide_links' => 'nullable|array',
            'peptide_links.*.peptide_name' => 'required_with:peptide_links.*|string|max:255',
            'peptide_links.*.url' => 'required_with:peptide_links.*|string|max:2000',
            'peptide_links.*.price' => 'nullable|numeric|min:0',
            'peptide_links.*.is_in_stock' => 'nullable',
        ]);
    }

    protected function syncPeptideLinks(StackStore $store, array $links): void
    {
        $store->peptideLinks()->delete();

        foreach ($links as $i => $link) {
            if (empty($link['peptide_name']) || empty($link['url'])) continue;

            $store->peptideLinks()->create([
                'peptide_name' => $link['peptide_name'],
                'url' => $link['url'],
                'price' => $link['price'] ?: null,
                'is_in_stock' => isset($link['is_in_stock']),
                'order' => $i,
            ]);
        }
    }
}
