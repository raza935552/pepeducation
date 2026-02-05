# Module: Calculator

## Overview
Interactive dosing calculator with two modes: Reconstitution and Mix Solutions. Accessible from navbar and peptide detail pages.

---

## Access Points

1. **Navbar Button** - "Calculator" in main navigation
2. **Peptide Page** - "Calculator" button in reconstitution section
3. **Mobile Bottom Nav** - Calculator icon
4. **Keyboard Shortcut** - Could implement Cmd/Ctrl+K style

---

## UI Component

The calculator appears as a **slide-out modal/drawer** from the right side of the screen.

```
┌─────────────────────────────────────────┐
│ Calculator                           ✕  │
├─────────────────────────────────────────┤
│ [Reconstitute]  [Mix Solutions]         │
│     (active)       (inactive)           │
├─────────────────────────────────────────┤
│                                         │
│ ... calculator content ...              │
│                                         │
└─────────────────────────────────────────┘
```

---

## Tab 1: Reconstitute

### Purpose
Calculate how much bacteriostatic water to add and what volume to draw for each dose.

### UI Layout

```
┌─────────────────────────────────────────┐
│ Calculator                           ✕  │
├─────────────────────────────────────────┤
│ [Reconstitute ●]  [Mix Solutions]       │
├─────────────────────────────────────────┤
│                                         │
│ 1  What's in your vial?     [Start Over]│
│                                         │
│ Peptide Amount:        Vial Size:       │
│ ┌─────────────────┐   [2 mL] [3 mL]    │
│ │ 0           mg  │   [5 mL] [10 mL]   │
│ └─────────────────┘                     │
│                                         │
│ 2  What's your dose per injection?      │
│                                         │
│ Your dose:                              │
│ ┌─────────────────────────────────────┐ │
│ │ 0                    [mg ●] [mcg]   │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ 3  How often will you inject?           │
│                                         │
│ ┌─────────┐ ┌─────────┐ ┌─────────┐    │
│ │  Daily  │ │  Twice  │ │ Weekly  │    │
│ │ 1x/day  │ │ 2x/day  │ │ 1x/wk   │    │
│ └─────────┘ └─────────┘ └─────────┘    │
│                                         │
└─────────────────────────────────────────┘
```

### Input Fields

| Field | Type | Options/Validation |
|-------|------|-------------------|
| Peptide Amount | Number input | Min: 0, suffix: "mg" |
| Vial Size | Button group | 2 mL, 3 mL, 5 mL, 10 mL |
| Your Dose | Number input | Min: 0 |
| Dose Unit | Toggle | mg / mcg |
| Frequency | Button group | Daily (1x/day), Twice (2x/day), Weekly (1x/wk) |

### Calculation Logic

```javascript
// Inputs
const peptideAmount = 5; // mg
const vialSize = 2; // mL
const dosePerInjection = 250; // mcg
const frequency = 'daily'; // 'daily', 'twice', 'weekly'

// Calculations
const concentration = peptideAmount / vialSize; // mg per mL
const doseInMg = dosePerInjection / 1000; // Convert mcg to mg
const volumePerDose = doseInMg / concentration; // mL per injection

// Injections per vial
const totalDoses = peptideAmount / doseInMg;

// Days supply
const dosesPerDay = frequency === 'twice' ? 2 : (frequency === 'weekly' ? 1/7 : 1);
const daysSupply = totalDoses / dosesPerDay;
```

### Results Display (when values entered)

```
┌─────────────────────────────────────────┐
│ Results                                 │
├─────────────────────────────────────────┤
│                                         │
│ Add this much BAC water:                │
│ ┌───────────────────────────────────┐   │
│ │           2.0 mL                  │   │
│ └───────────────────────────────────┘   │
│                                         │
│ Draw this much per injection:           │
│ ┌───────────────────────────────────┐   │
│ │          0.10 mL (10 units)       │   │
│ └───────────────────────────────────┘   │
│                                         │
│ Vial will last:                         │
│ ┌───────────────────────────────────┐   │
│ │     20 doses (~20 days supply)    │   │
│ └───────────────────────────────────┘   │
│                                         │
└─────────────────────────────────────────┘
```

---

## Tab 2: Mix Solutions

### Purpose
Calculate combined concentration when mixing multiple peptide solutions in one syringe.

### UI Layout

