<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Stack Products</span>
            <a href="{{ route('admin.stack-products.create') }}" class="btn btn-primary">+ New Product</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="mb-6 flex items-center gap-4">
        <form method="GET" class="flex items-center gap-4 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                class="rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold w-64">
            <select name="goal" onchange="this.form.submit()"
                class="rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                <option value="">All Goals</option>
                @foreach($goals as $goal)
                    <option value="{{ $goal->id }}" {{ request('goal') == $goal->id ? 'selected' : '' }}>{{ $goal->name }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()"
                class="rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Goals</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Featured</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="" class="w-10 h-10 rounded object-cover">
                                @else
                                    <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                    @if($product->subtitle)
                                        <div class="text-sm text-gray-500">{{ $product->subtitle }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($product->goals as $goal)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">{{ $goal->name }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($product->has_sale)
                                <span class="text-sm text-gray-400 line-through">${{ number_format($product->price, 2) }}</span>
                                <span class="font-medium text-green-600">${{ number_format($product->sale_price, 2) }}</span>
                            @else
                                <span class="font-medium">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($product->is_featured)
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Featured</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($product->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.stack-products.edit', $product) }}" class="text-brand-gold hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No stack products found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($products->hasPages())
            <div class="px-6 py-4 border-t">{{ $products->links() }}</div>
        @endif
    </div>
</x-admin-layout>
