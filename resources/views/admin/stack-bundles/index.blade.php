<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Stack Bundles</span>
            <a href="{{ route('admin.stack-bundles.create') }}" class="btn btn-primary">+ New Bundle</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="mb-6 flex items-center gap-4">
        <form method="GET" class="flex items-center gap-4 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search bundles..."
                class="rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold w-64">
            <select name="goal" onchange="this.form.submit()"
                class="rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                <option value="">All Goals</option>
                @foreach($goals as $goal)
                    <option value="{{ $goal->id }}" {{ request('goal') == $goal->id ? 'selected' : '' }}>{{ $goal->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bundle</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Goal</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Items</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Bundle Price</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Pick</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($bundles as $bundle)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $bundle->name }}</div>
                            <div class="text-sm text-gray-500">{{ $bundle->slug }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $bundle->goal?->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-center text-sm">{{ $bundle->items->count() }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="font-medium">${{ number_format($bundle->bundle_price, 2) }}</div>
                            @if($bundle->items->count() > 0 && (float) $bundle->savings_amount > 0)
                                <div class="text-xs text-green-600">Save ${{ $bundle->savings_amount }} ({{ $bundle->savings_percentage }}%)</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($bundle->is_professor_pick)
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pick</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($bundle->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.stack-bundles.edit', $bundle) }}" class="text-brand-gold hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">No bundles found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($bundles->hasPages())
            <div class="px-6 py-4 border-t">{{ $bundles->links() }}</div>
        @endif
    </div>
</x-admin-layout>
