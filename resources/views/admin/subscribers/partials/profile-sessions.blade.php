<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200">
        <h3 class="font-semibold text-gray-900">Recent Sessions</h3>
    </div>
    <div class="divide-y divide-gray-200">
        @forelse($subscriber->sessions()->latest()->take(5)->get() as $session)
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $session->page_views ?? 0 }} pages viewed
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $session->device_type ?? 'Unknown' }} / {{ $session->browser ?? 'Unknown' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">
                            {{ $session->created_at->diffForHumans() }}
                        </p>
                        @if($session->duration_seconds)
                            <p class="text-xs text-gray-400">
                                {{ gmdate('i:s', $session->duration_seconds) }} duration
                            </p>
                        @endif
                    </div>
                </div>
                @if($session->landing_page)
                    <p class="text-xs text-gray-400 mt-2 truncate">
                        {{ $session->landing_page }}
                    </p>
                @endif
            </div>
        @empty
            <div class="p-4 text-center text-sm text-gray-500">
                No sessions recorded
            </div>
        @endforelse
    </div>
</div>
