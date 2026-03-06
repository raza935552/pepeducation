<x-admin-layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.bugs.index') }}" class="text-sm text-gray-500 hover:text-gold-500 flex items-center gap-1">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Bug Reports
                </a>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $bugReport->title }}</h1>
            </div>
            <div class="flex items-center gap-2">
                @php $priorityBadge = $bugReport->priority_badge; @endphp
                <span class="px-3 py-1.5 rounded-full text-sm font-medium
                    {{ $priorityBadge['color'] === 'gray' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $priorityBadge['color'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $priorityBadge['color'] === 'orange' ? 'bg-orange-100 text-orange-800' : '' }}
                    {{ $priorityBadge['color'] === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ $priorityBadge['label'] }}
                </span>
                @php $statusBadge = $bugReport->status_badge; @endphp
                <span class="px-3 py-1.5 rounded-full text-sm font-medium
                    {{ $statusBadge['color'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $statusBadge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $statusBadge['color'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $statusBadge['color'] === 'gray' ? 'bg-gray-100 text-gray-800' : '' }}">
                    {{ $statusBadge['label'] }}
                </span>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Bug Details --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-gold-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-lg font-bold text-gold-600">{{ strtoupper(substr($bugReport->user?->name ?? '?', 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $bugReport->user?->name ?? 'Unknown' }}</h3>
                                    <p class="text-sm text-gray-500">{{ $bugReport->user?->email ?? '' }}</p>
                                </div>
                                <p class="text-sm text-gray-500">{{ $bugReport->created_at->format('M d, Y H:i') }}</p>
                            </div>

                            @if($bugReport->page_url)
                                <div class="mt-3 px-3 py-2 bg-gray-50 rounded-lg">
                                    <span class="text-xs font-medium text-gray-500 uppercase">Page URL</span>
                                    <p class="text-sm text-gray-700 font-mono">{{ $bugReport->page_url }}</p>
                                </div>
                            @endif

                            <div class="mt-4 prose max-w-none">
                                {!! nl2br(e($bugReport->description)) !!}
                            </div>

                            @if($bugReport->resolved_at)
                                <div class="mt-4 text-sm text-green-600">
                                    Resolved {{ $bugReport->resolved_at->format('M d, Y H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($bugReport->admin_notes)
                    <div class="bg-yellow-50 rounded-xl p-6 border border-yellow-200">
                        <h3 class="font-semibold text-gray-900 mb-2">Admin Notes</h3>
                        <p class="text-gray-700">{{ $bugReport->admin_notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar Actions --}}
            <div class="space-y-6">
                {{-- Update Status --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Update Status</h3>
                    <form action="{{ route('admin.bugs.status', $bugReport) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-gold-500 focus:border-gold-500">
                                <option value="reported" {{ $bugReport->status === 'reported' ? 'selected' : '' }}>Reported</option>
                                <option value="in_progress" {{ $bugReport->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="fixed" {{ $bugReport->status === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                <option value="closed" {{ $bugReport->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                            <textarea name="admin_notes" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-gold-500 focus:border-gold-500">{{ $bugReport->admin_notes }}</textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-gold-500 text-white rounded-lg hover:bg-gold-600 font-medium">
                            Update
                        </button>
                    </form>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <form action="{{ route('admin.bugs.destroy', $bugReport) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this bug report?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center gap-2 text-red-600 hover:text-red-700">
                                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Bug Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
