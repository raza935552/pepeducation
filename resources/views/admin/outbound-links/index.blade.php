<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Outbound Links</span>
            <a href="{{ route('admin.outbound-links.create') }}" class="btn btn-primary">+ New Link</a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Destination</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Clicks</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($links as $link)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $link->name }}</div>
                            <div class="text-sm text-gray-500 font-mono">/go/{{ $link->slug }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $link->destination_url }}</td>
                        <td class="px-6 py-4 text-center">{{ number_format($link->clicks_count) }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($link->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.outbound-links.edit', $link) }}" class="text-brand-gold hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No outbound links found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($links->hasPages())
            <div class="px-6 py-4 border-t">{{ $links->links() }}</div>
        @endif
    </div>
</x-admin-layout>
