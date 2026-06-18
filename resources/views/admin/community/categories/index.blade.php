<x-admin-layout>
    <x-slot name="header">Community Categories</x-slot>

    @if(session('status'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="mb-4"><a href="{{ route('admin.community.dashboard') }}" class="text-sm text-admin-primary-600">← Back to moderation</a></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Add form --}}
        <div class="card h-fit">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Add category</h3>
            <form action="{{ route('admin.community.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input w-full" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="input w-full">{{ old('description') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon (emoji)</label>
                        <input type="text" name="icon" value="{{ old('icon', '💬') }}" class="input w-full" maxlength="16">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="color" name="color" value="{{ old('color', '#6366F1') }}" class="w-full h-10 rounded cursor-pointer">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="input w-full" min="0">
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
                <button class="btn-primary w-full">Add category</button>
            </form>
        </div>

        {{-- List --}}
        <div class="lg:col-span-2 space-y-3">
            @forelse($categories as $category)
                <div class="card">
                    <form action="{{ route('admin.community.categories.update', $category) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">{{ $category->icon }}</span>
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <input type="text" name="name" value="{{ $category->name }}" class="input" required>
                                <input type="text" name="icon" value="{{ $category->icon }}" class="input" maxlength="16">
                                <input type="text" name="description" value="{{ $category->description }}" class="input sm:col-span-2" placeholder="Description">
                                <div class="flex items-center gap-2">
                                    <input type="color" name="color" value="{{ $category->color ?? '#6366F1' }}" class="w-10 h-9 rounded">
                                    <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="input w-20" min="0" title="Sort order">
                                    <label class="flex items-center gap-1 text-sm"><input type="checkbox" name="is_active" value="1" @checked($category->is_active) class="rounded"> Active</label>
                                </div>
                                <div class="text-xs text-gray-400 flex items-center">{{ number_format($category->threads_count) }} threads · /community/c/{{ $category->slug }}</div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-3">
                            <button class="btn-primary text-sm">Save</button>
                        </div>
                    </form>
                    <div class="flex justify-end mt-2 pt-2 border-t border-gray-100">
                        <form action="{{ route('admin.community.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="text-sm px-3 py-1.5 rounded bg-red-50 text-red-600">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="card text-center text-gray-500">No categories yet — add your first one.</div>
            @endforelse
        </div>
    </div>
</x-admin-layout>
