<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="font-semibold text-gray-900 dark:text-white">First Touch Attribution</h3>
    </div>
    <div class="p-4 space-y-3 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">Source</span>
            <span class="text-gray-900 dark:text-white font-medium">{{ $subscriber->source ?? 'Unknown' }}</span>
        </div>
        @if($subscriber->first_landing_page)
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">Landing Page</span>
                <span class="text-gray-900 dark:text-white font-medium truncate max-w-[200px]" title="{{ $subscriber->first_landing_page }}">
                    {{ Str::limit($subscriber->first_landing_page, 30) }}
                </span>
            </div>
        @endif
        @if($subscriber->first_utm_source)
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">UTM Source</span>
                <span class="text-gray-900 dark:text-white font-medium">{{ $subscriber->first_utm_source }}</span>
            </div>
        @endif
        @if($subscriber->first_utm_medium)
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">UTM Medium</span>
                <span class="text-gray-900 dark:text-white font-medium">{{ $subscriber->first_utm_medium }}</span>
            </div>
        @endif
        @if($subscriber->first_utm_campaign)
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">UTM Campaign</span>
                <span class="text-gray-900 dark:text-white font-medium">{{ $subscriber->first_utm_campaign }}</span>
            </div>
        @endif
        @if($subscriber->first_referrer)
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">Referrer</span>
                <span class="text-gray-900 dark:text-white font-medium truncate max-w-[200px]">{{ $subscriber->first_referrer }}</span>
            </div>
        @endif
        @if($subscriber->first_session_id)
            <div class="flex justify-between">
                <span class="text-gray-500 dark:text-gray-400">Session ID</span>
                <span class="text-gray-600 dark:text-gray-400 font-mono text-xs">{{ Str::limit($subscriber->first_session_id, 20) }}</span>
            </div>
        @endif

        {{-- Klaviyo Sync Status --}}
        <div class="pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <span class="text-gray-500 dark:text-gray-400">Klaviyo Status</span>
                @if($subscriber->klaviyo_id)
                    <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Synced
                    </span>
                @else
                    <span class="text-gray-400 dark:text-gray-500">Not synced</span>
                @endif
            </div>
            @if($subscriber->klaviyo_synced_at)
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 text-right">
                    Last sync: {{ $subscriber->klaviyo_synced_at->diffForHumans() }}
                </p>
            @endif
        </div>
    </div>
</div>
