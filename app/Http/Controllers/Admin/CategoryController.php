<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('peptides')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'color' => 'required|string|max:7',
        ]);

        $maxOrder = Category::max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;

        Category::create($validated);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'color' => 'required|string|max:7',
        ]);

        $category->update($validated);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->peptides()->exists()) {
            return back()->with('error', 'Cannot delete category with associated peptides.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $order = $request->validate(['order' => 'required|array'])['order'];

        DB::transaction(function () use ($order) {
            foreach ($order as $index => $id) {
                Category::where('id', $id)->update(['sort_order' => $index]);
            }
        });

        return response()->json(['success' => true]);
    }
}
