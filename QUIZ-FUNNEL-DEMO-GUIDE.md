# Quiz Funnel — Demo Cheat Sheet

**Status**: ALL COMPLETE — 6/6 Modules Done, 5/5 Journeys Tested

**Built**: 53 slides, 8 slide types, 5 journeys, 20 peptide recommendations, 19 vendor products

---

## SCREENSHARE STEP-BY-STEP

### Part 1: Admin Side

**Step 1 — Quiz List**
- Go to `/admin/quizzes`
- Show "Peptide Quiz Funnel" — 53 questions, Active

**Step 2 — Quiz Edit Page**
- Click Edit on the quiz
- Scroll through all 53 slides
- Point out: color-coded type badges, branching arrows, condition indicators

**Step 3 — Edit a Slide**
- Click edit on slide #6 (intermission with dynamic content)
- Show: slide type dropdown (8 types), title/body fields
- Show: dynamic content section — `health_goal` key with 11 variants
- Show: show conditions builder — AND/OR logic

**Step 4 — Results Bank**
- Go to `/admin/results-bank`
- Show 20 peptide recommendations in the list
- Click edit on Tirzepatide
- Show: linked Stack Product dropdown, star rating, testimonial

---

### Part 2: Take the Quiz (User Side)

**Step 5 — Start Quiz**
- Go to `/quiz/peptide-quiz-funnel`
- Select "I'm brand new to peptides" (TOF path)

**Step 6 — Answer Questions**
- Health goal → "Lose weight & burn fat"
- Experience → "First time trying peptides"
- Go through remaining questions

**Step 7 — Watch Dynamic Slides**
- Intermission slide changes content based on fat_loss answer
- Loading screen plays animated checklist, auto-advances
- Skip email capture

**Step 8 — The Reveal**
- Peptide Reveal: **Tirzepatide**, 4.9 stars, "Excellent Match"
- Vendor Reveal: PeptideSciences $149.99 vs SwissChems $154.99 with "Best" badge
- Bridge: "Ready to compare prices for Tirzepatide?" → links to Stack Builder

---

### Key Things to Call Out During Demo

- Progress bar is journey-aware (not "Step 2 of 53")
- Dynamic content swaps based on user answers
- `{{peptide_name}}` tokens replaced with actual peptide name
- Vendor pricing pulled from existing Stack Builder products
- Back button works across all slide types
- Zero new tables for vendors — reuses Stack Builder infrastructure
- Admin can manage everything: slides, branching, conditions, recommendations, vendor links

---

## What Was Built (Quick Summary)

| Module | What |
|--------|------|
| 1. Slide Types | 8 slide types + admin UI |
| 2. Branching | Skip-to routing + show conditions |
| 3. Results Bank | 20 peptide recommendations |
| 4. Vendor Integration | Linked to Stack Builder products/stores |
| 5. Dynamic Content | Per-answer content variants + token interpolation |
| 6. Full Assembly | 53 slides, 5 journeys, all E2E tested |
