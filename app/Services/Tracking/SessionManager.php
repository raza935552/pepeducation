<?php

namespace App\Services\Tracking;

use App\Models\UserSession;
use App\Models\Subscriber;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SessionManager
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getOrCreateSession(): UserSession
    {
        $sessionId = $this->getSessionId();

        $session = UserSession::where('session_id', $sessionId)
            ->whereNull('ended_at')
            ->first();

        if ($session) {
            return $this->updateActivity($session);
        }

        return $this->createSession($sessionId);
    }

    public function getSessionId(): string
    {
        // First check JSON body (from tracker.js POST requests)
        $sessionId = $this->request->input('session_id');

        // Then check cookie
        if (!$sessionId) {
            $sessionId = $this->request->cookie('pp_session_id');
        }

        // Generate new if neither exists
        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
        }

        return $sessionId;
    }

    protected function createSession(string $sessionId): UserSession
    {
        $userAgent = $this->request->userAgent() ?? '';
        $isReturning = UserSession::where('ip_address', $this->request->ip())
            ->where('created_at', '>=', now()->subDays(30))
            ->exists();
        $sessionNumber = UserSession::where('ip_address', $this->request->ip())
            ->where('created_at', '>=', now()->subDays(30))
            ->count() + 1;

        return UserSession::create([
            'session_id' => $sessionId,
            'user_id' => auth()->id(),
            'subscriber_id' => $this->findSubscriberId(),
            'started_at' => now(),
            'entry_url' => $this->request->fullUrl(),
            'referrer' => $this->request->header('referer'),
            'referrer_domain' => $this->extractDomain($this->request->header('referer')),
            'utm_source' => $this->request->get('utm_source'),
            'utm_medium' => $this->request->get('utm_medium'),
            'utm_campaign' => $this->request->get('utm_campaign'),
            'utm_content' => $this->request->get('utm_content'),
            'utm_term' => $this->request->get('utm_term'),
            'ip_address' => $this->request->ip(),
            'device_type' => $this->detectDeviceType($userAgent),
            'browser' => $this->detectBrowser($userAgent),
            'os' => $this->detectOS($userAgent),
            'is_mobile' => $this->detectDeviceType($userAgent) === 'mobile',
            'is_returning' => $isReturning,
            'session_number' => $sessionNumber,
            'pages_viewed' => 0,
            'events_count' => 0,
            'engagement_score' => 0,
        ]);
    }

    protected function updateActivity(UserSession $session): UserSession
    {
        $timeout = (int) Setting::getValue('tracking', 'session_timeout_minutes', 30);

        if ($session->updated_at->diffInMinutes(now()) > $timeout) {
            // Use DB transaction to prevent duplicate session creation
            return DB::transaction(function () use ($session) {
                $locked = UserSession::where('id', $session->id)->lockForUpdate()->first();
                if ($locked && !$locked->ended_at) {
                    $locked->endSession();
                }
                return $this->createSession($this->getSessionId());
            });
        }

        $session->touch();
        return $session;
    }

    protected function findSubscriberId(): ?int
    {
        $email = $this->request->cookie('pp_email');
        if ($email) {
            $subscriber = Subscriber::where('email', $email)->first();
            return $subscriber?->id;
        }
        return null;
    }

    protected function extractDomain(?string $url): ?string
    {
        if (!$url) return null;
        return parse_url($url, PHP_URL_HOST);
    }

    protected function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/mobile|android|iphone/i', $userAgent)) return 'mobile';
        if (preg_match('/tablet|ipad/i', $userAgent)) return 'tablet';
        return 'desktop';
    }

    protected function detectBrowser(string $userAgent): string
    {
        if (str_contains($userAgent, 'Chrome')) return 'Chrome';
        if (str_contains($userAgent, 'Firefox')) return 'Firefox';
        if (str_contains($userAgent, 'Safari')) return 'Safari';
        if (str_contains($userAgent, 'Edge')) return 'Edge';
        return 'Other';
    }

    protected function detectOS(string $userAgent): string
    {
        if (str_contains($userAgent, 'Windows')) return 'Windows';
        if (str_contains($userAgent, 'Mac')) return 'macOS';
        if (str_contains($userAgent, 'Linux')) return 'Linux';
        if (str_contains($userAgent, 'Android')) return 'Android';
        if (str_contains($userAgent, 'iOS')) return 'iOS';
        return 'Other';
    }

    public function linkSubscriber(UserSession $session, Subscriber $subscriber): void
    {
        $session->update(['subscriber_id' => $subscriber->id]);

        // Update subscriber with first touch attribution if not set
        if (!$subscriber->first_session_id) {
            $subscriber->update([
                'first_session_id' => $session->session_id,
                'first_utm_source' => $session->utm_source,
                'first_utm_medium' => $session->utm_medium,
                'first_utm_campaign' => $session->utm_campaign,
                'first_utm_content' => $session->utm_content,
                'first_referrer' => $session->referrer,
                'first_landing_page' => $session->entry_url,
                'device_type' => $session->device_type,
            ]);
        }
    }
}
