<div class="card p-6">
    <h3 class="text-lg font-semibold mb-4">Bundle Items</h3>

    {{-- Add Product Form --}}
    <form action="{{ route('admin.stack-bundles.items.store', $bundle) }}" method="POST" class="mb-6 flex items-end gap-4">
        @csrf
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Add Product</label>
            <select name="stack_product_id" required
                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                <option value="">Select a product...</option>
                @foreach($products as $product)
                    @unless($bundle->items->pluck('stack_product_id')->contains($product->id))
                        <option value="{{ $product->id }}">{{ $product->name }} (${{ number_format($product->current_price, 2) }})</option>
                    @endunless
                @endforeach
            </select>
        </div>
        <div class="w-24">
            <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
            <input type="number" name="quantity" value="1" min="1" max="100"
                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
        </div>
        <button type="submit" class="btn btn-primary whitespace-nowrap">Add</button>
    </form>

    {{-- Items List --}}
    @if($bundle->items->count() > 0)
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Unit Price</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($bundle->items as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $item->product->name }}</div>
                            @if($item->product->subtitle)
                                <div class="text-xs text-gray-500">{{ $item->product->subtitle }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-sm">${{ number_format($item->product->current_price, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.stack-bundles.items.update', [$bundle, $item]) }}" method="POST" class="inline-flex items-center gap-1">
                                @csrf @method('PUT')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="100"
                                    class="w-16 text-center rounded border-gray-300 text-sm focus:border-brand-gold focus:ring-brand-gold"
                                    onchange="this.form.submit()">
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-medium">
                            ${{ number_format($item->product->current_price * $item->quantity, 2) }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('admin.stack-bundles.items.destroy', [$bundle, $item]) }}" method="POST"
                                onsubmit="return confirm('Remove this product?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right font-medium text-gray-700">Regular Total:</td>
                    <td class="px-4 py-3 text-right font-bold">${{ $bundle->regular_total }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="text-center py-8 text-gray-500">
            No products in this bundle yet. Add products above.
        </div>
    @endif
</div>
