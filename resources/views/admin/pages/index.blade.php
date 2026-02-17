<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Pages</span>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                <svg aria-hidden="true" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Page
            </a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                            Title
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                            Status
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                            Author
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                            Updated
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pages as $page)
                        @include('admin.pages.partials.row', ['page' => $page])
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No pages found. Create your first page!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pages->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pages->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
