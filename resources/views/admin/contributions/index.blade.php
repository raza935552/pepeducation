<x-admin-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Contributions</h1>
        </div>

        {{-- Status Tabs --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.contributions.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-gold-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                All ({{ array_sum($counts) }})
            </a>
            <a href="{{ route('admin.contributions.index', ['status' => 'pending']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Pending ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('admin.contributions.index', ['status' => 'approved']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'approved' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Approved ({{ $counts['approved'] }})
            </a>
            <a href="{{ route('admin.contributions.index', ['status' => 'rejected']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Rejected ({{ $counts['rejected'] }})
            </a>
        </div>

        {{-- Search --}}
        <form class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by user or peptide..."
                   class="flex-1 rounded-lg border-gray-300 focus:ring-gold-500 focus:border-gold-500">
            <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600">Search</button>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            @if($contributions->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peptide</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($contributions as $contribution)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $contribution->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $contribution->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $contribution->peptide->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $contribution->section_label }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $badge = $contribution->status_badge; @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $badge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $badge['color'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $badge['color'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $badge['color'] === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $contribution->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.contributions.show', $contribution) }}"
                                       class="text-gold-600 hover:text-gold-900 font-medium">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $contributions->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg aria-hidden="true" class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No contributions yet</h3>
                    <p class="mt-2 text-gray-500">Community contributions will appear here for review.</p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
