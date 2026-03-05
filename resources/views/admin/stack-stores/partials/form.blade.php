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
                <h3 class="text-lg font-semibold mb-4">Vendor Details</h3>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            @foreach(\App\Models\StackStore::CATEGORIES as $value => $label)
                                <option value="{{ $value }}" {{ old('category', $store?->category ?? 'research_grade') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Used to filter vendors on quiz results based on user preference.</p>
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

            {{-- Peptide Links --}}
            <div class="card p-6" x-data="{ links: @js($store?->peptideLinks?->map(fn($l) => ['peptide_name' => $l->peptide_name, 'url' => $l->url, 'price' => $l->price, 'is_in_stock' => $l->is_in_stock])->values()->toArray() ?? []) }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Peptide Links</h3>
                    <button type="button" @click="links.push({ peptide_name: '', url: '', price: '', is_in_stock: true })"
                        class="text-sm text-brand-gold hover:underline">+ Add Link</button>
                </div>
                <p class="text-xs text-gray-500 mb-4">Direct purchase links for specific peptides at this vendor.</p>

                <template x-if="links.length === 0">
                    <p class="text-sm text-gray-400 text-center py-4">No peptide links added yet.</p>
                </template>

                <div class="space-y-3">
                    <template x-for="(link, index) in links" :key="index">
                        <div class="border border-gray-200 rounded-lg p-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500" x-text="'Link #' + (index + 1)"></span>
                                <button type="button" @click="links.splice(index, 1)" class="text-red-400 hover:text-red-600 text-xs">Remove</button>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-0.5">Peptide Name</label>
                                    <input type="text" :name="'peptide_links[' + index + '][peptide_name]'" x-model="link.peptide_name" required
                                        placeholder="e.g. BPC-157" class="w-full rounded border-gray-300 text-sm focus:border-brand-gold focus:ring-brand-gold">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-0.5">Price</label>
                                    <input type="number" :name="'peptide_links[' + index + '][price]'" x-model="link.price" step="0.01" min="0"
                                        placeholder="49.99" class="w-full rounded border-gray-300 text-sm focus:border-brand-gold focus:ring-brand-gold">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-0.5">URL</label>
                                <input type="text" :name="'peptide_links[' + index + '][url]'" x-model="link.url" required
                                    placeholder="https://store.com/bpc-157" class="w-full rounded border-gray-300 text-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="hidden" :name="'peptide_links[' + index + '][is_in_stock]'" value="0">
                                <input type="checkbox" :name="'peptide_links[' + index + '][is_in_stock]'" value="1" :checked="link.is_in_stock"
                                    class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                                <span class="text-gray-600">In Stock</span>
                            </label>
                        </div>
                    </template>
                </div>
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

            <button type="submit" class="w-full btn btn-primary">{{ $store ? 'Update Vendor' : 'Add Vendor' }}</button>

            @if($store)
                <form action="{{ route('admin.stack-stores.destroy', $store) }}" method="POST"
                    onsubmit="return confirm('Delete this vendor? All product pricing links will be removed.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600">Delete Vendor</button>
                </form>
            @endif
        </div>
    </div>
</form>
