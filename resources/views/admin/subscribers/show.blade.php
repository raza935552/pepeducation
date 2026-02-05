<x-admin-layout>
    <x-slot name="title">Subscriber Details</x-slot>

    <div class="max-w-2xl space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.subscribers.index') }}"
               class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Subscribers
            </a>
            <form action="{{ route('admin.subscribers.destroy', $subscriber) }}" method="POST"
                  onsubmit="return confirm('Delete this subscriber?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-red-600 hover:text-red-700 border border-red-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    Delete Subscriber
                </button>
            </form>
        </div>

        {{-- Main Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($subscriber->email, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $subscriber->email }}</h2>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $subscriber->status === 'active' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                            {{ ucfirst($subscriber->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Details --}}
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Source</label>
                        <p class="mt-1 text-gray-900 dark:text-white">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                {{ $subscriber->source === 'popup' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                                {{ ucfirst($subscriber->source) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Subscribed At</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $subscriber->subscribed_at->format('F j, Y g:i A') }}</p>
                    </div>
                </div>

                @if($subscriber->unsubscribed_at)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unsubscribed At</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $subscriber->unsubscribed_at->format('F j, Y g:i A') }}</p>
                    </div>
                @endif

                @if($subscriber->ip_address)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">IP Address</label>
                        <p class="mt-1 text-gray-900 dark:text-white font-mono text-sm">{{ $subscriber->ip_address }}</p>
                    </div>
                @endif

                @if($subscriber->user_agent)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">User Agent</label>
                        <p class="mt-1 text-gray-600 dark:text-gray-400 text-sm break-all">{{ $subscriber->user_agent }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Created</label>
                        <p class="mt-1 text-gray-600 dark:text-gray-400 text-sm">{{ $subscriber->created_at->format('M j, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Last Updated</label>
                        <p class="mt-1 text-gray-600 dark:text-gray-400 text-sm">{{ $subscriber->updated_at->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
