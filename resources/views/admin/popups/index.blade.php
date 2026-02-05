<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Popups</span>
            <a href="{{ route('admin.popups.create') }}" class="btn btn-primary">+ New Popup</a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Views</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Conversions</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Rate</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($popups as $popup)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $popup->name }}</div>
                            <div class="text-sm text-gray-500">{{ $popup->headline }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">{{ number_format($popup->views_count) }}</td>
                        <td class="px-6 py-4 text-center">{{ number_format($popup->conversions_count) }}</td>
                        <td class="px-6 py-4 text-center">{{ $popup->getConversionRate() }}%</td>
                        <td class="px-6 py-4 text-center">
                            @if($popup->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.popups.edit', $popup) }}" class="text-brand-gold hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No popups found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($popups->hasPages())
            <div class="px-6 py-4 border-t">{{ $popups->links() }}</div>
        @endif
    </div>
</x-admin-layout>
