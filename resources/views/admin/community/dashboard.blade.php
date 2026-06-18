<x-admin-layout>
    <x-slot name="header">Community Moderation</x-slot>

    @if(session('status'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">{{ $errors->first() }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        @foreach(['Discussions' => $stats['threads'], 'Replies' => $stats['posts'], 'Open reports' => $stats['open_reports'], 'Members' => $stats['members'], 'Seed personas' => $stats['seed_members']] as $label => $val)
            <div class="card text-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($val) }}</div>
                <div class="text-xs text-gray-500">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Settings + nav --}}
        <div class="space-y-6">
            <form action="{{ route('admin.community.settings') }}" method="POST" class="card h-fit">
                @csrf @method('PUT')
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Visibility</h3>
                <label class="flex items-center gap-3 mb-3">
                    <input type="checkbox" name="enabled" value="1" @checked($communityEnabled) class="rounded">
                    <span class="text-sm text-gray-700">Community is <strong>live</strong> (visible to members)</span>
                </label>
                <label class="flex items-center gap-3 mb-4">
                    <input type="checkbox" name="drip_enabled" value="1" @checked($dripEnabled) class="rounded">
                    <span class="text-sm text-gray-700">Enable seed-content drip scheduler</span>
                </label>
                <p class="text-xs text-gray-500 mb-4">While off, only admins can see <code>/community</code> — so you can seed &amp; preview before launch.</p>
                <button class="btn-primary w-full">Save settings</button>
            </form>

            <a href="{{ route('admin.community.categories.index') }}" class="card block hover:bg-gray-50 transition">
                <h3 class="text-base font-semibold text-gray-900">Manage categories →</h3>
                <p class="text-sm text-gray-500">Create and organise discussion categories.</p>
            </a>
        </div>

        {{-- Reports queue --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Open reports ({{ $stats['open_reports'] }})</h3>
                @forelse($reports as $report)
                    @php($item = $report->reportable)
                    <div class="border border-gray-200 rounded-lg p-3 mb-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <span class="text-xs font-semibold uppercase tracking-wide text-red-600">{{ $report->reason }}</span>
                                <p class="text-sm text-gray-800 mt-1 line-clamp-2">
                                    @if($item instanceof \App\Models\ForumThread)
                                        🧵 {{ $item->title }}
                                    @elseif($item instanceof \App\Models\ForumPost)
                                        💬 {{ Str::limit(strip_tags($item->body), 120) }}
                                    @else
                                        <em class="text-gray-400">[deleted]</em>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 mt-1">reported by {{ $report->reporter?->name ?? 'unknown' }} · {{ $report->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @if($item instanceof \App\Models\ForumThread)
                                <form method="POST" action="{{ route('admin.community.threads.action', $item) }}">@csrf
                                    <input type="hidden" name="action" value="hide">
                                    <button class="text-xs px-2 py-1 rounded bg-amber-100 text-amber-700">Hide thread</button></form>
                                <form method="POST" action="{{ route('admin.community.threads.action', $item) }}">@csrf
                                    <input type="hidden" name="action" value="delete">
                                    <button class="text-xs px-2 py-1 rounded bg-red-100 text-red-700" onclick="return confirm('Delete thread?')">Delete thread</button></form>
                            @elseif($item instanceof \App\Models\ForumPost)
                                <form method="POST" action="{{ route('admin.community.posts.action', $item) }}">@csrf
                                    <input type="hidden" name="action" value="hide">
                                    <button class="text-xs px-2 py-1 rounded bg-amber-100 text-amber-700">Hide reply</button></form>
                                <form method="POST" action="{{ route('admin.community.posts.action', $item) }}">@csrf
                                    <input type="hidden" name="action" value="delete">
                                    <button class="text-xs px-2 py-1 rounded bg-red-100 text-red-700" onclick="return confirm('Delete reply?')">Delete reply</button></form>
                            @endif
                            <form method="POST" action="{{ route('admin.community.reports.resolve', $report) }}">@csrf
                                <input type="hidden" name="status" value="actioned">
                                <button class="text-xs px-2 py-1 rounded bg-green-100 text-green-700">Mark actioned</button></form>
                            <form method="POST" action="{{ route('admin.community.reports.resolve', $report) }}">@csrf
                                <input type="hidden" name="status" value="dismissed">
                                <button class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600">Dismiss</button></form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No open reports. 🎉</p>
                @endforelse
            </div>

            {{-- Recent threads --}}
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent discussions</h3>
                <div class="divide-y divide-gray-100">
                    @forelse($recentThreads as $thread)
                        <div class="flex items-center justify-between gap-3 py-2">
                            <div class="min-w-0">
                                <a href="{{ route('community.threads.show', $thread) }}" target="_blank" class="text-sm font-medium text-gray-900 hover:text-admin-primary-600 truncate block">
                                    {{ $thread->title }}
                                    @if($thread->status !== 'published')<span class="text-xs text-amber-600">({{ $thread->status }})</span>@endif
                                </a>
                                <p class="text-xs text-gray-400">{{ $thread->category?->name }} · {{ $thread->user?->name }} · {{ $thread->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <form method="POST" action="{{ route('admin.community.threads.action', $thread) }}">@csrf
                                    <input type="hidden" name="action" value="{{ $thread->is_pinned ? 'unpin' : 'pin' }}">
                                    <button class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600" title="Pin">{{ $thread->is_pinned ? '📌' : 'Pin' }}</button></form>
                                <form method="POST" action="{{ route('admin.community.threads.action', $thread) }}">@csrf
                                    <input type="hidden" name="action" value="{{ $thread->is_locked ? 'unlock' : 'lock' }}">
                                    <button class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600" title="Lock">{{ $thread->is_locked ? '🔓' : '🔒' }}</button></form>
                                <form method="POST" action="{{ route('admin.community.threads.action', $thread) }}">@csrf
                                    <input type="hidden" name="action" value="delete">
                                    <button class="text-xs px-2 py-1 rounded bg-red-50 text-red-600" onclick="return confirm('Delete thread?')">✕</button></form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No discussions yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
