<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Stack Goals</span>
            <a href="{{ route('admin.stack-goals.create') }}" class="btn btn-primary">+ New Goal</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="mb-6 flex items-center gap-4">
        <form method="GET" class="flex items-center gap-4 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search goals..."
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
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Goal</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Products</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Bundles</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($goals as $goal)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $goal->order }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($goal->color)
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $goal->color }}"></div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $goal->name }}</div>
                                    <div class="text-sm text-gray-500">/stack-builder/{{ $goal->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm">{{ $goal->products_count ?? $goal->products()->count() }}</td>
                        <td class="px-6 py-4 text-center text-sm">{{ $goal->bundles_count ?? $goal->bundles()->count() }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($goal->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.stack-goals.edit', $goal) }}" class="text-brand-gold hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No stack goals found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($goals->hasPages())
            <div class="px-6 py-4 border-t">{{ $goals->links() }}</div>
        @endif
    </div>
</x-admin-layout>
