<x-admin-layout>
    <x-slot name="header">Blog Categories</x-slot>

    @if(session('error'))
        <div class="mb-6 rounded-lg bg-red-50 p-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Category Form -->
        <div class="card h-fit">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Category</h3>
            <form action="{{ route('admin.blog-categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input w-full" required placeholder="Category name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="input w-full" placeholder="auto-generated-from-name">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="input w-full" placeholder="Optional description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <div class="flex gap-2">
                        <input type="color" name="color" value="{{ old('color', '#3b82f6') }}" class="w-12 h-10 rounded cursor-pointer">
                        <input type="text" name="color_hex" value="{{ old('color', '#3b82f6') }}" class="input flex-1"
                               x-data x-on:input="$el.previousElementSibling.value = $el.value"
                               x-init="$el.previousElementSibling.addEventListener('input', (e) => $el.value = e.target.value)">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="input w-full" min="0">
                </div>
                <button type="submit" class="btn btn-primary w-full">Add Category</button>
            </form>
        </div>

        <!-- Categories List -->
        <div class="lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Posts</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Order</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($categories as $category)
                                <tr class="hover:bg-gray-50 transition-colors"
                                    x-data="{ editing: false, name: '{{ addslashes($category->name) }}', color: '{{ $category->color }}', description: '{{ addslashes($category->description ?? '') }}', sort_order: {{ $category->sort_order }} }">
                                    <td class="px-6 py-4">
                                        <template x-if="!editing">
                                            <div>
                                                <div class="flex items-center gap-3">
                                                    <span class="w-4 h-4 rounded-full flex-shrink-0" style="background-color: {{ $category->color }}"></span>
                                                    <span class="font-medium text-gray-900">{{ $category->name }}</span>
                                                </div>
                                                @if($category->description)
                                                    <p class="text-sm text-gray-500 mt-1 ml-7">{{ $category->description }}</p>
                                                @endif
                                                <div class="text-xs text-gray-400 mt-0.5 ml-7">/blog/category/{{ $category->slug }}</div>
                                            </div>
                                        </template>
                                        <template x-if="editing">
                                            <form action="{{ route('admin.blog-categories.update', $category) }}" method="POST"
                                                  class="space-y-2" @submit="editing = false">
                                                @csrf
                                                @method('PUT')
                                                <div class="flex items-center gap-2">
                                                    <input type="color" name="color" x-model="color" class="w-8 h-8 rounded cursor-pointer">
                                                    <input type="text" name="name" x-model="name" class="input flex-1 text-sm py-1" required>
                                                </div>
                                                <textarea name="description" x-model="description" class="input w-full text-sm py-1" rows="1" placeholder="Description"></textarea>
                                                <div class="flex items-center gap-2">
                                                    <label class="text-xs text-gray-500">Order:</label>
                                                    <input type="number" name="sort_order" x-model="sort_order" class="input w-20 text-sm py-1" min="0">
                                                    <button type="submit" class="p-1 text-green-600 hover:text-green-700">
                                                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                    <button type="button" @click="editing = false" class="p-1 text-gray-400 hover:text-gray-600">
                                                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </form>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $category->posts_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ $category->sort_order }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <template x-if="!editing">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" @click="editing = true"
                                                        class="p-2 text-gray-500 hover:text-primary-600 transition-colors" title="Edit">
                                                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                @if($category->posts_count === 0)
                                                    <form action="{{ route('admin.blog-categories.destroy', $category) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Delete {{ addslashes($category->name) }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 transition-colors" title="Delete">
                                                            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="p-2 text-gray-300 cursor-not-allowed" title="Has posts">
                                                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        No blog categories yet. Create your first one!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
