<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;

class PeptideCalculator extends Component
{
    // Reconstitution inputs
    public $peptideAmount = 5; // mg in vial
    public $waterAmount = 2; // mL of bacteriostatic water

    // Dosing inputs
    public $desiredDose = 250; // desired per injection
    public $doseUnit = 'mcg'; // 'mcg' or 'mg'
    public $syringeSize = '100'; // 100u, 50u, or 30u

    // Body weight dosing
    public $useBodyWeight = false;
    public $bodyWeight = 70; // kg
    public $dosePerKg = 5; // mcg/kg
    public $weightUnit = 'kg';

    // Cycle planning
    public $injectionsPerDay = 1;
    public $cycleDays = 30;

    /**
     * Coerce a Livewire-bound input to a float safely. Empty strings, "abc",
     * etc. all become 0.0. Prevents PHP 8 "Unsupported operand types"
     * fatals when the user clears an input or types a non-numeric value.
     */
    private function num($value): float
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return 0.0;
        }

        return (float) $value;
    }

    /**
     * Calculate concentration in mcg/mL
     * Formula: (peptide mg × 1000) ÷ water mL
     */
    #[Computed]
    public function concentration(): float
    {
        $water = $this->num($this->waterAmount);
        if ($water <= 0) return 0;
        return ($this->num($this->peptideAmount) * 1000) / $water;
    }

    /**
     * Calculate mcg per unit on syringe
     * Formula: concentration ÷ 100
     */
    #[Computed]
    public function mcgPerUnit(): float
    {
        return $this->concentration / 100;
    }

    /**
     * Get the effective desired dose in mcg (considering body weight and unit)
     */
    #[Computed]
    public function effectiveDose(): float
    {
        if ($this->useBodyWeight) {
            $weight = $this->num($this->bodyWeight);
            $weightInKg = $this->weightUnit === 'lb'
                ? $weight * 0.453592
                : $weight;
            return $this->num($this->dosePerKg) * $weightInKg;
        }
        // Convert mg to mcg if needed
        $dose = $this->num($this->desiredDose);
        return $this->doseUnit === 'mg'
            ? $dose * 1000
            : $dose;
    }

    /**
     * Calculate volume needed in mL
     * Formula: desired dose (mcg) ÷ concentration (mcg/mL)
     */
    #[Computed]
    public function volumeNeeded(): float
    {
        if ($this->concentration <= 0) return 0;
        return $this->effectiveDose / $this->concentration;
    }

    /**
     * Calculate units to draw on syringe
     * Formula: volume (mL) × 100
     */
    #[Computed]
    public function unitsToDrawRaw(): float
    {
        return $this->volumeNeeded * 100;
    }

    /**
     * Get the max units for selected syringe
     */
    #[Computed]
    public function maxSyringeUnits(): int
    {
        return match($this->syringeSize) {
            '30' => 30,
            '50' => 50,
            default => 100,
        };
    }

    /**
     * Check if dose exceeds syringe capacity
     */
    #[Computed]
    public function exceedsSyringe(): bool
    {
        return $this->unitsToDrawRaw > $this->maxSyringeUnits;
    }

    /**
     * Get units to draw (capped at syringe max for display)
     */
    #[Computed]
    public function unitsToDraw(): float
    {
        return min($this->unitsToDrawRaw, $this->maxSyringeUnits);
    }

    /**
     * Calculate total doses available in vial
     * Formula: (peptide mg × 1000) ÷ effective dose
     */
    #[Computed]
    public function totalDosesInVial(): float
    {
        if ($this->effectiveDose <= 0) return 0;
        return ($this->num($this->peptideAmount) * 1000) / $this->effectiveDose;
    }

    /**
     * Calculate total peptide needed for cycle (mg)
     */
    #[Computed]
    public function totalPeptideForCycle(): float
    {
        $totalMcg = $this->effectiveDose * $this->num($this->injectionsPerDay) * $this->num($this->cycleDays);
        return $totalMcg / 1000; // Convert to mg
    }

    /**
     * Calculate vials needed for cycle
     */
    #[Computed]
    public function vialsNeeded(): int
    {
        $vialMg = $this->num($this->peptideAmount);
        if ($vialMg <= 0) return 0;
        return (int) ceil($this->totalPeptideForCycle / $vialMg);
    }

    /**
     * Get syringe fill percentage for visualization
     */
    #[Computed]
    public function syringeFillPercent(): float
    {
        return min(($this->unitsToDrawRaw / $this->maxSyringeUnits) * 100, 100);
    }

    public function render()
    {
        return view('livewire.peptide-calculator');
    }
}
