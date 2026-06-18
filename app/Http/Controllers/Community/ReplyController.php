<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Services\ForumContentSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ReplyController extends Controller
{
    public function __construct(private ForumContentSanitizer $sanitizer)
    {
    }

    public function store(Request $request, ForumThread $thread)
    {
        $this->authorize('reply', $thread);

        $key = 'forum-reply:' . $request->user()->id;
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return back()->withInput()->withErrors([
                'body' => 'You are posting too fast. Please wait a moment and try again.',
            ]);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:20000'],
        ]);

        $body = $this->sanitizer->sanitize($data['body']);
        abort_if(trim(strip_tags($body)) === '', 422, 'Reply is empty after sanitisation.');

        $post = ForumPost::create([
            'thread_id' => $thread->id,
            'user_id' => $request->user()->id,
            'body' => $body,
            'status' => 'published',
        ]);

        $thread->increment('replies_count');
        $thread->update([
            'last_activity_at' => now(),
            'last_post_user_id' => $request->user()->id,
        ]);
        $thread->category()->increment('posts_count');
        $request->user()->increment('forum_posts_count');
        $request->user()->forumSubscriptions()->firstOrCreate(['thread_id' => $thread->id]);

        RateLimiter::hit($key, 60);

        // Notifications to thread subscribers are dispatched here (see milestone 141).
        event(new \App\Events\ForumReplyPosted($post));

        return redirect()
            ->to(route('community.threads.show', $thread) . '#reply-' . $post->id)
            ->with('status', 'Reply posted.');
    }

    public function update(Request $request, ForumPost $post)
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:20000'],
        ]);

        $post->update([
            'body' => $this->sanitizer->sanitize($data['body']),
            'edited_at' => now(),
        ]);

        return redirect()
            ->to(route('community.threads.show', $post->thread) . '#reply-' . $post->id)
            ->with('status', 'Reply updated.');
    }

    public function toggleSolution(Request $request, ForumPost $post)
    {
        $thread = $post->thread;
        abort_unless(
            $thread && ($request->user()->id === $thread->user_id || $request->user()->isAdmin()),
            403
        );

        if ($post->is_solution) {
            $post->update(['is_solution' => false]);
            $message = 'Removed the answer mark.';
        } else {
            // Only one accepted answer per thread.
            $thread->posts()->where('is_solution', true)->update(['is_solution' => false]);
            $post->update(['is_solution' => true]);
            $message = 'Marked as the answer. ✓';
        }

        return redirect()
            ->to(route('community.threads.show', $thread) . '#reply-' . $post->id)
            ->with('status', $message);
    }

    public function destroy(Request $request, ForumPost $post)
    {
        $this->authorize('delete', $post);

        $thread = $post->thread;
        $post->delete();

        if ($thread) {
            $thread->decrement('replies_count');
            $thread->category()->decrement('posts_count');
        }

        return redirect()
            ->route('community.threads.show', $thread)
            ->with('status', 'Reply removed.');
    }
}
