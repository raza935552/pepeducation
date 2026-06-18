<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $categories = ForumCategory::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $recentThreads = ForumThread::published()
            ->with(['user', 'category', 'lastPoster'])
            ->withCount(['posts as solutions_count' => fn ($q) => $q->where('is_solution', true)])
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_activity_at')
            ->limit(8)
            ->get();

        $stats = [
            'threads' => ForumThread::published()->count(),
            'posts' => ForumPost::published()->count(),
            'members' => User::query()->count(),
        ];

        // Top contributors (real + seed) — light social proof on the index.
        $topMembers = User::where('forum_posts_count', '>', 0)
            ->orderByDesc('forum_posts_count')
            ->limit(6)
            ->get();

        return view('community.index', compact('categories', 'recentThreads', 'stats', 'topMembers'));
    }

    public function category(Request $request, ForumCategory $category)
    {
        abort_unless($category->is_active, 404);

        $sort = $request->query('sort', 'latest');

        $threads = $category->threads()
            ->published()
            ->with(['user', 'lastPoster'])
            ->withCount(['posts as solutions_count' => fn ($q) => $q->where('is_solution', true)])
            ->orderByDesc('is_pinned')
            ->when($sort === 'top', fn ($q) => $q->orderByDesc('likes_count')->orderByDesc('replies_count'))
            ->when($sort === 'unanswered', fn ($q) => $q->where('replies_count', 0)->orderByDesc('created_at'))
            ->when($sort === 'latest' || ! in_array($sort, ['top', 'unanswered']),
                fn ($q) => $q->orderByDesc('last_activity_at'))
            ->paginate(20)
            ->withQueryString();

        return view('community.category', compact('category', 'threads', 'sort'));
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $threads = collect();

        if (mb_strlen($q) >= 2) {
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';

            $byThread = ForumThread::published()
                ->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('body', 'like', $like))
                ->pluck('id');

            $byReply = ForumPost::published()->where('body', 'like', $like)->pluck('thread_id');

            $ids = $byThread->merge($byReply)->unique();

            $threads = ForumThread::published()
                ->whereIn('id', $ids)
                ->with(['user', 'category', 'lastPoster'])
                ->withCount(['posts as solutions_count' => fn ($qq) => $qq->where('is_solution', true)])
                ->orderByDesc('last_activity_at')
                ->limit(50)
                ->get();
        }

        return view('community.search', compact('q', 'threads'));
    }

    public function member(Request $request, User $user)
    {
        $isSelf = $request->user()?->id === $user->id;

        $threads = $user->forumThreads()
            ->published()
            ->with('category')
            ->withCount(['posts as solutions_count' => fn ($q) => $q->where('is_solution', true)])
            ->latest()
            ->limit(20)
            ->get();

        $replies = ForumPost::published()
            ->where('user_id', $user->id)
            ->with('thread.category')
            ->latest()
            ->limit(20)
            ->get();

        $following = $isSelf
            ? $user->forumSubscriptions()->with('thread.category')->latest()->get()->pluck('thread')->filter()
            : collect();

        $stats = [
            'threads' => $user->forumThreads()->published()->count(),
            'replies' => ForumPost::published()->where('user_id', $user->id)->count(),
            'likes_received' => ForumThread::where('user_id', $user->id)->sum('likes_count')
                + ForumPost::where('user_id', $user->id)->sum('likes_count'),
        ];

        return view('community.members.show', compact('user', 'threads', 'replies', 'following', 'stats', 'isSelf'));
    }
}
