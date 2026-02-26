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

        StackStore::create($validated);

        return redirect()->route('admin.stack-stores.index')
            ->with('success', 'Stack store created successfully.');
    }

    public function edit(StackStore $stackStore)
    {
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

        return redirect()->route('admin.stack-stores.index')
            ->with('success', 'Stack store updated successfully.');
    }

    public function destroy(StackStore $stackStore)
    {
        $stackStore->delete();

        return redirect()->route('admin.stack-stores.index')
            ->with('success', 'Stack store deleted successfully.');
    }

    protected function validateStore(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stack_stores,slug' . ($ignoreId ? ",{$ignoreId}" : ''),
            'website_url' => 'nullable|url|max:2000',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
            'is_recommended' => 'nullable',
        ]);
    }
}
