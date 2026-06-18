<x-app-layout>
    @push('head')
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
        <title>{{ $thread->title }} — Community — Professor Peptides</title>
    @endpush

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('community.index') }}" class="hover:text-indigo-600">Community</a>
            <span class="mx-1">/</span>
            <a href="{{ route('community.category', $thread->category) }}" class="hover:text-indigo-600">{{ $thread->category->name }}</a>
        </nav>

        @if(session('status'))
            <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('status') }}</div>
        @endif

        <div class="flex items-start justify-between gap-4 mb-4">
            <div class="flex items-start gap-2">
                @if($thread->is_pinned)<span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded mt-1">📌 Pinned</span>@endif
                @if($thread->is_locked)<span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded mt-1">🔒 Locked</span>@endif
                <h1 class="text-2xl font-bold text-gray-900">{{ $thread->title }}</h1>
            </div>
            <form method="POST" action="{{ route('community.subscribe', $thread) }}" class="shrink-0">
                @csrf
                <button class="inline-flex items-center gap-1.5 rounded-full border px-4 py-2 text-sm font-medium transition
                    {{ $isSubscribed ? 'border-indigo-200 bg-indigo-50 text-indigo-700' : 'border-gray-300 text-gray-600 hover:border-indigo-300' }}">
                    {{ $isSubscribed ? '✓ Following' : '+ Follow' }}
                </button>
            </form>
        </div>

        {{-- Original post --}}
        <article class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <x-community.avatar :user="$thread->user" :size="44" />
                <div>
                    <a href="{{ route('community.members.show', $thread->user) }}" class="font-semibold text-gray-900 hover:text-indigo-600">{{ $thread->user->name }}</a>
                    <div class="text-xs text-gray-500">{{ $thread->created_at->diffForHumans() }}@if($thread->user->credentials) · {{ $thread->user->credentials }}@endif</div>
                </div>
                @can('update', $thread)
                    <div class="ml-auto flex items-center gap-2 text-sm">
                        <a href="{{ route('community.threads.edit', $thread) }}" class="text-gray-500 hover:text-indigo-600">Edit</a>
                        <form method="POST" action="{{ route('community.threads.destroy', $thread) }}" onsubmit="return confirm('Delete this discussion?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-500 hover:text-rose-600">Delete</button>
                        </form>
                    </div>
                @endcan
            </div>
            <div class="prose prose-sm max-w-none prose-a:text-indigo-600">{!! $thread->body !!}</div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <x-community.actions type="thread" :id="$thread->id" :likes="$thread->likes_count" :liked="$likedThread" />
            </div>
        </article>

        {{-- Replies --}}
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ number_format($thread->replies_count) }} {{ Str::plural('Reply', $thread->replies_count) }}</h2>

        @php($canAccept = auth()->id() === $thread->user_id || auth()->user()?->isAdmin())
        <div class="space-y-4 mb-8">
            @foreach($posts as $post)
                <article id="reply-{{ $post->id }}"
                         class="rounded-xl border p-5 {{ $post->is_solution ? 'border-emerald-300 ring-1 ring-emerald-300 bg-emerald-50/40' : 'border-gray-200 bg-white' }}">
                    @if($post->is_solution)
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700 mb-2">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            Accepted answer
                        </div>
                    @endif
                    <div class="flex items-center gap-3 mb-3">
                        <x-community.avatar :user="$post->user" :size="36" />
                        <div>
                            <a href="{{ route('community.members.show', $post->user) }}" class="font-semibold text-gray-900 text-sm hover:text-indigo-600">{{ $post->user->name }}</a>
                            <div class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}@if($post->edited_at) · edited @endif</div>
                        </div>
                        <div class="ml-auto flex items-center gap-3">
                            @if($canAccept)
                                <form method="POST" action="{{ route('community.replies.solution', $post) }}">
                                    @csrf
                                    <button class="text-xs font-medium {{ $post->is_solution ? 'text-emerald-700' : 'text-gray-400 hover:text-emerald-700' }}">
                                        {{ $post->is_solution ? '✓ Answer' : 'Mark as answer' }}
                                    </button>
                                </form>
                            @endif
                            @can('update', $post)
                                <form method="POST" action="{{ route('community.replies.destroy', $post) }}" onsubmit="return confirm('Delete this reply?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-gray-400 hover:text-rose-600">Delete</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                    <div class="prose prose-sm max-w-none prose-a:text-indigo-600">{!! $post->body !!}</div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <x-community.actions type="post" :id="$post->id" :likes="$post->likes_count" :liked="in_array($post->id, $likedPostIds)" />
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mb-8">{{ $posts->links() }}</div>

        {{-- Reply form --}}
        @if($thread->is_locked)
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 text-center text-gray-500">🔒 This discussion is locked. New replies are disabled.</div>
        @else
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Add a reply</h3>
                @error('body')<p class="text-sm text-rose-600 mb-2">{{ $message }}</p>@enderror
                <form method="POST" action="{{ route('community.replies.store', $thread) }}">
                    @csrf
                    <textarea name="body" rows="5" required minlength="2" maxlength="20000"
                              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Share your thoughts… Be respectful and keep it research-focused.">{{ old('body') }}</textarea>
                    <div class="flex items-center justify-between mt-3">
                        <p class="text-xs text-gray-400">Educational discussion only — no medical advice.</p>
                        <button class="rounded-full bg-indigo-600 text-white font-semibold px-6 py-2.5 hover:bg-indigo-700 transition">Post reply</button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
