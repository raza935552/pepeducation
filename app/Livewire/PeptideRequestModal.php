<?php

namespace App\Livewire;

use App\Models\PeptideRequest;
use Livewire\Component;

class PeptideRequestModal extends Component
{
    public bool $show = false;
    public string $peptideName = '';
    public string $sourceLinks = '';
    public string $notes = '';
    public bool $submitted = false;

    protected $listeners = ['openPeptideRequestModal' => 'open'];

    protected $rules = [
        'peptideName' => 'required|string|max:255',
        'sourceLinks' => 'required|string',
        'notes' => 'nullable|string|max:1000',
    ];

    public function open()
    {
        $this->show = true;
        $this->submitted = false;
    }

    public function close()
    {
        $this->show = false;
        $this->reset(['peptideName', 'sourceLinks', 'notes', 'submitted']);
    }

    public function submit()
    {
        $this->validate();

        // Simple rate limit: max 3 per hour (bypassed on .test domains)
        if (! \App\Providers\AppServiceProvider::isTestEnv()) {
            $key = 'peptide_request_' . request()->ip();
            if (cache()->get($key, 0) >= 3) {
                $this->addError('peptideName', 'Too many requests. Please try again later.');
                return;
            }
            cache()->put($key, cache()->get($key, 0) + 1, 3600);
        }

        $links = array_filter(array_map('trim', explode("\n", $this->sourceLinks)));

        PeptideRequest::create([
            'user_id' => auth()->id(),
            'peptide_name' => $this->peptideName,
            'source_links' => $links,
            'notes' => $this->notes ?: null,
            'status' => 'pending',
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.peptide-request-modal');
    }
}
