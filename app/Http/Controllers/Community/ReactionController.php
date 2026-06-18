<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumReaction;
use App\Models\ForumThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReactionController extends Controller
{
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:thread,post'],
            'id' => ['required', 'integer'],
        ]);

        abort_unless($request->user()->canParticipateInCommunity(), 403);

        /** @var ForumThread|ForumPost $model */
        $model = $data['type'] === 'thread'
            ? ForumThread::published()->findOrFail($data['id'])
            : ForumPost::published()->findOrFail($data['id']);

        $userId = $request->user()->id;

        $liked = DB::transaction(function () use ($model, $userId) {
            $existing = $model->reactions()
                ->where('user_id', $userId)
                ->where('type', 'like')
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $existing->delete();
                $model->decrement('likes_count');

                return false;
            }

            $model->reactions()->create(['user_id' => $userId, 'type' => 'like']);
            $model->increment('likes_count');

            return true;
        });

        return response()->json([
            'liked' => $liked,
            'count' => $model->fresh()->likes_count,
        ]);
    }
}
