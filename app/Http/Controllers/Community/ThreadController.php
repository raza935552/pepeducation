<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Services\ForumContentSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ThreadController extends Controller
{
    public function __construct(private ForumContentSanitizer $sanitizer)
    {
    }

    public function create(Request $request)
    {
        $this->authorize('create', ForumThread::class);

        $categories = ForumCategory::active()->orderBy('sort_order')->orderBy('name')->get();
        $selected = $request->query('category');

        return view('community.threads.create', compact('categories', 'selected'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', ForumThread::class);

        $key = 'forum-thread:' . $request->user()->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withInput()->withErrors([
                'body' => 'You are posting too fast. Please wait a moment and try again.',
            ]);
        }

        $data = $request->validate([
            'category_id' => ['required', 'exists:forum_categories,id'],
            'title' => ['required', 'string', 'min:8', 'max:160'],
            'body' => ['required', 'string', 'min:15', 'max:20000'],
        ]);

        $body = $this->sanitizer->sanitize($data['body']);
        abort_if(trim(strip_tags($body)) === '', 422, 'Post content is empty after sanitisation.');

        $thread = ForumThread::create([
            'category_id' => $data['category_id'],
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'slug' => ForumThread::generateUniqueSlug($data['title']),
            'body' => $body,
            'status' => 'published',
            'last_activity_at' => now(),
            'last_post_user_id' => $request->user()->id,
        ]);

        $thread->category()->increment('threads_count');
        $request->user()->forumSubscriptions()->firstOrCreate(['thread_id' => $thread->id]);

        RateLimiter::hit($key, 60);

        return redirect()
            ->route('community.threads.show', $thread)
            ->with('status', 'Your discussion has been posted.');
    }

    public function show(Request $request, ForumThread $thread)
    {
        abort_unless($thread->status === 'published' || ($request->user()?->isAdmin()), 404);

        // Inflation-resistant view count: once per thread per session.
        $seen = session()->get('forum_viewed', []);
        if (! in_array($thread->id, $seen, true)) {
            $thread->increment('views_count');
            session()->put('forum_viewed', array_merge($seen, [$thread->id]));
        }

        $thread->load(['user', 'category']);

        $posts = $thread->posts()
            ->published()
            ->with('user')
            ->orderBy('created_at')
            ->paginate(15);

        $user = $request->user();
        $isSubscribed = $user
            ? $user->forumSubscriptions()->where('thread_id', $thread->id)->exists()
            : false;

        // Preload the viewer's like state (avoids N+1 across replies).
        $likedThread = $thread->likedBy($user);
        $likedPostIds = $user
            ? \App\Models\ForumReaction::where('user_id', $user->id)
                ->where('type', 'like')
                ->where('reactable_type', \App\Models\ForumPost::class)
                ->whereIn('reactable_id', $posts->pluck('id'))
                ->pluck('reactable_id')
                ->all()
            : [];

        return view('community.threads.show', compact('thread', 'posts', 'isSubscribed', 'likedThread', 'likedPostIds'));
    }

    public function edit(Request $request, ForumThread $thread)
    {
        $this->authorize('update', $thread);

        $categories = ForumCategory::active()->orderBy('sort_order')->orderBy('name')->get();

        return view('community.threads.edit', compact('thread', 'categories'));
    }

    public function update(Request $request, ForumThread $thread)
    {
        $this->authorize('update', $thread);

        $data = $request->validate([
            'category_id' => ['required', 'exists:forum_categories,id'],
            'title' => ['required', 'string', 'min:8', 'max:160'],
            'body' => ['required', 'string', 'min:15', 'max:20000'],
        ]);

        $thread->update([
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'body' => $this->sanitizer->sanitize($data['body']),
        ]);

        return redirect()
            ->route('community.threads.show', $thread)
            ->with('status', 'Discussion updated.');
    }

    public function destroy(Request $request, ForumThread $thread)
    {
        $this->authorize('delete', $thread);

        $thread->category()->decrement('threads_count');
        $thread->delete();

        return redirect()
            ->route('community.category', $thread->category)
            ->with('status', 'Discussion removed.');
    }
}
