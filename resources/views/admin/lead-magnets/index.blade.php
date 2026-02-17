<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Lead Magnets</span>
            <a href="{{ route('admin.lead-magnets.create') }}" class="btn btn-primary">+ New Lead Magnet</a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Segment</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Downloads</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($leadMagnets as $lm)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $lm->name }}</div>
                            <div class="text-sm text-gray-500">/lead-magnet/{{ $lm->slug }}</div>
                        </td>
                        <td class="px-6 py-4 text-center uppercase text-sm">{{ $lm->file_type }}</td>
                        <td class="px-6 py-4 text-center uppercase text-sm">{{ $lm->segment }}</td>
                        <td class="px-6 py-4 text-center">{{ number_format($lm->downloads_count) }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($lm->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.lead-magnets.edit', $lm) }}" class="text-brand-gold hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No lead magnets found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($leadMagnets->hasPages())
            <div class="px-6 py-4 border-t">{{ $leadMagnets->links() }}</div>
        @endif
    </div>
</x-admin-layout>
