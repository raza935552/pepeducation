<x-admin-layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.messages.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gold-500 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Messages
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $message->subject }}</h1>
            </div>
            @php $badge = $message->status_badge; @endphp
            <span class="px-3 py-1.5 rounded-full text-sm font-medium
                {{ $badge['color'] === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                {{ $badge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                {{ $badge['color'] === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}">
                {{ $badge['label'] }}
            </span>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Message Content --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-gold-100 dark:bg-gold-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-lg font-bold text-gold-600 dark:text-gold-400">{{ strtoupper(substr($message->name, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $message->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message->email }}</p>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="mt-4 prose dark:prose-invert max-w-none">
                                {!! nl2br(e($message->message)) !!}
                            </div>
                        </div>
                    </div>
                </div>

                @if($message->admin_notes)
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-6 border border-yellow-200 dark:border-yellow-900/30">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Admin Notes</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $message->admin_notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar Actions --}}
            <div class="space-y-6">
                {{-- Update Status --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Update Status</h3>
                    <form action="{{ route('admin.messages.status', $message) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gold-500 focus:border-gold-500">
                                <option value="new" {{ $message->status === 'new' ? 'selected' : '' }}>New</option>
                                <option value="in_progress" {{ $message->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $message->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Admin Notes</label>
                            <textarea name="admin_notes" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gold-500 focus:border-gold-500">{{ $message->admin_notes }}</textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-gold-500 text-white rounded-lg hover:bg-gold-600 font-medium">
                            Update
                        </button>
                    </form>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="mailto:{{ $message->email }}" class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-gold-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Reply via Email
                        </a>
                        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center gap-2 text-red-600 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
