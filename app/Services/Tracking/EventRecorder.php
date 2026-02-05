<?php

namespace App\Services\Tracking;

use App\Models\UserEvent;
use App\Models\UserSession;

class EventRecorder
{
    public function record(UserSession $session, string $type, array $data = []): UserEvent
    {
        $points = $this->calculatePoints($type, $data);

        $event = UserEvent::create([
            'session_id' => $session->session_id,
            'user_id' => $session->user_id,
            'subscriber_id' => $session->subscriber_id,
            'event_type' => $type,
            'event_category' => $this->categorize($type),
            'event_data' => $data['extra'] ?? null,
            'page_url' => $data['page_url'] ?? null,
            'page_title' => $data['page_title'] ?? null,
            'element_id' => $data['element_id'] ?? null,
            'element_class' => $data['element_class'] ?? null,
            'element_text' => $data['element_text'] ?? null,
            'scroll_depth' => $data['scroll_depth'] ?? null,
            'time_on_page' => $data['time_on_page'] ?? null,
            'engagement_points' => $points,
            'created_at' => now(),
        ]);

        $this->updateSessionStats($session, $type, $points, $data);

        return $event;
    }

    protected function calculatePoints(string $type, array $data): int
    {
        $points = match ($type) {
            UserEvent::TYPE_PAGE_VIEW => (int) \App\Models\Setting::getValue('scoring', 'points_page_view', 1),
            UserEvent::TYPE_SCROLL => $this->scrollPoints($data['scroll_depth'] ?? 0),
            UserEvent::TYPE_QUIZ_START => (int) \App\Models\Setting::getValue('scoring', 'points_quiz_start', 3),
            UserEvent::TYPE_QUIZ_COMPLETE => (int) \App\Models\Setting::getValue('scoring', 'points_quiz_complete', 10),
            UserEvent::TYPE_LEAD_MAGNET => (int) \App\Models\Setting::getValue('scoring', 'points_lead_magnet', 8),
            UserEvent::TYPE_OUTBOUND_CLICK => (int) \App\Models\Setting::getValue('scoring', 'points_product_click', 10),
            UserEvent::TYPE_POPUP_CONVERT => 5,
            default => 0,
        };

        // Time on page bonus
        if (isset($data['time_on_page']) && $data['time_on_page'] >= 60) {
            $points += (int) \App\Models\Setting::getValue('scoring', 'points_time_60s', 2);
        }

        return $points;
    }

    protected function scrollPoints(int $depth): int
    {
        if ($depth >= 75) {
            return (int) \App\Models\Setting::getValue('scoring', 'points_scroll_75', 2);
        }
        return 0;
    }

    protected function categorize(string $type): string
    {
        return match ($type) {
            UserEvent::TYPE_PAGE_VIEW, UserEvent::TYPE_SCROLL => 'navigation',
            UserEvent::TYPE_CLICK, UserEvent::TYPE_RAGE_CLICK => 'interaction',
            UserEvent::TYPE_FORM_FOCUS, UserEvent::TYPE_FORM_SUBMIT => 'form',
            UserEvent::TYPE_QUIZ_START, UserEvent::TYPE_QUIZ_ANSWER, UserEvent::TYPE_QUIZ_COMPLETE => 'quiz',
            UserEvent::TYPE_POPUP_VIEW, UserEvent::TYPE_POPUP_CONVERT => 'popup',
            UserEvent::TYPE_LEAD_MAGNET => 'conversion',
            UserEvent::TYPE_OUTBOUND_CLICK => 'outbound',
            default => 'other',
        };
    }

    protected function updateSessionStats(UserSession $session, string $type, int $points, array $data): void
    {
        $updates = ['events_count' => $session->events_count + 1];

        if ($type === UserEvent::TYPE_PAGE_VIEW) {
            $updates['pages_viewed'] = $session->pages_viewed + 1;
            $updates['exit_url'] = $data['page_url'] ?? $session->exit_url;
        }

        if (isset($data['scroll_depth']) && $data['scroll_depth'] > ($session->avg_scroll_depth ?? 0)) {
            $updates['avg_scroll_depth'] = $data['scroll_depth'];
        }

        if ($points > 0) {
            $updates['engagement_score'] = $session->engagement_score + $points;
        }

        $session->update($updates);

        // Update subscriber engagement
        if ($session->subscriber_id && $points > 0) {
            $session->subscriber?->addEngagementPoints($points);
        }
    }
}
