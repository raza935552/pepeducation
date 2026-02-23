<?php

namespace App\Livewire;

use App\Models\Popup;
use App\Models\PopupInteraction;
use App\Services\SubscriberService;
use Livewire\Component;
use Illuminate\Support\Collection;

class PopupManager extends Component
{
    public Collection $popups;
    public ?int $activePopupId = null;
    public array $shownPopups = [];
    public string $email = '';
    public bool $submitted = false;
    public string $currentPath = '';
    public ?string $currentSegment = null;

    public function mount()
    {
        $this->currentPath = request()->path();
        $this->currentSegment = session('pp_segment');
        $this->loadEligiblePopups();
    }

    public function loadEligiblePopups(): void
    {
        $this->popups = Popup::select([
                'id', 'name', 'slug', 'type', 'design', 'triggers', 'targeting',
                'headline', 'body', 'button_text', 'image', 'success_message',
                'form_fields', 'display_rules',
                'views_count', 'conversions_count', 'dismissals_count',
            ])
            ->active()
            ->get()
            ->filter(fn($popup) => $this->isEligible($popup));
    }

    private function isEligible(Popup $popup): bool
    {
        if ($this->wasShownRecently($popup)) {
            return false;
        }

        $targeting = $popup->targeting ?? [];

        if (!empty($targeting['segments'])) {
            if (!in_array($this->currentSegment, $targeting['segments'])) {
                return false;
            }
        }

        if (!empty($targeting['pages'])) {
            $match = false;
            foreach ($targeting['pages'] as $pattern) {
                if ($pattern === '*' || fnmatch($pattern, $this->currentPath)) {
                    $match = true;
                    break;
                }
            }
            if (!$match) return false;
        }

        if (!empty($targeting['exclude_pages'])) {
            foreach ($targeting['exclude_pages'] as $pattern) {
                if (fnmatch($pattern, $this->currentPath)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function wasShownRecently(Popup $popup): bool
    {
        $key = 'popup_shown_' . $popup->id;
        $lastShown = session($key);

        if (!$lastShown) return false;

        $frequency = $popup->targeting['frequency'] ?? 'session';

        return match ($frequency) {
            'once' => true,
            'session' => true,
            'daily' => $lastShown > now()->subDay()->timestamp,
            'always' => false,
            default => true,
        };
    }

    public function showPopup(int $popupId): void
    {
        $popup = $this->popups->firstWhere('id', $popupId);
        if (!$popup || in_array($popupId, $this->shownPopups)) return;

        $this->activePopupId = $popupId;
        $this->shownPopups[] = $popupId;
        $this->submitted = false;
        $this->email = '';

        session(['popup_shown_' . $popupId => now()->timestamp]);

        $this->recordInteraction($popup, 'impression');
    }

    public function closePopup(): void
    {
        if ($this->activePopupId) {
            $popup = $this->popups->firstWhere('id', $this->activePopupId);
            if ($popup) {
                $this->recordInteraction($popup, 'close');
            }
        }
        $this->activePopupId = null;
    }

    public function submitEmail(): void
    {
        $this->validate(['email' => 'required|email']);

        $popup = $this->popups->firstWhere('id', $this->activePopupId);
        if (!$popup) return;

        $service = app(SubscriberService::class);

        $service->subscribe($this->email, [
            'source' => 'popup:' . $popup->slug,
            'segment' => in_array($s = strtolower(request()->cookie('pp_segment') ?? $this->currentSegment ?? 'tof'), ['tof', 'mof', 'bof']) ? $s : 'tof',
            'first_session_id' => request()->cookie('pp_session_id'),
            'first_landing_page' => url()->current(),
        ]);

        $service->setEmailCookie($this->email);

        $this->recordInteraction($popup, 'conversion', ['email' => $this->email]);
        $this->submitted = true;

        $this->dispatch('popup-conversion', popupId: $popup->id, email: $this->email);
    }

    private function recordInteraction(Popup $popup, string $type, array $data = []): void
    {
        $sessionId = request()->cookie('pp_session_id') ?? session()->getId();

        // Map internal types to schema enum values (view, dismiss, convert)
        $interactionType = match ($type) {
            'impression' => 'view',
            'conversion' => 'convert',
            'close' => 'dismiss',
            default => $type,
        };

        PopupInteraction::create([
            'popup_id' => $popup->id,
            'session_id' => $sessionId,
            'interaction_type' => $interactionType,
            'form_data' => !empty($data) ? $data : null,
        ]);

        $popup->increment($type === 'impression' ? 'views_count' : ($type === 'conversion' ? 'conversions_count' : 'dismissals_count'));
    }

    public function getActivePopupProperty(): ?Popup
    {
        if (!$this->activePopupId) return null;
        return $this->popups->firstWhere('id', $this->activePopupId);
    }

    public function render()
    {
        return view('livewire.popup-manager');
    }
}
