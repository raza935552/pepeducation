@php($answered = ($thread->solutions_count ?? 0) > 0)
<a href="{{ route('community.threads.show', $thread) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition">
    <x-community.avatar :user="$thread->user" :size="40" />
    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-2">
            @if($thread->is_pinned)<span class="text-[11px] font-semibold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">📌 Pinned</span>@endif
            @if($answered)<span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">✓ Answered</span>@endif
            @if($thread->is_locked)<span class="text-[11px] font-semibold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">🔒</span>@endif
            <h3 class="font-semibold text-gray-900 truncate">{{ $thread->title }}</h3>
        </div>
        <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1.5">
            <x-community.presence :user="$thread->user" />
            <span>{{ $thread->user->name }}</span>
            @isset($thread->category)<span>· {{ $thread->category->name }}</span>@endisset
            <span>· {{ $thread->last_activity_at?->diffForHumans() }}</span>
        </p>
    </div>
    <div class="hidden sm:flex items-center gap-4 text-xs text-gray-400 shrink-0">
        <span class="inline-flex items-center gap-1" title="Replies">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.7 9.7 0 01-4-.85L3 20l1.4-3.5A7.6 7.6 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            {{ number_format($thread->replies_count) }}
        </span>
        <span class="inline-flex items-center gap-1" title="Likes">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            {{ number_format($thread->likes_count) }}
        </span>
        <span class="inline-flex items-center gap-1" title="Views">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            {{ number_format($thread->views_count) }}
        </span>
    </div>
</a>
