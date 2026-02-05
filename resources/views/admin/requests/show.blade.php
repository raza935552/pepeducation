<x-admin-layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.requests.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gold-500 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Requests
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $peptideRequest->peptide_name }}</h1>
            </div>
            @php $badge = $peptideRequest->status_badge; @endphp
            <span class="px-3 py-1.5 rounded-full text-sm font-medium
                {{ $badge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                {{ $badge['color'] === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                {{ $badge['color'] === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                {{ $badge['color'] === 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                {{ $badge['label'] }}
            </span>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Request Details --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Request Details</h3>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Requested By</span>
                            @if($peptideRequest->user)
                                <p class="text-gray-900 dark:text-white">{{ $peptideRequest->user->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $peptideRequest->user->email }}</p>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">Anonymous</p>
                            @endif
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Submitted</span>
                            <p class="text-gray-900 dark:text-white">{{ $peptideRequest->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    @if($peptideRequest->notes)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Additional Notes</span>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $peptideRequest->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Source Links --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Source Links</h3>
                    @if($peptideRequest->source_links && count($peptideRequest->source_links) > 0)
                        <ul class="space-y-3">
                            @foreach($peptideRequest->source_links as $link)
                                <li>
                                    <a href="{{ $link }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-gold-600 hover:text-gold-700 dark:text-gold-400 dark:hover:text-gold-300">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        <span class="truncate">{{ $link }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No source links provided.</p>
                    @endif
                </div>

                @if($peptideRequest->rejection_reason)
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-6 border border-red-200 dark:border-red-900/30">
                        <h3 class="font-semibold text-red-800 dark:text-red-300 mb-2">Rejection Reason</h3>
                        <p class="text-red-700 dark:text-red-400">{{ $peptideRequest->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar Actions --}}
            <div class="space-y-6">
                {{-- Update Status --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Update Status</h3>
                    <form action="{{ route('admin.requests.status', $peptideRequest) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <select name="status" id="status-select" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gold-500 focus:border-gold-500">
                                <option value="pending" {{ $peptideRequest->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $peptideRequest->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="published" {{ $peptideRequest->status === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="rejected" {{ $peptideRequest->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div id="rejection-reason" class="{{ $peptideRequest->status !== 'rejected' ? 'hidden' : '' }}">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rejection Reason</label>
                            <textarea name="rejection_reason" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gold-500 focus:border-gold-500">{{ $peptideRequest->rejection_reason }}</textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-gold-500 text-white rounded-lg hover:bg-gold-600 font-medium">
                            Update
                        </button>
                    </form>
                    <script>
                        document.getElementById('status-select').addEventListener('change', function() {
                            document.getElementById('rejection-reason').classList.toggle('hidden', this.value !== 'rejected');
                        });
                    </script>
                </div>

                {{-- Actions --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.peptides.create') }}" class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-gold-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Peptide
                        </a>
                        <form action="{{ route('admin.requests.destroy', $peptideRequest) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center gap-2 text-red-600 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
