<x-admin-layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.contributions.index') }}" class="text-sm text-gray-500 hover:text-gold-500 flex items-center gap-1">
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Contributions
                </a>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">Review Contribution</h1>
            </div>
            @php $badge = $contribution->status_badge; @endphp
            <span class="px-3 py-1.5 rounded-full text-sm font-medium
                {{ $badge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $badge['color'] === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $badge['color'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                {{ $badge['color'] === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                {{ $badge['label'] }}
            </span>
        </div>

        {{-- Contribution Info --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Submitted By</h3>
                    <p class="mt-1 text-gray-900">{{ $contribution->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $contribution->user->email }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Peptide</h3>
                    <p class="mt-1 text-gray-900">{{ $contribution->peptide->name }}</p>
                    <a href="{{ route('peptides.show', $contribution->peptide) }}" target="_blank" rel="noopener" class="text-sm text-gold-500 hover:text-gold-600">View peptide →</a>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Section</h3>
                    <p class="mt-1 text-gray-900">{{ $contribution->section_label }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Submitted</h3>
                    <p class="mt-1 text-gray-900">{{ $contribution->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            @if($contribution->edit_reason)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500">Reason for Edit</h3>
                    <p class="mt-1 text-gray-900">{{ $contribution->edit_reason }}</p>
                </div>
            @endif
        </div>

        {{-- Diff View --}}
        <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                    Original Content
                </h3>
                <div class="prose max-w-none text-sm bg-red-50 p-4 rounded-lg border border-red-200">
                    {!! nl2br(e($contribution->original_content)) !!}
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                    Proposed Changes
                </h3>
                <div class="prose max-w-none text-sm bg-green-50 p-4 rounded-lg border border-green-200">
                    {!! nl2br(e($contribution->new_content)) !!}
                </div>
            </div>
        </div>

        {{-- Actions --}}
        @if($contribution->status === 'pending')
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Review Actions</h3>

                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Approve --}}
                    <form action="{{ route('admin.contributions.approve', $contribution) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                            <textarea name="reviewer_notes" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500" placeholder="Add notes for the contributor..."></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 font-medium">
                            Approve & Publish
                        </button>
                    </form>

                    {{-- Reject --}}
                    <form action="{{ route('admin.contributions.reject', $contribution) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason (required)</label>
                            <textarea name="reviewer_notes" rows="3" required class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="Explain why this was rejected..."></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
        @else
            {{-- Review Info --}}
            @if($contribution->reviewer)
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Review Details</h3>
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm text-gray-500">Reviewed by:</span>
                            <span class="ml-2 text-gray-900">{{ $contribution->reviewer->name }}</span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Reviewed at:</span>
                            <span class="ml-2 text-gray-900">{{ $contribution->reviewed_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($contribution->reviewer_notes)
                            <div>
                                <span class="text-sm text-gray-500">Notes:</span>
                                <p class="mt-1 text-gray-900">{{ $contribution->reviewer_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-admin-layout>
