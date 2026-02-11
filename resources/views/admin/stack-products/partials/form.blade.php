<form action="{{ $product ? route('admin.stack-products.update', $product) : route('admin.stack-products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($product) @method('PUT') @endif

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
                <h3 class="text-lg font-semibold mb-4">Product Details</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name', $product?->name) }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $product?->slug) }}" placeholder="Auto-generated"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <input type="text" name="subtitle" value="{{ old('subtitle', $product?->subtitle) }}" placeholder="Short tagline"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('description', $product?->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dosing Info</label>
                        <input type="text" name="dosing_info" value="{{ old('dosing_info', $product?->dosing_info) }}" placeholder="e.g. 5mg/day"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Pricing</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Regular Price ($)</label>
                        <input type="number" name="price" value="{{ old('price', $product?->price) }}" required step="0.01" min="0"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sale Price ($)</label>
                        <input type="number" name="sale_price" value="{{ old('sale_price', $product?->sale_price) }}" step="0.01" min="0"
                            placeholder="Leave empty for no sale"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>
            </div>

            <div class="card p-6" x-data="{ benefits: {{ json_encode(old('key_benefits', $product?->key_benefits ?? [''])) }} }">
                <h3 class="text-lg font-semibold mb-4">Key Benefits</h3>
                <div class="space-y-2">
                    <template x-for="(benefit, index) in benefits" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" :name="'key_benefits[' + index + ']'" x-model="benefits[index]" placeholder="Enter a benefit"
                                class="flex-1 rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <button type="button" @click="benefits.splice(index, 1)" class="text-red-400 hover:text-red-600" x-show="benefits.length > 1">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="benefits.push('')" class="text-sm text-brand-gold hover:underline">+ Add Benefit</button>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Links</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">External URL (direct buy link)</label>
                        <input type="url" name="external_url" value="{{ old('external_url', $product?->external_url) }}"
                            placeholder="https://example.com/product"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Outbound Link (for tracked redirect)</label>
                        <select name="outbound_link_id"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">None</option>
                            @foreach($outboundLinks as $link)
                                <option value="{{ $link->id }}" {{ old('outbound_link_id', $product?->outbound_link_id) == $link->id ? 'selected' : '' }}>{{ $link->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Related Peptide (for "Learn More")</label>
                        <select name="related_peptide_id"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">None</option>
                            @foreach($peptides as $peptide)
                                <option value="{{ $peptide->id }}" {{ old('related_peptide_id', $product?->related_peptide_id) == $peptide->id ? 'selected' : '' }}>{{ $peptide->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Image</h3>
                @if($product?->image)
                    <div class="mb-4">
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-gold/10 file:text-brand-gold hover:file:bg-brand-gold/20">
            </div>

            @if($stores->count())
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Store Pricing</h3>
                <p class="text-sm text-gray-500 mb-4">Set per-store pricing and links. Leave price empty to exclude from a store.</p>
                <div class="space-y-4">
                    @foreach($stores as $store)
                        @php
                            $pivot = $product?->stores->firstWhere('id', $store->id)?->pivot;
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                @if($store->logo)
                                    <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-6 h-6 object-contain rounded">
                                @endif
                                <span class="font-medium text-gray-900">{{ $store->name }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Price ($)</label>
                                    <input type="number" name="store_prices[{{ $store->id }}][price]"
                                        value="{{ old("store_prices.{$store->id}.price", $pivot?->price) }}"
                                        step="0.01" min="0" placeholder="0.00"
                                        class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Outbound Link</label>
                                    <select name="store_prices[{{ $store->id }}][outbound_link_id]"
                                        class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm">
                                        <option value="">None</option>
                                        @foreach($outboundLinks as $link)
                                            <option value="{{ $link->id }}" {{ old("store_prices.{$store->id}.outbound_link_id", $pivot?->outbound_link_id) == $link->id ? 'selected' : '' }}>{{ $link->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Direct URL (fallback if no outbound link)</label>
                                <input type="url" name="store_prices[{{ $store->id }}][url]"
                                    value="{{ old("store_prices.{$store->id}.url", $pivot?->url) }}"
                                    placeholder="https://store.com/product"
                                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold text-sm">
                            </div>
                            <div class="mt-3 flex items-center gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="store_prices[{{ $store->id }}][is_in_stock]" value="1"
                                        {{ old("store_prices.{$store->id}.is_in_stock", $pivot?->is_in_stock ?? true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                                    <span class="text-sm">In Stock</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="store_prices[{{ $store->id }}][is_recommended]" value="1"
                                        {{ old("store_prices.{$store->id}.is_recommended", $pivot?->is_recommended) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                                    <span class="text-sm text-amber-700 font-medium">Recommended</span>
                                    @if($store->is_recommended)
                                        <span class="text-xs text-amber-400">(also globally recommended)</span>
                                    @endif
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Goals</h3>
                <div class="space-y-2">
                    @foreach($goals as $goal)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="goals[]" value="{{ $goal->id }}"
                                {{ in_array($goal->id, old('goals', $product?->goals->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                            <span class="text-sm">{{ $goal->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input type="number" name="order" value="{{ old('order', $product?->order ?? 0) }}" min="0"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product?->is_featured) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span>Professor's Pick</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span>Active</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full btn btn-primary">{{ $product ? 'Update Product' : 'Create Product' }}</button>

            @if($product)
                <form action="{{ route('admin.stack-products.destroy', $product) }}" method="POST"
                    onsubmit="return confirm('Delete this product?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600">Delete Product</button>
                </form>
            @endif
        </div>
    </div>
</form>
