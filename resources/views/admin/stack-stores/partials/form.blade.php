<form action="{{ $store ? route('admin.stack-stores.update', $store) : route('admin.stack-stores.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($store) @method('PUT') @endif

    @if($errors->any())
        <div class="rounded-lg bg-red-50 p-4">
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Store Details</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name', $store?->name) }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $store?->slug) }}" placeholder="Auto-generated from name"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                        <input type="url" name="website_url" value="{{ old('website_url', $store?->website_url) }}" placeholder="https://example.com"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('description', $store?->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Logo</h3>
                @if($store?->logo)
                    <div class="mb-4">
                        <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-32 h-32 object-contain rounded-lg bg-gray-50 p-2">
                    </div>
                @endif
                <input type="file" name="logo" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-gold/10 file:text-brand-gold hover:file:bg-brand-gold/20">
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input type="number" name="order" value="{{ old('order', $store?->order ?? 0) }}" min="0"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $store?->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span>Active</span>
                    </label>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_recommended" value="1" {{ old('is_recommended', $store?->is_recommended) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                            <span class="text-amber-700 font-medium">Recommended</span>
                        </label>
                        <p class="text-xs text-gray-400 mt-1 ml-6">Globally recommended for all products (can be overridden per-product)</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full btn btn-primary">{{ $store ? 'Update Store' : 'Create Store' }}</button>

            @if($store)
                <form action="{{ route('admin.stack-stores.destroy', $store) }}" method="POST"
                    onsubmit="return confirm('Delete this store? All product-store pricing links will be removed.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600">Delete Store</button>
                </form>
            @endif
        </div>
    </div>
</form>
