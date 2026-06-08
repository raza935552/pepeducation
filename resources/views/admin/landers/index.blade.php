<x-admin-layout>
    <x-slot name="header"><span>Landers</span></x-slot>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Editable (CMS) landers --}}
    <div class="card overflow-hidden mb-6">
        <div class="px-6 py-3 border-b border-gray-100 text-sm font-semibold text-gray-900">Editable landers (CMS)</div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">URL</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($cmsLanders as $l)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4"><div class="font-medium text-gray-900">{{ $l->name }}</div><div class="text-xs text-gray-400 font-mono">template: {{ $l->template }}</div></td>
                        <td class="px-6 py-4 text-sm text-gray-600 font-mono">/lp/{{ $l->slug }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($l->is_active)<span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Active</span>
                            @else<span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500">Off</span>@endif
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <a href="{{ $l->url }}" target="_blank" class="text-sm text-gray-500 hover:text-gray-700 mr-3">View ↗</a>
                            <a href="{{ route('admin.landers.edit', $l) }}" class="btn btn-primary btn-sm">Edit content</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No CMS landers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Legacy static landers (view-only) --}}
    <div class="card overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-100 text-sm font-semibold text-gray-900">Legacy landers <span class="font-normal text-gray-400">(static — not yet editable)</span></div>
        <div class="divide-y divide-gray-50">
            @foreach($staticLanders as $slug)
                <div class="flex items-center justify-between px-6 py-3">
                    <span class="text-sm font-mono text-gray-600">/lp/{{ $slug }}</span>
                    <a href="{{ url('/lp/'.$slug) }}" target="_blank" class="text-sm text-gray-500 hover:text-gray-700">View ↗</a>
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>
