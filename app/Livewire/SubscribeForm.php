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
    public bool $alreadySubscribed = false;

    protected $rules = [
        'email' => 'required|email',
    ];

    public function mount()
    {
        $ppEmail = request()->cookie('pp_email');
        if ($ppEmail) {
            $this->alreadySubscribed = true;
        }
    }

    public function subscribe()
    {
        $this->validate();

        // Rate limit: max 5 per hour per IP (bypassed on .test domains)
        if (! \App\Providers\AppServiceProvider::isTestEnv()) {
            $key = 'subscribe_' . request()->ip();
            if (cache()->get($key, 0) >= 5) {
                $this->addError('email', 'Too many attempts. Please try again later.');
                return;
            }
            cache()->put($key, cache()->get($key, 0) + 1, 3600);
        }

        $service = app(SubscriberService::class);

        // Subscribe (handles deduplication internally - no duplicates created)
        $service->subscribe($this->email, [
            'source' => $this->source,
            'segment' => in_array($s = strtolower(request()->cookie('pp_segment') ?? 'tof'), ['tof', 'mof', 'bof']) ? $s : 'tof',
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
