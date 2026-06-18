<x-app-layout>
    @push('head')
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
        <title>{{ $category->name }} — Community — Professor Peptides</title>
    @endpush

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('community.index') }}" class="hover:text-indigo-600">Community</a>
            <span class="mx-1">/</span>
            <span class="text-gray-700">{{ $category->name }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center h-12 w-12 rounded-lg text-2xl" style="background: {{ $category->color ?? '#EEF2FF' }}20;">{{ $category->icon ?? '💬' }}</span>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                    @if($category->description)<p class="text-gray-500 text-sm">{{ $category->description }}</p>@endif
                </div>
            </div>
            <a href="{{ route('community.threads.create', ['category' => $category->id]) }}"
               class="inline-flex items-center justify-center rounded-full bg-indigo-600 text-white font-semibold px-5 py-2.5 hover:bg-indigo-700 transition shrink-0">
                + New discussion
            </a>
        </div>

        @if(session('status'))
            <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('status') }}</div>
        @endif

        <div class="mb-4">@include('community.partials.search-bar')</div>

        {{-- Sort tabs --}}
        <div class="flex gap-1 mb-4 text-sm">
            @foreach(['latest' => 'Latest', 'top' => 'Top', 'unanswered' => 'Unanswered'] as $key => $label)
                <a href="{{ route('community.category', [$category, 'sort' => $key]) }}"
                   class="px-3 py-1.5 rounded-full {{ $sort === $key ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">{{ $label }}</a>
            @endforeach
        </div>

        <div class="rounded-xl border border-gray-200 bg-white divide-y divide-gray-100">
            @forelse($threads as $thread)
                @include('community.partials.thread-row', ['thread' => $thread])
            @empty
                <div class="p-10 text-center text-gray-500">
                    No discussions in this category yet.
                    <a href="{{ route('community.threads.create', ['category' => $category->id]) }}" class="text-indigo-600 font-medium">Start one →</a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $threads->links() }}</div>
    </div>
</x-app-layout>
