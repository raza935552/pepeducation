<?php

namespace App\Livewire;

use App\Models\OutboundLink;
use App\Models\Setting;
use App\Models\StackBundle;
use App\Models\StackGoal;
use App\Models\StackProduct;
use App\Models\StackStore;
use Livewire\Component;

class StackBuilder extends Component
{
    public int $currentStep = 1;
    public ?string $selectedGoalSlug = null;
    public bool $skippedBundles = false;
    public bool $skippedGoal = false;
    public string $productTab = 'goal'; // 'goal' or 'all'

    public function mount(?string $goalSlug = null)
    {
        $this->dispatch('stack-started');

        if ($goalSlug) {
            $goal = StackGoal::active()->where('slug', $goalSlug)->first();
            if ($goal) {
                $this->selectGoal($goal->slug);
            }
        }
    }

    public function selectGoal(string $slug): void
    {
        $goal = StackGoal::active()->where('slug', $slug)->first();
        if (!$goal) return;

        $this->selectedGoalSlug = $slug;
        $this->skippedGoal = false;
        $this->currentStep = 2;

        $this->dispatch('stack-goal-selected', goalSlug: $slug, goalName: $goal->name);
    }

    public function goToStep(int $step): void
    {
        if ($step < 1 || $step > 3) return;

        if ($step === 1) {
            $this->currentStep = 1;
            return;
        }

        if ($step === 2 && $this->selectedGoalSlug) {
            $this->currentStep = 2;
            return;
        }

        if ($step === 3 && ($this->selectedGoalSlug || $this->skippedGoal)) {
            $this->currentStep = 3;
            $this->dispatch('stack-completed', goalSlug: $this->selectedGoalSlug, goalName: $this->selectedGoal?->name);
            return;
        }
    }

    public function skipBundles(): void
    {
        $this->skippedBundles = true;
        $this->currentStep = 3;
        $this->dispatch('stack-completed', goalSlug: $this->selectedGoalSlug, goalName: $this->selectedGoal?->name);
    }

    public function skipToProducts(): void
    {
        $this->skippedGoal = true;
        $this->selectedGoalSlug = null;
        $this->productTab = 'all';
        $this->currentStep = 3;
        $this->dispatch('stack-completed', goalSlug: null, goalName: null);
    }

    public function resetGoal(): void
    {
        $this->selectedGoalSlug = null;
        $this->skippedGoal = false;
        $this->skippedBundles = false;
        $this->currentStep = 1;
    }

    // Computed Properties
    public function getGoalsProperty()
    {
        return StackGoal::active()->ordered()->get();
    }

    public function getSelectedGoalProperty()
    {
        if (!$this->selectedGoalSlug) return null;
        return StackGoal::active()->where('slug', $this->selectedGoalSlug)->first();
    }

    public function getProfessorPicksProperty()
    {
        if (!$this->selectedGoal) return collect();
        return StackBundle::active()
            ->professorPicks()
            ->forGoal($this->selectedGoal->id)
            ->with([
                'items.product.stores' => fn ($q) => $q->where('stack_stores.is_active', true)->orderBy('stack_stores.order'),
                'stores' => fn ($q) => $q->where('stack_stores.is_active', true)->orderBy('stack_stores.order'),
            ])
            ->ordered()
            ->get();
    }

    public function getGoalBundlesProperty()
    {
        if (!$this->selectedGoal) return collect();
        return StackBundle::active()
            ->forGoal($this->selectedGoal->id)
            ->with([
                'items.product.stores' => fn ($q) => $q->where('stack_stores.is_active', true)->orderBy('stack_stores.order'),
                'stores' => fn ($q) => $q->where('stack_stores.is_active', true)->orderBy('stack_stores.order'),
            ])
            ->ordered()
            ->get();
    }

    public function getGoalProductsProperty()
    {
        if (!$this->selectedGoal) return collect();
        return $this->selectedGoal->products()
            ->where('is_active', true)
            ->with(['stores' => fn ($q) => $q->where('stack_stores.is_active', true)->orderBy('stack_stores.order')])
            ->orderByPivot('order')
            ->get();
    }

    public function getAllProductsProperty()
    {
        return StackProduct::active()
            ->with(['stores' => fn ($q) => $q->where('stack_stores.is_active', true)->orderBy('stack_stores.order')])
            ->ordered()
            ->get();
    }

    public function getStoresProperty()
    {
        return StackStore::active()->ordered()->get();
    }

    public function getProgressProperty(): int
    {
        if ($this->currentStep === 1) return 0;
        if ($this->currentStep === 2) return 50;
        return 100; // step 3
    }

    public function getSettingsProperty(): array
    {
        return Setting::getGroup('stack_builder');
    }

    public function render()
    {
        return view('livewire.stack-builder');
    }
}
