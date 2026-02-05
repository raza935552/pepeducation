<x-admin-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Contact Messages</h1>
        </div>

        {{-- Status Tabs --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.messages.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-gold-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                All ({{ array_sum($counts) }})
            </a>
            <a href="{{ route('admin.messages.index', ['status' => 'new']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'new' ? 'bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                New ({{ $counts['new'] }})
            </a>
            <a href="{{ route('admin.messages.index', ['status' => 'in_progress']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'in_progress' ? 'bg-yellow-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                In Progress ({{ $counts['in_progress'] }})
            </a>
            <a href="{{ route('admin.messages.index', ['status' => 'resolved']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'resolved' ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Resolved ({{ $counts['resolved'] }})
            </a>
        </div>

        {{-- Search --}}
        <form class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name, email, or subject..."
                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gold-500 focus:border-gold-500">
            <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-lg hover:bg-gold-600">Search</button>
        </form>

        {{-- Messages List --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            @if($messages->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($messages as $message)
                        <a href="{{ route('admin.messages.show', $message) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700/50 p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $message->name }}</span>
                                        @php $badge = $message->status_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $badge['color'] === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                            {{ $badge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                            {{ $badge['color'] === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message->email }}</p>
                                    <p class="mt-2 font-medium text-gray-800 dark:text-gray-200">{{ $message->subject }}</p>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $message->message }}</p>
                                </div>
                                <div class="ml-4 flex-shrink-0 text-right">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $messages->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No messages yet</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Contact messages will appear here.</p>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
