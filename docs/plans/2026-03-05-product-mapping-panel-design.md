# Product Mapping Panel — Design Document

**Date**: 2026-03-05
**Goal**: Make product recommendations visible to marketing people directly in the quiz editor.

## Problem

Marketing team can't see which quiz answers lead to which product recommendations. The Results Bank (a separate admin page) holds the mapping, but there's no connection visible from the quiz editor. Marketing has to mentally piece together: Quiz → Results Bank → Stack Products.

## Solution: Product Mapping Panel

Add a read-only "Product Recommendations" card to the quiz editor sidebar that shows the full `health_goal → peptide` mapping at a glance, with edit links to the Results Bank.

## Design

### Visual Layout

New card in the quiz editor sidebar (below Outcomes section):

```
┌──────────────────────────────────────────┐
│  Product Recommendations                 │
│  Determined by "health_goal" answer      │
│  TOF = Beginner · MOF/BOF = varies       │
│                                          │
│  Fat Loss ──────────── Tirzepatide  [✎]  │
│  Muscle Growth ──────── CJC-1295    [✎]  │
│  Anti-Aging ──────────── GHK-Cu     [✎]  │
│  ...etc for all 10 goals                 │
│                                          │
│  ✅ All 10 goals have products assigned  │
│  ⚠️ 2 goals missing products (if any)    │
└──────────────────────────────────────────┘
```

### Data Flow

1. `QuizController::edit()` queries `results_bank` table grouped by experience level
2. Passes `$resultsBankEntries` to the view
3. New partial `product-mapping-panel.blade.php` renders the mapping
4. Each row links to the Results Bank edit page

### What's shown

- Health goal label (human-readable)
- Mapped peptide name
- Edit link → Results Bank edit page for that entry
- Coverage indicator: green check if all goals covered, yellow warning for gaps
- Two columns: Beginner and Advanced (collapsed by default, since TOF = beginner)

## Scope

- **Backend**: Add Results Bank query to `QuizController::edit()`
- **Frontend**: New Blade partial included in `edit.blade.php`
- **No database changes**
- **No quiz player logic changes**

## Approach chosen

Approach A (Product Mapping Panel) — simplest option that gives marketing visibility without refactoring the data model. Results Bank still handles editing; this just surfaces the mapping in context.
