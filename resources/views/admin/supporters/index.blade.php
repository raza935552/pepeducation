<x-admin-layout title="Supporters">
    <x-slot name="header">Supporters</x-slot>
    <x-slot name="headerAction">
        <a href="{{ route('admin.supporters.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gold-500 hover:bg-gold-600 text-white font-medium rounded-lg transition-colors">
            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Supporter
        </a>
    </x-slot>

    {{-- Filters --}}
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search supporters..."
                       class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <select name="tier" class="rounded-lg border-gray-300 text-sm">
                <option value="">All Tiers</option>
                <option value="platinum" {{ request('tier') === 'platinum' ? 'selected' : '' }}>Platinum</option>
                <option value="gold" {{ request('tier') === 'gold' ? 'selected' : '' }}>Gold</option>
                <option value="silver" {{ request('tier') === 'silver' ? 'selected' : '' }}>Silver</option>
                <option value="bronze" {{ request('tier') === 'bronze' ? 'selected' : '' }}>Bronze</option>
            </select>
            <select name="status" class="rounded-lg border-gray-300 text-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                Filter
            </button>
            @if(request()->hasAny(['search', 'tier', 'status']))
                <a href="{{ route('admin.supporters.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($supporters->isEmpty())
            <div class="p-12 text-center">
                <svg aria-hidden="true" class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <p class="text-gray-500">No supporters found.</p>
                <a href="{{ route('admin.supporters.create') }}" class="mt-4 inline-flex items-center text-gold-600 hover:text-gold-700 font-medium">
                    Add your first supporter
                    <svg aria-hidden="true" class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supporter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($supporters as $supporter)
                        @include('admin.supporters.partials.row', ['supporter' => $supporter])
                    @endforeach
                </tbody>
            </table>

            @if($supporters->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $supporters->links() }}
                </div>
            @endif
        @endif
    </div>
</x-admin-layout>
