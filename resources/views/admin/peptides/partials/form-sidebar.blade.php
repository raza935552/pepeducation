@php
    $categories = \App\Models\Category::orderBy('name')->get();
    $selectedCategories = old('categories', $peptide?->categories->pluck('id')->toArray() ?? []);
@endphp

<!-- Publish Status -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>

    <label class="flex items-center gap-3 cursor-pointer">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1"
               {{ old('is_published', $peptide?->is_published) ? 'checked' : '' }}
               class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
        <span class="text-gray-700">Published</span>
    </label>
    <p class="mt-2 text-sm text-gray-500">
        Unpublished peptides will not appear on the public site.
    </p>
</div>

<!-- Research Status -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Research Status</h3>

    <select name="research_status" class="input w-full">
        @foreach(['extensive' => 'Extensively Studied', 'well' => 'Well Researched', 'emerging' => 'Emerging Research', 'limited' => 'Limited Research'] as $value => $label)
            <option value="{{ $value }}" {{ old('research_status', $peptide?->research_status ?? 'limited') === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>

<!-- Categories -->
<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>

    <div class="space-y-2 max-h-64 overflow-y-auto">
        @foreach($categories as $category)
            <label class="flex items-center gap-3 cursor-pointer p-2 rounded hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                       {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="inline-flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $category->color }}"></span>
                    <span class="text-gray-700">{{ $category->name }}</span>
                </span>
            </label>
        @endforeach
    </div>
</div>
