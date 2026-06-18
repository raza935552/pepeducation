<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumReport;
use App\Models\ForumThread;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'threads' => ForumThread::count(),
            'posts' => ForumPost::count(),
            'open_reports' => ForumReport::open()->count(),
            'members' => User::where('is_seed', false)->count(),
            'seed_members' => User::where('is_seed', true)->count(),
        ];

        $reports = ForumReport::open()
            ->with(['reporter', 'reportable'])
            ->latest()
            ->limit(50)
            ->get();

        $recentThreads = ForumThread::with(['user', 'category'])
            ->latest()
            ->limit(25)
            ->get();

        $communityEnabled = (bool) Setting::getValue('community', 'enabled', false);
        $dripEnabled = (bool) Setting::getValue('community', 'drip_enabled', false);

        return view('admin.community.dashboard', compact('stats', 'reports', 'recentThreads', 'communityEnabled', 'dripEnabled'));
    }

    public function updateSettings(Request $request)
    {
        Setting::setValue('community', 'enabled', $request->boolean('enabled'));
        Setting::setValue('community', 'drip_enabled', $request->boolean('drip_enabled'));

        return back()->with('status', 'Community settings saved.');
    }

    public function threadAction(Request $request, ForumThread $thread)
    {
        $action = $request->validate(['action' => ['required', 'in:pin,unpin,lock,unlock,hide,publish,delete']])['action'];

        $category = $thread->category;

        match ($action) {
            'pin' => $thread->update(['is_pinned' => true]),
            'unpin' => $thread->update(['is_pinned' => false]),
            'lock' => $thread->update(['is_locked' => true]),
            'unlock' => $thread->update(['is_locked' => false]),
            'hide' => $thread->update(['status' => 'hidden']),
            'publish' => $thread->update(['status' => 'published']),
            'delete' => $thread->delete(),
        };

        // Visibility/deletion changes affect the denormalised counters.
        if (in_array($action, ['hide', 'publish', 'delete'], true) && $category) {
            $this->recountCategory($category);
        }

        return back()->with('status', "Thread {$action} applied.");
    }

    public function postAction(Request $request, ForumPost $post)
    {
        $action = $request->validate(['action' => ['required', 'in:hide,publish,delete']])['action'];

        $thread = $post->thread;

        match ($action) {
            'hide' => $post->update(['status' => 'hidden']),
            'publish' => $post->update(['status' => 'published']),
            'delete' => $post->delete(),
        };

        if ($thread) {
            $this->recountThread($thread);
            if ($thread->category) {
                $this->recountCategory($thread->category);
            }
        }

        return back()->with('status', "Reply {$action} applied.");
    }

    /** Recompute a category's published thread/post counts. */
    private function recountCategory(ForumCategory $category): void
    {
        $publishedThreadIds = $category->threads()->published()->pluck('id');

        $category->update([
            'threads_count' => $publishedThreadIds->count(),
            'posts_count' => ForumPost::whereIn('thread_id', $publishedThreadIds)->published()->count(),
        ]);
    }

    /** Recompute a thread's published reply count + last-post pointer. */
    private function recountThread(ForumThread $thread): void
    {
        $latest = $thread->posts()->published()->latest()->first();

        $thread->update([
            'replies_count' => $thread->posts()->published()->count(),
            'last_post_user_id' => $latest?->user_id ?? $thread->user_id,
            'last_activity_at' => $latest?->created_at ?? $thread->created_at,
        ]);
    }

    public function resolveReport(Request $request, ForumReport $report)
    {
        $status = $request->validate(['status' => ['required', 'in:actioned,dismissed']])['status'];

        $report->update([
            'status' => $status,
            'handled_by' => $request->user()->id,
            'handled_at' => now(),
        ]);

        return back()->with('status', 'Report resolved.');
    }

    public function suspendUser(Request $request, User $user)
    {
        abort_if($user->isAdmin(), 403, 'Cannot suspend an admin.');

        $suspend = ! $user->is_suspended;
        $user->update([
            'is_suspended' => $suspend,
            'suspended_at' => $suspend ? now() : null,
        ]);

        return back()->with('status', $suspend ? 'Member suspended.' : 'Member reinstated.');
    }
}
