<x-account-layout>
    <div class="bg-white rounded-xl shadow-sm border border-cream-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">
                Saved Peptides
            </h2>
            <span class="text-sm text-gray-500">{{ $bookmarks->total() }} saved</span>
        </div>

        @if($bookmarks->count() > 0)
            <div class="space-y-3">
                @foreach($bookmarks as $bookmark)
                    <div class="flex items-center justify-between p-4 bg-cream-50 rounded-xl hover:bg-cream-100 transition-colors">
                        <a href="{{ route('peptides.show', $bookmark->peptide) }}" class="flex items-center gap-4 flex-1">
                            <span class="text-xs font-mono bg-gold-100 text-gold-700 px-2.5 py-1 rounded-lg">
                                {{ $bookmark->peptide->abbreviation ?? 'N/A' }}
                            </span>
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $bookmark->peptide->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Saved {{ $bookmark->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                        <form action="{{ route('bookmarks.toggle', $bookmark->peptide) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Remove bookmark">
                                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($bookmarks->hasPages())
                <div class="mt-6">
                    {{ $bookmarks->links() }}
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-cream-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg aria-hidden="true" class="w-8 h-8 text-cream-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No bookmarks yet</h3>
                <p class="text-gray-500 mb-6">Browse peptides and bookmark the ones you're interested in</p>
                <a href="{{ route('peptides.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gold-500 text-white rounded-full font-medium text-sm hover:bg-gold-600 transition-colors">
                    Browse Peptides
                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</x-account-layout>
