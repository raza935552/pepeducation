<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Stack Stores</span>
            <a href="{{ route('admin.stack-stores.create') }}" class="btn btn-primary">+ New Store</a>
        </div>
    </x-slot>

    <div class="mb-6 flex items-center gap-4">
        <form method="GET" class="flex items-center gap-4 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search stores..."
                class="rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold w-64">
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
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Store</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Website</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Products</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($stores as $store)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $store->order }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($store->logo)
                                    <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-8 h-8 object-contain rounded">
                                @else
                                    <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400">N/A</div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $store->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $store->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($store->website_url)
                                <a href="{{ $store->website_url }}" target="_blank" class="text-brand-gold hover:underline">{{ Str::limit($store->website_url, 30) }}</a>
                            @else
                                <span class="text-gray-400">--</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-sm">{{ $store->products()->count() }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($store->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.stack-stores.edit', $store) }}" class="text-brand-gold hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No stack stores found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($stores->hasPages())
            <div class="px-6 py-4 border-t">{{ $stores->links() }}</div>
        @endif
    </div>
</x-admin-layout>
