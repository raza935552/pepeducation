<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use Livewire\Component;
use Livewire\Attributes\Renderless;

class ContactModal extends Component
{
    protected int $rateLimitMaxAttempts = 3;

    public bool $show = false;
    public string $name = '';
    public string $email = '';
    public string $subject = 'general';
    public string $message = '';
    public bool $submitted = false;

    protected $listeners = ['openContactModal' => 'open'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|in:general,bug,feature,correction,partnership,other',
        'message' => 'required|string|min:10|max:2000',
    ];

    public function open()
    {
        $this->show = true;
        $this->submitted = false;

        if (auth()->check()) {
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    public function close()
    {
        $this->show = false;
        $this->reset(['name', 'email', 'subject', 'message', 'submitted']);
    }

    public function submit()
    {
        $this->validate();

        // Simple rate limit: max 3 per session (bypassed on .test domains)
        if (! \App\Providers\AppServiceProvider::isTestEnv()) {
            $key = 'contact_submit_' . request()->ip();
            if (cache()->get($key, 0) >= $this->rateLimitMaxAttempts) {
                $this->addError('message', 'Too many submissions. Please try again later.');
                return;
            }
            cache()->put($key, cache()->get($key, 0) + 1, 3600);
        }

        ContactMessage::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => 'new',
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.contact-modal');
    }
}