```
┌─────────────────────────────────────────┐
│ Calculator                           ✕  │
├─────────────────────────────────────────┤
│ [Reconstitute]  [Mix Solutions ●]       │
├─────────────────────────────────────────┤
│                                         │
│ 1  How many solutions are you mixing?   │
│                                         │
│    [2] [3] [4] [5]                      │
│                                         │
│ 2  Enter details for each solution:     │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ Solution 1                          │ │
│ │                                     │ │
│ │ [Know concentration] [Enter vial ●] │ │
│ │                                     │ │
│ │ Peptide in vial:                    │ │
│ │ ┌─────────────────────────────┐     │ │
│ │ │ 0                       mg  │     │ │
│ │ └─────────────────────────────┘     │ │
│ │                                     │ │
│ │ BAC water added:                    │ │
│ │ ┌─────────────────────────────┐     │ │
│ │ │ 0                       mL  │     │ │
│ │ └─────────────────────────────┘     │ │
│ │                                     │ │
│ │ Volume to use from this vial:       │ │
│ │ ┌─────────────────────────────┐     │ │
│ │ │ 0                       mL  │     │ │
│ │ └─────────────────────────────┘     │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ Solution 2                          │ │
│ │ ... same fields ...                 │ │
│ └─────────────────────────────────────┘ │
│                                         │
└─────────────────────────────────────────┘
```

### Input Modes per Solution

**Mode 1: Know Concentration**
```
Concentration:
┌─────────────────────────────────┐
│ 0                       mg/mL   │
└─────────────────────────────────┘

Volume to use:
┌─────────────────────────────────┐
│ 0                       mL      │
└─────────────────────────────────┘
```

**Mode 2: Enter Vial Details**
```
Peptide in vial:
┌─────────────────────────────────┐
│ 0                       mg      │
└─────────────────────────────────┘

BAC water added:
┌─────────────────────────────────┐
│ 0                       mL      │
└─────────────────────────────────┘

Volume to use from this vial:
┌─────────────────────────────────┐
│ 0                       mL      │
└─────────────────────────────────┘
```

### Calculation Logic

```javascript
// For each solution
const solutions = [
  { peptide: 5, water: 2, volume: 0.1 }, // Solution 1
  { peptide: 10, water: 2, volume: 0.1 }, // Solution 2
];

// Calculate concentration for each
const withConcentration = solutions.map(s => ({
  ...s,
  concentration: s.peptide / s.water, // mg/mL
  peptideInDose: (s.peptide / s.water) * s.volume // mg
}));

// Total volume
const totalVolume = solutions.reduce((sum, s) => sum + s.volume, 0);

// Total peptide per component
const totalsByPeptide = withConcentration.map(s => ({
  peptideAmount: s.peptideInDose,
  percentOfTotal: (s.volume / totalVolume) * 100
}));
```

### Results Display

```
┌─────────────────────────────────────────┐
│ Mixed Solution Results                  │
├─────────────────────────────────────────┤
│                                         │
│ Total injection volume: 0.20 mL         │
│                                         │
│ Contains:                               │
│ • Solution 1: 0.25 mg (2.5 mg/mL)      │
│ • Solution 2: 0.50 mg (5.0 mg/mL)      │
│                                         │
│ Combined concentration:                 │
│ • Solution 1: 1.25 mg/mL in mix        │
│ • Solution 2: 2.50 mg/mL in mix        │
│                                         │
└─────────────────────────────────────────┘
```

---

## Mobile Bottom Navigation

The calculator is also accessible via mobile bottom nav:

```
┌───────────────────────────────────────────────────┐
│  🏠      📋      🤖      🧮      ⋯                │
│ Home   Browse  Assistant Calculator  More        │
└───────────────────────────────────────────────────┘
```

---

## State Management

```typescript
interface CalculatorState {
  activeTab: 'reconstitute' | 'mix';

  // Reconstitute tab
  reconstitute: {
    peptideAmount: number;
    vialSize: number;
    doseAmount: number;
    doseUnit: 'mg' | 'mcg';
    frequency: 'daily' | 'twice' | 'weekly';
  };

  // Mix tab
  mix: {
    solutionCount: 2 | 3 | 4 | 5;
    solutions: {
      mode: 'concentration' | 'vial';
      // Concentration mode
      concentration?: number;
      volumeToUse?: number;
      // Vial mode
      peptideInVial?: number;
      waterAdded?: number;
      volumeFromVial?: number;
    }[];
  };
}
```

---

## Features to Implement

1. **Persist Last Values** - LocalStorage to remember user's last inputs
2. **Unit Conversion** - Auto-convert between mg/mcg, mL/units
3. **Syringe Unit Display** - Show insulin syringe units (0.10 mL = 10 units)
4. **Copy Results** - Button to copy calculation to clipboard
5. **Save Calculation** - Save to user account for future reference
6. **Pre-fill from Peptide** - When opened from peptide page, pre-fill typical values
