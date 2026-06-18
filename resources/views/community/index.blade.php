@php($noindex = true)
<x-app-layout>
    @push('head')
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
        <title>Community — Professor Peptides</title>
    @endpush

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Hero --}}
        <div class="rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 text-white p-8 sm:p-10 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div>
                    <p class="text-indigo-200 text-sm font-medium uppercase tracking-wide">Members only · Private</p>
                    <h1 class="text-3xl font-bold mt-1">The Research Community</h1>
                    <p class="text-indigo-100 mt-2 max-w-xl">Ask questions, share findings, and learn alongside other researchers. Discussions here are private to members and never indexed by search engines.</p>
                </div>
                <a href="{{ route('community.threads.create') }}"
                   class="inline-flex items-center justify-center rounded-full bg-white text-indigo-700 font-semibold px-6 py-3 hover:bg-indigo-50 transition shrink-0">
                    + Start a discussion
                </a>
            </div>
            <div class="flex gap-8 mt-7 text-sm">
                <div><span class="text-2xl font-bold">{{ number_format($stats['threads']) }}</span><span class="block text-indigo-200">Discussions</span></div>
                <div><span class="text-2xl font-bold">{{ number_format($stats['posts']) }}</span><span class="block text-indigo-200">Replies</span></div>
                <div><span class="text-2xl font-bold">{{ number_format($stats['members']) }}</span><span class="block text-indigo-200">Members</span></div>
            </div>
        </div>

        @if(session('status'))
            <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('status') }}</div>
        @endif

        {{-- Search --}}
        <div class="mb-6 max-w-xl">@include('community.partials.search-bar')</div>

        {{-- New-member intro nudge --}}
        @if((auth()->user()->forum_posts_count ?? 0) === 0 && auth()->user()->forumThreads()->doesntExist())
            <div class="mb-6 rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-4 flex items-center justify-between gap-4">
                <p class="text-sm text-indigo-900">👋 New here? Introduce yourself and say what you're researching — the community is friendly.</p>
                <a href="{{ route('community.threads.create') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900 shrink-0">Say hello →</a>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Categories --}}
            <div class="lg:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Categories</h2>
                <div class="space-y-3">
                    @forelse($categories as $category)
                        <a href="{{ route('community.category', $category) }}"
                           class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 hover:border-indigo-300 hover:shadow-sm transition">
                            <span class="flex items-center justify-center h-12 w-12 rounded-lg text-2xl shrink-0"
                                  style="background: {{ $category->color ?? '#EEF2FF' }}20;">
                                {{ $category->icon ?? '💬' }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $category->name }}</h3>
                                @if($category->description)
                                    <p class="text-sm text-gray-500 line-clamp-1">{{ $category->description }}</p>
                                @endif
                            </div>
                            <div class="text-right text-sm text-gray-400 shrink-0">
                                <div class="font-semibold text-gray-700">{{ number_format($category->threads_count) }}</div>
                                <div>threads</div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-500">
                            No categories yet.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent activity --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent activity</h2>
                <div class="rounded-xl border border-gray-200 bg-white divide-y divide-gray-100">
                    @forelse($recentThreads as $thread)
                        <a href="{{ route('community.threads.show', $thread) }}" class="flex items-start gap-3 p-4 hover:bg-gray-50 transition">
                            <x-community.avatar :user="$thread->lastPoster ?? $thread->user" :size="36" />
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 line-clamp-2">{{ $thread->title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $thread->category->name }} · {{ $thread->last_activity_at?->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-sm text-gray-500">Nothing yet — be the first to post!</div>
                    @endforelse
                </div>

                {{-- Top contributors --}}
                @if($topMembers->isNotEmpty())
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 mt-8">Top contributors</h2>
                    <div class="rounded-xl border border-gray-200 bg-white divide-y divide-gray-100">
                        @foreach($topMembers as $member)
                            <a href="{{ route('community.members.show', $member) }}" class="flex items-center gap-3 p-3 hover:bg-gray-50 transition">
                                <x-community.avatar :user="$member" :size="32" />
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $member->name }}</p>
                                </div>
                                <span class="text-xs text-gray-400 shrink-0">{{ number_format($member->forum_posts_count) }} posts</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
