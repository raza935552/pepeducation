# Quiz Admin Redesign — Tabbed Journey Editor

**Date:** 2026-03-03
**Status:** Approved
**Scope:** UI redesign only (no schema changes)

## Problem

Marketing team struggles to manage the quiz admin because:
1. **No flow visualization** — can't see which slides lead where or how paths diverge
2. **Flat slide list** — 30+ slides in one list with no grouping by journey phase
3. **Cryptic labels** — scores shown as `T:3 M:0 B:0`, skip-to shown as arrow icons, conditions shown as gear icons with no detail

## Solution: Tabbed Journey Editor

Replace the flat list with a tabbed interface grouped by journey phase, with human-readable labels.

### Page Layout

- **Tab bar:** Journey Map | Shared Start | TOF Path | MOF Path | BOF Path
- **Main area:** Slide cards for the active tab (5-10 per tab instead of 30+ flat)
- **Sidebar:** Collapsible quiz settings, stats/URL, outcomes grouped by segment

### Journey Map Tab (Overview)

Visual overview showing all phases as clickable boxes with:
- Slide count per phase
- Branching connections with human-readable labels (e.g., "Brand new" → TOF Path)
- Outcome summary at bottom

### Enhanced Slide Cards

Each slide card shows:
- Drag handle, order number, type badge (color-coded), title
- **"Visible when:"** — plain English condition (replaces gear icon)
- **"Next →"** — destination slide name (replaces arrow icon)
- **Options as sub-cards** with:
  - "Leans TOF (+3 awareness)" instead of `T:3 M:0 B:0`
  - "Jumps to: [slide name]" instead of arrow icon
- Content preview for intermission/loading slides
- Expand/collapse for details

### Phase Grouping Logic (No Migration)

Phases inferred from `show_conditions`:
- **Shared Start:** No conditions (shown to everyone)
- **TOF Path:** Conditions reference segmentation answer = `brand_new`
- **MOF Path:** Conditions reference = `researching`
- **BOF Path:** Conditions reference = `ready_to_buy` + sub-paths

### Outcomes Section

Moved to sidebar, grouped by segment (TOF/MOF/BOF) with human-readable condition summaries.

## Files to Modify

1. `app/Http/Controllers/Admin/QuizController.php` — add phase grouping logic to `edit()`
2. `resources/views/admin/quizzes/edit.blade.php` — rebuild with tabs layout
3. `resources/views/admin/quizzes/partials/question-row.blade.php` — enhanced card design
4. `resources/views/admin/quizzes/partials/outcome-row.blade.php` — grouped display
5. **New:** `resources/views/admin/quizzes/partials/journey-map.blade.php` — overview tab

## Files Unchanged

- `partials/question-modal.blade.php` — reused as-is
- `partials/outcome-modal.blade.php` — reused as-is
- All models, migrations, services — untouched
- Frontend quiz player — untouched

## Tech Stack

- Blade + Alpine.js (existing stack, no new packages)
- Existing reorder endpoint works within tabs
- Existing question/outcome modals reused
