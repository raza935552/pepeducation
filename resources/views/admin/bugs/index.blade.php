<x-admin-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Bug Reports</h1>
            <a href="{{ route('admin.bugs.create') }}" class="px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600 font-medium">
                + Report Bug
            </a>
        </div>

        {{-- Status Tabs --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.bugs.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-gold-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                All ({{ array_sum($counts) }})
            </a>
            <a href="{{ route('admin.bugs.index', ['status' => 'reported']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'reported' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Reported ({{ $counts['reported'] }})
            </a>
            <a href="{{ route('admin.bugs.index', ['status' => 'in_progress']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'in_progress' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                In Progress ({{ $counts['in_progress'] }})
            </a>
            <a href="{{ route('admin.bugs.index', ['status' => 'fixed']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'fixed' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Fixed ({{ $counts['fixed'] }})
            </a>
            <a href="{{ route('admin.bugs.index', ['status' => 'closed']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'closed' ? 'bg-gray-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Closed ({{ $counts['closed'] }})
            </a>
        </div>

        {{-- Search --}}
        <form class="flex gap-4">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by title or description..."
                   class="flex-1 rounded-lg border-gray-300 focus:ring-gold-500 focus:border-gold-500">
            <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600">Search</button>
        </form>

        {{-- Bug List --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            @if($bugs->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($bugs as $bug)
                        <a href="{{ route('admin.bugs.show', $bug) }}" class="block hover:bg-gray-50 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3">
                                        <span class="font-medium text-gray-900">{{ $bug->title }}</span>
                                        @php $priorityBadge = $bug->priority_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $priorityBadge['color'] === 'gray' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $priorityBadge['color'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $priorityBadge['color'] === 'orange' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $priorityBadge['color'] === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $priorityBadge['label'] }}
                                        </span>
                                        @php $statusBadge = $bug->status_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $statusBadge['color'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $statusBadge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $statusBadge['color'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $statusBadge['color'] === 'gray' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ $statusBadge['label'] }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Reported by {{ $bug->user?->name ?? 'Unknown' }}
                                        @if($bug->page_url)
                                            &middot; <span class="text-gray-400">{{ $bug->page_url }}</span>
                                        @endif
                                    </p>
                                    <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $bug->description }}</p>
                                </div>
                                <div class="ml-4 flex-shrink-0 text-right">
                                    <p class="text-sm text-gray-500">{{ $bug->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bugs->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg aria-hidden="true" class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No bug reports yet</h3>
                    <p class="mt-2 text-gray-500">Bug reports will appear here.</p>
                    <a href="{{ route('admin.bugs.create') }}" class="mt-4 inline-block px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600 font-medium">
                        Report a Bug
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
