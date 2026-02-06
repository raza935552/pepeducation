<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Peptide;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SearchModal extends Component
{
    public string $search = '';
    public bool $isOpen = false;
    public int $selectedIndex = 0;

    protected $listeners = ['openSearch' => 'open'];

    public function open(): void
    {
        $this->isOpen = true;
        $this->search = '';
        $this->selectedIndex = 0;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->search = '';
    }

    public function updatedSearch(): void
    {
        $this->selectedIndex = 0;
    }

    public function selectNext(): void
    {
        if ($this->selectedIndex < count($this->results) - 1) {
            $this->selectedIndex++;
        }
    }

    public function selectPrevious(): void
    {
        if ($this->selectedIndex > 0) {
            $this->selectedIndex--;
        }
    }

    public function goToSelected(): void
    {
        $results = $this->results;
        if (isset($results[$this->selectedIndex])) {
            $item = $results[$this->selectedIndex];
            $this->redirect($item['url']);
        }
    }

    #[Computed]
    public function results(): array
    {
        if (strlen($this->search) < 2) {
            return [];
        }

        $results = [];

        $search = str_replace(['%', '_'], ['\\%', '\\_'], $this->search);

        // Search peptides (name, abbreviation, type only - not overview for performance)
        $peptides = Peptide::select(['id', 'name', 'slug', 'abbreviation', 'type'])
            ->where('is_published', true)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('abbreviation', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            })
            ->limit(5)
            ->get();

        foreach ($peptides as $peptide) {
            $results[] = [
                'type' => 'peptide',
                'name' => $peptide->name,
                'subtitle' => $peptide->type ?? $peptide->abbreviation,
                'url' => route('peptides.show', $peptide),
            ];
        }

        // Search categories
        $categories = Category::select(['id', 'name', 'slug'])
            ->where('name', 'like', "%{$search}%")
            ->withCount('peptides')
            ->having('peptides_count', '>', 0)
            ->limit(3)
            ->get();

        foreach ($categories as $category) {
            $results[] = [
                'type' => 'category',
                'name' => $category->name,
                'subtitle' => $category->peptides_count . ' peptides',
                'url' => route('peptides.index', ['category' => $category->slug]),
            ];
        }

        return $results;
    }

    public function render()
    {
        return view('livewire.search-modal');
    }
}
