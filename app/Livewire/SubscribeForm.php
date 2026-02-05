<?php

namespace App\Livewire;

use App\Services\SubscriberService;
use Livewire\Component;

class SubscribeForm extends Component
{
    public string $email = '';
    public string $source = 'footer';
    public bool $success = false;
    public string $message = '';

    protected $rules = [
        'email' => 'required|email',
    ];

    public function subscribe()
    {
        $this->validate();

        $service = app(SubscriberService::class);

        // Subscribe (handles deduplication internally - no duplicates created)
        $service->subscribe($this->email, [
            'source' => $this->source,
            'segment' => request()->cookie('pp_segment') ?? 'TOF',
            'first_session_id' => request()->cookie('pp_session_id'),
            'first_landing_page' => url()->current(),
        ]);

        $service->setEmailCookie($this->email);

        // Always show same message - user doesn't need to know if they were already subscribed
        $this->success = true;
        $this->message = 'Thanks for subscribing!';
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.subscribe-form');
    }
}
