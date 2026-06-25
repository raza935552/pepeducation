<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Services\Chat\ChatBotService;
use App\Services\Chat\ChatPresence;
use App\Services\Chat\ChatService;
use App\Services\SubscriberService;
use Illuminate\Http\Request;

/**
 * Visitor-facing live chat (Professor Peptides). Polling-based — no websockets.
 * Bot answers from the peptide library / calculators / blog and bridges
 * purchases to Biolinx. The visitor's email is captured as a subscriber lead.
 */
class ChatWidgetController extends Controller
{
    public function __construct(private ChatService $chat) {}

    /** Resume an existing conversation (token, <24h) or report availability. */
    public function init(Request $request)
    {
        $token = (string) $request->input('token', '');
        $conv = $token
            ? ChatConversation::where('token', $token)->where('last_message_at', '>=', now()->subDay())->first()
            : null;

        return response()->json([
            'online' => ChatPresence::anyOnline(),
            'started' => (bool) $conv,
            'token' => $conv?->token,
            'name' => $conv?->name,
            'human_requested' => (bool) ($conv?->human_requested),
            'rating' => $conv?->rating,
            'messages' => $conv ? $this->serialize($conv) : [],
        ]);
    }

    /** Start (or resume <24h by email) a conversation. */
    public function start(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:100',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string|max:5000',
        ]);

        $online = ChatPresence::anyOnline();

        // ONE conversation per email — reuse the visitor's existing thread
        // instead of piling up rows. Quiet >24h → fresh session for the visitor
        // (re-greet, hide old transcript) on the same row the team sees.
        $existing = ChatConversation::where('email', $data['email'])
            ->latest('last_message_at')->first();

        if ($existing) {
            $stale = !$existing->last_message_at || $existing->last_message_at->lt(now()->subDay());
            $sinceId = (int) $existing->messages()->max('id');

            if ($stale) {
                $existing->forceFill([
                    'human_requested' => false, 'status' => 'open',
                    'mode' => $online ? 'live' : 'offline',
                    'rating' => null, 'rated_at' => null, 'last_message_at' => now(),
                ])->save();
            }

            if (!empty($data['message'])) {
                $this->chat->post($existing, 'visitor', $data['message'], $existing->name);
            }
            if ($this->botShouldRespond($existing)) {
                $bot = app(ChatBotService::class);
                if ($stale) {
                    $this->chat->post($existing, 'bot', $bot->greeting($existing), 'Assistant');
                }
                if (!empty($data['message'])) {
                    $reply = $bot->respond($existing, $data['message']);
                    if ($reply) {
                        $this->chat->post($existing, 'bot', $reply, 'Assistant');
                    }
                }
            }

            $messages = $stale
                ? $existing->messages()->where('id', '>', $sinceId)->get()->map(fn ($m) => $this->chat->row($m))->values()->all()
                : $this->serialize($existing);

            return response()->json([
                'token' => $existing->token, 'online' => $online,
                'human_requested' => (bool) $existing->human_requested, 'rating' => $existing->rating,
                'messages' => $messages, 'resumed' => true,
            ]);
        }

        $conv = ChatConversation::create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'mode' => $online ? 'live' : 'offline',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'landing_page' => substr((string) ($request->headers->get('referer') ?: ''), 0, 500),
            'last_message_at' => now(),
        ]);

        if (!empty($data['message'])) {
            $this->chat->post($conv, 'visitor', $data['message'], $conv->name);
        }

        // Capture the visitor as a subscriber lead (Customer.io), same as popups.
        try {
            app(SubscriberService::class)->subscribe($data['email'], [
                'name' => $data['name'] ?? null,
                'source' => 'live_chat',
            ]);
        } catch (\Throwable $e) {
            // never block the chat on lead capture
        }

        if ($this->botShouldRespond($conv)) {
            $bot = app(ChatBotService::class);
            $this->chat->post($conv, 'bot', $bot->greeting($conv), 'Assistant');
            if (!empty($data['message'])) {
                $this->chat->post($conv, 'bot', $bot->respond($conv, $data['message']), 'Assistant');
            }
        }

        return response()->json([
            'token' => $conv->token, 'online' => $online,
            'human_requested' => (bool) $conv->human_requested, 'rating' => $conv->rating,
            'messages' => $this->serialize($conv),
        ]);
    }

    /** Visitor sends a message. */
    public function send(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string',
            'body' => 'required|string|max:5000',
        ]);

        $conv = ChatConversation::where('token', $data['token'])->firstOrFail();
        $msg = $this->chat->post($conv, 'visitor', $data['body'], $conv->name);

        // Polling-based, so return the bot reply inline for an instant answer
        // (polling still delivers later agent replies).
        $replies = [];
        if ($this->botShouldRespond($conv)) {
            $reply = app(ChatBotService::class)->respond($conv, $data['body']);
            if ($reply) {
                $bot = $this->chat->post($conv, 'bot', $reply, 'Assistant');
                $replies[] = $this->chat->row($bot);
            }
        }

        return response()->json(['ok' => true, 'message' => $this->chat->row($msg), 'replies' => $replies]);
    }

    /** Visitor asks for a human — silence the bot, flag for the team. */
    public function handoff(Request $request)
    {
        $conv = ChatConversation::where('token', (string) $request->input('token'))->firstOrFail();
        if ($conv->human_requested) {
            return response()->json(['ok' => true]);
        }
        $conv->forceFill(['human_requested' => true, 'human_requested_at' => now(), 'status' => 'open'])->save();

        $line = ChatPresence::anyOnline()
            ? "👍 Connecting you with our team — someone will reply right here shortly."
            : "👍 I've flagged this for our team. We'll reply here and email you at {$conv->email} as soon as we can.";
        $msg = $this->chat->post($conv, 'bot', $line, 'Assistant');

        return response()->json(['ok' => true, 'message' => $this->chat->row($msg)]);
    }

    /** CSAT rating (one per conversation). */
    public function rate(Request $request)
    {
        $data = $request->validate(['token' => 'required|string', 'rating' => 'required|in:0,1']);
        $conv = ChatConversation::where('token', $data['token'])->firstOrFail();
        if (is_null($conv->rating)) {
            $conv->forceFill(['rating' => (int) $data['rating'], 'rated_at' => now()])->save();
        }
        return response()->json(['ok' => true]);
    }

    /** Polling: messages after a given id. */
    public function poll(Request $request)
    {
        $conv = ChatConversation::where('token', (string) $request->input('token'))->firstOrFail();
        $after = (int) $request->input('after', 0);
        $msgs = $conv->messages()->where('id', '>', $after)->get();
        return response()->json(['messages' => $msgs->map(fn ($m) => $this->chat->row($m))->values()]);
    }

    private function botShouldRespond(ChatConversation $conv): bool
    {
        // Bot always helps until the visitor asks for a human or an agent replies.
        if ($conv->human_requested) return false;
        if ($conv->messages()->where('sender', 'agent')->exists()) return false;
        return true;
    }

    private function serialize(ChatConversation $conv): array
    {
        return $conv->messages()->get()->map(fn ($m) => $this->chat->row($m))->values()->all();
    }
}
