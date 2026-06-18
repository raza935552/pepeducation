<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use Illuminate\Http\Request;

class ForumCategoryController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.community.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['slug'] = ! empty($data['slug'])
            ? \Illuminate\Support\Str::slug($data['slug'])
            : ForumCategory::generateUniqueSlug($data['name']);

        ForumCategory::create($data);

        return back()->with('status', 'Category created.');
    }

    public function update(Request $request, ForumCategory $forumCategory)
    {
        $data = $this->validateData($request, $forumCategory->id);
        if (! empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['slug']);
        } else {
            unset($data['slug']);
        }

        $forumCategory->update($data);

        return back()->with('status', 'Category updated.');
    }

    public function destroy(ForumCategory $forumCategory)
    {
        if ($forumCategory->threads()->exists()) {
            return back()->withErrors(['category' => 'Cannot delete a category that still has discussions. Move or remove them first.']);
        }

        $forumCategory->delete();

        return back()->with('status', 'Category deleted.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:90'],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:16'],
            'color' => ['nullable', 'string', 'max:9'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
