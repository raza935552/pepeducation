<?php

namespace App\Livewire;

use App\Models\LeadMagnet;
use App\Models\LeadMagnetDownload;
use App\Models\Subscriber;
use App\Services\SubscriberService;
use App\Services\Tracking\TrackingManager;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class LeadMagnetForm extends Component
{
    public LeadMagnet $leadMagnet;
    public string $email = '';
    public string $name = '';
    public bool $submitted = false;
    public ?string $downloadUrl = null;

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'name' => 'nullable|string|max:100',
        ];
    }

    public function submit()
    {
        $this->validate();

        $tracking = new TrackingManager(request());
        $session = $tracking->getSession();

        // Create or update subscriber
        $subscriberService = app(SubscriberService::class);
        $subscriber = $subscriberService->subscribe($this->email, [
            'name' => $this->name ?: null,
            'source' => 'lead_magnet:' . $this->leadMagnet->slug,
            'segment' => $this->leadMagnet->segment !== 'all' ? $this->leadMagnet->segment : null,
            'first_session_id' => $session->session_id,
        ]);

        // Link subscriber to session
        if ($subscriber && !$session->subscriber_id) {
            $session->update(['subscriber_id' => $subscriber->id]);
        }

        // Create download record
        $download = LeadMagnetDownload::create([
            'lead_magnet_id' => $this->leadMagnet->id,
            'session_id' => $session->session_id,
            'subscriber_id' => $subscriber?->id,
            'user_id' => auth()->id(),
            'source_page' => url()->previous(),
            'source_popup' => null,
            'utm_source' => $session->utm_source,
            'utm_campaign' => $session->utm_campaign,
            'delivery_method' => $this->leadMagnet->delivery_method,
            'downloaded' => $this->leadMagnet->delivery_method === LeadMagnet::DELIVERY_INSTANT,
            'downloaded_at' => $this->leadMagnet->delivery_method === LeadMagnet::DELIVERY_INSTANT ? now() : null,
            'created_at' => now(),
        ]);

        // Increment download count
        $this->leadMagnet->increment('downloads_count');

        // Track the download
        $tracking->trackLeadMagnetDownload($download);

        // Update subscriber with download info
        if ($subscriber) {
            $downloadedMagnets = $subscriber->lead_magnets_downloaded ?? [];
            if (!in_array($this->leadMagnet->slug, $downloadedMagnets)) {
                $downloadedMagnets[] = $this->leadMagnet->slug;
                $subscriber->update([
                    'lead_magnets_downloaded' => $downloadedMagnets,
                    'engagement_score' => $subscriber->engagement_score + 10,
                ]);
            }
        }

        $this->submitted = true;

        // Handle delivery method
        if ($this->leadMagnet->delivery_method === LeadMagnet::DELIVERY_INSTANT) {
            $this->downloadUrl = route('lead-magnet.download', [
                'slug' => $this->leadMagnet->slug,
                'email' => $this->email,
            ]);
            $download->markDownloaded();
        }
    }

    public function render()
    {
        return view('livewire.lead-magnet-form');
    }
}
