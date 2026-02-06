<?php

namespace App\Livewire;

use App\Models\Contribution;
use App\Models\Peptide;
use Livewire\Component;

class EditSuggestionModal extends Component
{
    public bool $show = false;
    public ?int $peptideId = null;
    public ?Peptide $peptide = null;
    public string $section = '';
    public string $originalContent = '';
    public string $newContent = '';
    public string $editReason = '';
    public bool $submitted = false;

    protected $listeners = ['openEditSuggestionModal' => 'open'];

    protected $rules = [
        'section' => 'required|string',
        'newContent' => 'required|string|min:10',
        'editReason' => 'nullable|string|max:500',
    ];

    public function open(int $peptideId, string $section = '')
    {
        $this->peptide = Peptide::find($peptideId);
        if (!$this->peptide) return;

        $this->peptideId = $peptideId;
        $this->section = $section;
        $this->loadOriginalContent();
        $this->show = true;
        $this->submitted = false;
    }

    public function updatedSection()
    {
        $this->loadOriginalContent();
    }

    protected function loadOriginalContent()
    {
        if (!$this->peptide || !$this->section) {
            $this->originalContent = '';
            $this->newContent = '';
            return;
        }

        $this->originalContent = match ($this->section) {
            'overview' => $this->peptide->overview ?? '',
            'mechanism_of_action' => $this->peptide->mechanism_of_action ?? '',
            'benefits' => $this->peptide->benefits ?? '',
            'side_effects' => $this->peptide->side_effects ?? '',
            'dosage_guidelines' => $this->peptide->dosage_guidelines ?? '',
            'timeline' => $this->peptide->timeline ?? '',
            'warnings' => $this->peptide->warnings ?? '',
            default => '',
        };

        $this->newContent = $this->originalContent;
    }

    public function close()
    {
        $this->show = false;
        $this->reset(['peptideId', 'peptide', 'section', 'originalContent', 'newContent', 'editReason', 'submitted']);
    }

    public function submit()
    {
        if (!auth()->check()) {
            $this->addError('newContent', 'You must be logged in to submit suggestions.');
            return;
        }

        $this->validate();

        if ($this->newContent === $this->originalContent) {
            $this->addError('newContent', 'Please make changes to the content.');
            return;
        }

        Contribution::create([
            'user_id' => auth()->id(),
            'peptide_id' => $this->peptideId,
            'section' => $this->section,
            'original_content' => $this->originalContent,
            'new_content' => $this->newContent,
            'edit_reason' => $this->editReason ?: null,
            'status' => 'pending',
        ]);

        $this->submitted = true;
    }

    public function getSectionOptions(): array
    {
        return [
            'overview' => 'Overview',
            'mechanism_of_action' => 'Mechanism of Action',
            'benefits' => 'Benefits',
            'side_effects' => 'Side Effects',
            'dosage_guidelines' => 'Dosage Guidelines',
            'timeline' => 'Timeline',
            'warnings' => 'Warnings',
        ];
    }

    public function render()
    {
        return view('livewire.edit-suggestion-modal');
    }
}
