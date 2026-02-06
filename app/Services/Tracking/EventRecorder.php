<?php

namespace App\Services\Tracking;

use App\Models\UserEvent;
use App\Models\UserSession;
use App\Models\Setting;

class EventRecorder
{
    public function record(UserSession $session, string $type, array $data = []): UserEvent
    {
        $data = $this->sanitizeData($data);
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

    protected function sanitizeData(array $data): array
    {
        $stringFields = ['page_url', 'page_title', 'element_id', 'element_class'];
        foreach ($stringFields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = mb_substr($data[$field], 0, 2048);
            }
        }

        if (isset($data['element_text']) && is_string($data['element_text'])) {
            $data['element_text'] = mb_substr($data['element_text'], 0, 255);
        }

        if (isset($data['scroll_depth'])) {
            $data['scroll_depth'] = max(0, min(100, (int) $data['scroll_depth']));
        }

        if (isset($data['time_on_page'])) {
            $data['time_on_page'] = max(0, min(86400, (int) $data['time_on_page']));
        }

        return $data;
    }

    protected function calculatePoints(string $type, array $data): int
    {
        $points = match ($type) {
            UserEvent::TYPE_PAGE_VIEW => (int) Setting::getValue('scoring', 'points_page_view', 1),
            UserEvent::TYPE_SCROLL => $this->scrollPoints($data['scroll_depth'] ?? 0),
            UserEvent::TYPE_QUIZ_START => (int) Setting::getValue('scoring', 'points_quiz_start', 3),
            UserEvent::TYPE_QUIZ_COMPLETE => (int) Setting::getValue('scoring', 'points_quiz_complete', 10),
            UserEvent::TYPE_LEAD_MAGNET => (int) Setting::getValue('scoring', 'points_lead_magnet', 8),
            UserEvent::TYPE_CTA_CLICK => (int) Setting::getValue('scoring', 'points_cta_click', 5),
            UserEvent::TYPE_OUTBOUND_CLICK => (int) Setting::getValue('scoring', 'points_product_click', 10),
            UserEvent::TYPE_POPUP_CONVERT => 5,
            default => 0,
        };

        if (isset($data['time_on_page']) && $data['time_on_page'] >= 60) {
            $points += (int) Setting::getValue('scoring', 'points_time_60s', 2);
        }

        return $points;
    }

    protected function scrollPoints(int $depth): int
    {
        if ($depth >= 75) {
            return (int) Setting::getValue('scoring', 'points_scroll_75', 2);
        }
        return 0;
    }

    protected function categorize(string $type): string
    {
        return match ($type) {
            UserEvent::TYPE_PAGE_VIEW, UserEvent::TYPE_SCROLL => 'navigation',
            UserEvent::TYPE_CLICK, UserEvent::TYPE_CTA_CLICK, UserEvent::TYPE_RAGE_CLICK => 'interaction',
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

        // Update subscriber engagement (increment only, defer tier calc)
        if ($session->subscriber_id && $points > 0) {
            $session->subscriber?->increment('engagement_score', $points);
        }
    }
}
