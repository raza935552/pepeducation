<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StackGoal;
use App\Models\StackProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StackGoalController extends Controller
{
    public function index(Request $request)
    {
        $query = StackGoal::query();

        if ($rawSearch = $request->get('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $rawSearch);
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->get('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->get('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $goals = $query->ordered()->paginate(15)->withQueryString();

        return view('admin.stack-goals.index', compact('goals'));
    }

    public function create()
    {
        $products = StackProduct::orderBy('name')->get(['id', 'name', 'is_active']);

        return view('admin.stack-goals.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateGoal($request);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('stack-goals', 'public');
        }

        $goal = StackGoal::create($validated);
        $this->syncProducts($goal, $request);

        return redirect()->route('admin.stack-goals.index')
            ->with('success', 'Stack goal created successfully.');
    }

    public function edit(StackGoal $stackGoal)
    {
        $stackGoal->load('products:id');
        $products = StackProduct::orderBy('name')->get(['id', 'name', 'is_active']);

        return view('admin.stack-goals.edit', compact('stackGoal', 'products'));
    }

    public function update(Request $request, StackGoal $stackGoal)
    {
        $validated = $this->validateGoal($request, $stackGoal->id);
        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('stack-goals', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $stackGoal->update($validated);
        $this->syncProducts($stackGoal, $request);

        return redirect()->route('admin.stack-goals.index')
            ->with('success', 'Stack goal updated successfully.');
    }

    /**
     * Sync the goal's products from the submitted products[] checkboxes, preserving
     * the existing per-goal order for products that stay and appending new ones.
     */
    protected function syncProducts(StackGoal $goal, Request $request): void
    {
        if (!$request->has('products')) {
            return; // form section absent — leave assignments untouched
        }

        $ids = array_values(array_unique(array_map('intval', (array) $request->input('products', []))));
        $existing = $goal->products()->pluck('goal_stack_product.order', 'stack_products.id');
        $next = (int) ($existing->max() ?? 0);

        $sync = [];
        foreach ($ids as $id) {
            $sync[$id] = ['order' => $existing[$id] ?? ++$next];
        }

        $goal->products()->sync($sync);
    }

    public function destroy(StackGoal $stackGoal)
    {
        $stackGoal->delete();

        return redirect()->route('admin.stack-goals.index')
            ->with('success', 'Stack goal deleted successfully.');
    }

    protected function validateGoal(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stack_goals,slug' . ($ignoreId ? ",{$ignoreId}" : ''),
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:2048',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);
    }
}
