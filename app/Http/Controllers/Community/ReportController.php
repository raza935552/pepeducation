<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumReport;
use App\Models\ForumThread;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:thread,post'],
            'id' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:60'],
            'details' => ['nullable', 'string', 'max:500'],
        ]);

        abort_unless($request->user()->canParticipateInCommunity(), 403);

        $model = $data['type'] === 'thread'
            ? ForumThread::findOrFail($data['id'])
            : ForumPost::findOrFail($data['id']);

        // One open report per user per item.
        $report = $model->reports()->firstOrCreate(
            [
                'reporter_id' => $request->user()->id,
                'status' => 'open',
            ],
            [
                'reason' => $data['reason'],
                'details' => $data['details'] ?? null,
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Thanks — our moderators will review this.',
        ]);
    }
}
