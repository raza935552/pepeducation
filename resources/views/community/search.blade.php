<x-app-layout>
    @push('head')
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
        <title>Search — Community — Professor Peptides</title>
    @endpush

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('community.index') }}" class="hover:text-indigo-600">Community</a>
            <span class="mx-1">/</span><span class="text-gray-700">Search</span>
        </nav>

        <div class="mb-6">@include('community.partials.search-bar')</div>

        @if(mb_strlen($q) < 2)
            <p class="text-gray-500 text-sm">Type at least 2 characters to search discussions and replies.</p>
        @else
            <p class="text-sm text-gray-500 mb-3">{{ $threads->count() }} result{{ $threads->count() === 1 ? '' : 's' }} for <span class="font-semibold text-gray-700">"{{ $q }}"</span></p>
            <div class="rounded-xl border border-gray-200 bg-white divide-y divide-gray-100">
                @forelse($threads as $thread)
                    @include('community.partials.thread-row', ['thread' => $thread])
                @empty
                    <div class="p-10 text-center text-gray-500">
                        No discussions matched. Try different keywords, or
                        <a href="{{ route('community.threads.create') }}" class="text-indigo-600 font-medium">start a new discussion →</a>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</x-app-layout>
