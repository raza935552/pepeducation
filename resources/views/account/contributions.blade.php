<x-account-layout>
    <div class="space-y-6">
        <div class="bg-white dark:bg-brown-800 rounded-xl shadow-sm border border-cream-200 dark:border-brown-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-cream-100">Your Contributions</h2>
                    <p class="text-sm text-gray-500 dark:text-cream-400 mt-1">Edit suggestions you've submitted</p>
                </div>
                <span class="text-sm text-gray-500 dark:text-cream-400">{{ $contributions->total() }} total</span>
            </div>

            @if($contributions->isEmpty())
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-cream-100 dark:bg-brown-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-cream-100 mb-2">No contributions yet</h3>
                    <p class="text-gray-500 dark:text-cream-400 mb-6">Help improve our peptide information by suggesting edits.</p>
                    <a href="{{ route('peptides.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gold-500 text-white rounded-full font-medium hover:bg-gold-600 transition-colors">
                        Browse Peptides
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($contributions as $contribution)
                        @php $badge = $contribution->status_badge; @endphp
                        <div class="border border-cream-200 dark:border-brown-700 rounded-lg p-4 hover:border-gold-300 dark:hover:border-gold-700 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <a href="{{ route('peptides.show', $contribution->peptide) }}" class="font-medium text-gray-900 dark:text-cream-100 hover:text-gold-600 dark:hover:text-gold-400">
                                            {{ $contribution->peptide->name }}
                                        </a>
                                        <span class="text-gray-400">·</span>
                                        <span class="text-sm text-gray-500 dark:text-cream-400 capitalize">{{ str_replace('_', ' ', $contribution->section) }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-cream-400 line-clamp-2">
                                        {{ Str::limit($contribution->new_content, 150) }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-cream-500 mt-2">
                                        Submitted {{ $contribution->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $badge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                    {{ $badge['color'] === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $badge['color'] === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    {{ $badge['color'] === 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                                    {{ $badge['label'] }}
                                </span>
                            </div>

                            @if($contribution->status === 'rejected' && $contribution->reviewer_notes)
                                <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-100 dark:border-red-900/30">
                                    <p class="text-sm text-red-700 dark:text-red-300">
                                        <span class="font-medium">Reviewer feedback:</span> {{ $contribution->reviewer_notes }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $contributions->links() }}
                </div>
            @endif
        </div>

        {{-- Status Legend --}}
        <div class="bg-cream-100 dark:bg-brown-700 rounded-xl p-4">
            <h4 class="text-sm font-medium text-gray-900 dark:text-cream-100 mb-3">Status Guide</h4>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                    <span class="text-gray-600 dark:text-cream-400">Pending - Awaiting review</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span class="text-gray-600 dark:text-cream-400">Under Review - Being evaluated</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-gray-600 dark:text-cream-400">Approved - Changes applied</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span class="text-gray-600 dark:text-cream-400">Rejected - Not accepted</span>
                </div>
            </div>
        </div>
    </div>
</x-account-layout>
