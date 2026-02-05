<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Title -->
        <div class="card">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
            <input type="text" name="title" value="{{ old('title', $page->title ?? '') }}"
                   class="input w-full text-xl font-semibold" placeholder="Page title" required>
            @error('title')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Editor -->
        <div class="card">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
            <div id="editorjs" class="min-h-[400px] border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800"></div>
            <input type="hidden" name="content" id="content-input">
            @error('content')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Publish Settings -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Publish</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="input w-full">
                        <option value="draft" {{ old('status', $page->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $page->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                    <div class="flex items-center">
                        <span class="text-gray-500 dark:text-gray-400 text-sm mr-1">/</span>
                        <input type="text" name="slug" value="{{ old('slug', $page->slug ?? '') }}"
                               class="input w-full" placeholder="auto-generated-from-title">
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty to auto-generate</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Template</label>
                    <select name="template" class="input w-full">
                        <option value="default" {{ old('template', $page->template ?? 'default') === 'default' ? 'selected' : '' }}>Default</option>
                        <option value="full-width" {{ old('template', $page->template ?? '') === 'full-width' ? 'selected' : '' }}>Full Width</option>
                        <option value="landing" {{ old('template', $page->template ?? '') === 'landing' ? 'selected' : '' }}>Landing Page</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">
                    {{ isset($page) ? 'Update Page' : 'Create Page' }}
                </button>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">SEO</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}"
                           class="input w-full" placeholder="Page title for search engines">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span x-data="{ count: 0 }" x-init="count = $el.previousElementSibling.previousElementSibling.value.length"
                              x-text="count + '/60 characters'"></span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="3" class="input w-full"
                              placeholder="Brief description for search engines">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 160 characters</p>
                </div>
            </div>
        </div>
    </div>
</div>
