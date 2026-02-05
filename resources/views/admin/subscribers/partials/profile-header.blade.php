<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            {{-- Avatar --}}
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-2xl">
                {{ strtoupper(substr($subscriber->email, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subscriber->email }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    {{-- Status Badge --}}
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $subscriber->status === 'active' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                        {{ ucfirst($subscriber->status) }}
                    </span>
                    {{-- Segment Badge --}}
                    @if($subscriber->segment)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $subscriber->segment === 'BOF' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' :
                               ($subscriber->segment === 'MOF' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' :
                               'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400') }}">
                            {{ strtoupper($subscriber->segment) }}
                        </span>
                    @endif
                    {{-- Tier Badge --}}
                    @if($subscriber->engagement_tier)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $subscriber->engagement_tier === 'hot' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400' :
                               ($subscriber->engagement_tier === 'warm' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' :
                               'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                            {{ ucfirst($subscriber->engagement_tier) }} Lead
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-right text-sm text-gray-500 dark:text-gray-400">
            <p>Subscribed {{ $subscriber->subscribed_at?->diffForHumans() }}</p>
            @if($subscriber->last_activity_at)
                <p>Last active {{ $subscriber->last_activity_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>
</div>
