<form action="{{ $bundle ? route('admin.stack-bundles.update', $bundle) : route('admin.stack-bundles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($bundle) @method('PUT') @endif

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
                <h3 class="text-lg font-semibold mb-4">Bundle Details</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name', $bundle?->name) }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $bundle?->slug) }}" placeholder="Auto-generated"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('description', $bundle?->description) }}</textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Goal</label>
                            <select name="stack_goal_id"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">No specific goal</option>
                                @foreach($goals as $goal)
                                    <option value="{{ $goal->id }}" {{ old('stack_goal_id', $bundle?->stack_goal_id) == $goal->id ? 'selected' : '' }}>{{ $goal->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bundle Price ($)</label>
                            <input type="number" name="bundle_price" value="{{ old('bundle_price', $bundle?->bundle_price) }}" required step="0.01" min="0"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Links</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">External URL (checkout link)</label>
                        <input type="url" name="external_url" value="{{ old('external_url', $bundle?->external_url) }}"
                            placeholder="https://example.com/bundle"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Outbound Link (for tracked redirect)</label>
                        <select name="outbound_link_id"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">None</option>
                            @foreach($outboundLinks as $link)
                                <option value="{{ $link->id }}" {{ old('outbound_link_id', $bundle?->outbound_link_id) == $link->id ? 'selected' : '' }}>{{ $link->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Image</h3>
                @if($bundle?->image)
                    <div class="mb-4">
                        <img src="{{ Storage::url($bundle->image) }}" alt="{{ $bundle->name }}" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-gold/10 file:text-brand-gold hover:file:bg-brand-gold/20">
            </div>

            @if($stores->count())
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Store Pricing</h3>
                <p class="text-sm text-gray-500 mb-4">Set per-store bundle pricing and links. Leave price empty to exclude from a store.</p>
                <div class="space-y-4">
                    @foreach($stores as $store)
                        @php
                            $pivot = $bundle?->stores->firstWhere('id', $store->id)?->pivot;
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                @if($store->logo)
                                    <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-6 h-6 object-contain rounded">
                                @endif
                                <span class="font-medium text-gray-900">{{ $store->name }}</span>
                                @if($store->is_recommended)
                                    <span class="text-xs text-amber-500 font-medium">(Globally Recommended)</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Bundle Price ($)</label>
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
                                    placeholder="https://store.com/bundle"
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
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            @if($bundle && $bundle->items->count() > 0)
                <div class="card p-6">
                    <h3 class="text-lg font-semibold mb-4">Pricing Summary</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Regular Total</dt>
                            <dd class="font-medium">${{ $bundle->regular_total }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Bundle Price</dt>
                            <dd class="font-medium">${{ number_format($bundle->bundle_price, 2) }}</dd>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <dt class="text-green-600 font-medium">Savings</dt>
                            <dd class="font-bold text-green-600">${{ $bundle->savings_amount }} ({{ $bundle->savings_percentage }}%)</dd>
                        </div>
                    </dl>
                </div>
            @endif

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input type="number" name="order" value="{{ old('order', $bundle?->order ?? 0) }}" min="0"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_professor_pick" value="1" {{ old('is_professor_pick', $bundle?->is_professor_pick) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span>Professor's Pick</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $bundle?->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span>Active</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full btn btn-primary">{{ $bundle ? 'Update Bundle' : 'Create Bundle' }}</button>

            @if($bundle)
                <form action="{{ route('admin.stack-bundles.destroy', $bundle) }}" method="POST"
                    onsubmit="return confirm('Delete this bundle?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600">Delete Bundle</button>
                </form>
            @endif
        </div>
    </div>
</form>
