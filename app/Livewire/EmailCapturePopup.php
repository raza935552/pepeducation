<?php

namespace App\Livewire;

use App\Services\SubscriberService;
use Livewire\Attributes\On;
use Livewire\Component;

class EmailCapturePopup extends Component
{
    public bool $show = false;
    public string $email = '';
    public bool $success = false;

    protected $rules = [
        'email' => 'required|email',
    ];

    #[On('showEmailPopup')]
    public function open()
    {
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
    }

    public function subscribe()
    {
        $this->validate();

        $service = app(SubscriberService::class);

        $service->subscribe($this->email, [
            'source' => 'popup',
            'segment' => request()->cookie('pp_segment') ?? 'TOF',
            'first_session_id' => request()->cookie('pp_session_id'),
            'first_landing_page' => url()->previous(),
        ]);

        $service->setEmailCookie($this->email);

        $this->success = true;
    }

    public function render()
    {
        return view('livewire.email-capture-popup');
    }
}
