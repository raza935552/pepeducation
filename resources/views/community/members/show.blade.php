<x-app-layout>
    @push('head')
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
        <title>{{ $user->name }} — Community — Professor Peptides</title>
    @endpush

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="{ tab: '{{ $isSelf && $following->count() ? 'discussions' : 'discussions' }}' }">

        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('community.index') }}" class="hover:text-indigo-600">Community</a>
            <span class="mx-1">/</span><span class="text-gray-700">{{ $user->name }}</span>
        </nav>

        @if(session('status'))
            <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('status') }}</div>
        @endif

        {{-- Profile header --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 mb-6">
            <div class="flex items-start gap-4">
                <x-community.avatar :user="$user" :size="64" />
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <x-community.presence :user="$user" :showLabel="true" />
                    </div>
                    @if($user->bio)<p class="text-sm text-gray-600 mt-1">{{ $user->bio }}</p>@endif
                    <p class="text-xs text-gray-400 mt-1">Member since {{ $user->created_at->format('M Y') }}</p>
                </div>
            </div>
            <div class="flex gap-8 mt-5 text-sm border-t border-gray-100 pt-4">
                <div><span class="text-lg font-bold text-gray-900">{{ number_format($stats['threads']) }}</span> <span class="text-gray-500">discussions</span></div>
                <div><span class="text-lg font-bold text-gray-900">{{ number_format($stats['replies']) }}</span> <span class="text-gray-500">replies</span></div>
                <div><span class="text-lg font-bold text-gray-900">{{ number_format($stats['likes_received']) }}</span> <span class="text-gray-500">likes received</span></div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 mb-4 text-sm">
            <button @click="tab='discussions'" :class="tab==='discussions' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-3 py-1.5 rounded-full">Discussions</button>
            <button @click="tab='replies'" :class="tab==='replies' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-3 py-1.5 rounded-full">Replies</button>
            @if($isSelf)
                <button @click="tab='following'" :class="tab==='following' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100'" class="px-3 py-1.5 rounded-full">Following</button>
            @endif
        </div>

        {{-- Discussions --}}
        <div x-show="tab==='discussions'" class="rounded-xl border border-gray-200 bg-white divide-y divide-gray-100">
            @forelse($threads as $thread)
                @include('community.partials.thread-row', ['thread' => $thread])
            @empty
                <div class="p-8 text-center text-gray-500 text-sm">No discussions yet.</div>
            @endforelse
        </div>

        {{-- Replies --}}
        <div x-show="tab==='replies'" x-cloak class="space-y-2">
            @forelse($replies as $reply)
                <a href="{{ route('community.threads.show', $reply->thread) }}#reply-{{ $reply->id }}" class="block rounded-xl border border-gray-200 bg-white p-4 hover:bg-gray-50">
                    <p class="text-xs text-gray-400 mb-1">on <span class="font-medium text-gray-600">{{ $reply->thread?->title }}</span> · {{ $reply->created_at->diffForHumans() }}</p>
                    <p class="text-sm text-gray-700 line-clamp-2">{{ Str::limit(strip_tags($reply->body), 180) }}</p>
                </a>
            @empty
                <div class="p-8 text-center text-gray-500 text-sm rounded-xl border border-gray-200 bg-white">No replies yet.</div>
            @endforelse
        </div>

        {{-- Following (self only) --}}
        @if($isSelf)
            <div x-show="tab==='following'" x-cloak class="rounded-xl border border-gray-200 bg-white divide-y divide-gray-100">
                @forelse($following as $thread)
                    <a href="{{ route('community.threads.show', $thread) }}" class="flex items-center justify-between gap-3 p-4 hover:bg-gray-50">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $thread->title }}</p>
                            <p class="text-xs text-gray-400">{{ $thread->category?->name }} · {{ $thread->last_activity_at?->diffForHumans() }}</p>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-gray-500 text-sm">You're not following any discussions yet. Open a thread and hit <strong>Follow</strong> to get reply notifications.</div>
                @endforelse
            </div>
        @endif
    </div>
</x-app-layout>
