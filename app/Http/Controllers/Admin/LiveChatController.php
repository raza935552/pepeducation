<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Services\Chat\ChatPresence;
use App\Services\Chat\ChatService;
use Illuminate\Http\Request;

class LiveChatController extends Controller
{
    public function __construct(private ChatService $chat) {}

    public function index(Request $request)
    {
        $conversations = ChatConversation::with('assignee')
            ->orderByRaw("FIELD(status,'open','closed')")
            ->orderByDesc('last_message_at')
            ->limit(100)->get();

        return view('admin.live-chat.index', [
            'conversations' => $conversations,
            'openId' => $request->integer('open') ?: null,
            'stats' => $this->chatStats(),
        ]);
    }

    private function chatStats(): array
    {
        $since = now()->subDays(30);
        $base = ChatConversation::where('created_at', '>=', $since);
        $total = (clone $base)->count();
        $botResolved = (clone $base)->whereDoesntHave('messages', fn ($q) => $q->where('sender', 'agent'))->count();
        $leads = (clone $base)->whereNotNull('email')->distinct()->count('email');

        return [
            'total' => $total,
            'open_now' => ChatConversation::where('status', 'open')->count(),
            'bot_resolved_pct' => $total ? (int) round($botResolved / $total * 100) : 0,
            'leads' => $leads,
        ];
    }

    public function list()
    {
        $rows = ChatConversation::with('assignee')
            ->orderByRaw("FIELD(status,'open','closed')")
            ->orderByDesc('last_message_at')
            ->limit(100)->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->displayName(),
                'email' => $c->email,
                'status' => $c->status,
                'mode' => $c->mode,
                'unread' => $c->unread_for_admin,
                'human' => (bool) $c->human_requested,
                'rating' => $c->rating,
                'last' => optional($c->last_message_at)->diffForHumans(),
            ]);

        return response()->json(['conversations' => $rows]);
    }

    public function show(ChatConversation $conversation)
    {
        $conversation->messages()->where('sender', 'visitor')->whereNull('read_at')->update(['read_at' => now()]);
        $conversation->update(['unread_for_admin' => 0]);

        return response()->json([
            'id' => $conversation->id,
            'name' => $conversation->displayName(),
            'email' => $conversation->email,
            'status' => $conversation->status,
            'mode' => $conversation->mode,
            'human_requested' => (bool) $conversation->human_requested,
            'rating' => $conversation->rating,
            'messages' => $conversation->messages()->get()->map(fn ($m) => $this->chat->row($m))->values(),
        ]);
    }

    public function reply(Request $request, ChatConversation $conversation)
    {
        $data = $request->validate(['body' => 'required|string|max:5000']);
        $conversation->update(['unread_for_admin' => 0, 'assigned_to' => $conversation->assigned_to ?: $request->user()?->id]);
        $msg = $this->chat->post($conversation, 'agent', $data['body'], $request->user()?->name, $request->user()?->id);

        return response()->json(['ok' => true, 'message' => $this->chat->row($msg)]);
    }

    public function close(ChatConversation $conversation)
    {
        $wasOpen = $conversation->status === 'open';
        $conversation->update(['status' => $wasOpen ? 'closed' : 'open']);

        if ($wasOpen && $conversation->email && !$conversation->transcript_sent_at && $conversation->messages()->exists()) {
            try {
                \Illuminate\Support\Facades\Mail::to($conversation->email)
                    ->send(new \App\Mail\ChatTranscriptMail($conversation));
                $conversation->forceFill(['transcript_sent_at' => now()])->save();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('PP chat transcript failed: ' . $e->getMessage());
            }
        }

        return response()->json(['ok' => true, 'status' => $conversation->status]);
    }

    public function heartbeat(Request $request)
    {
        if ($request->user()) {
            ChatPresence::heartbeat($request->user()->id);
        }
        return response()->json(['online' => ChatPresence::onlineCount()]);
    }
}
