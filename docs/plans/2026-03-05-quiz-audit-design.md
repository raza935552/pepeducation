# 100-Slide Peptide Finder Quiz + Full Stack UX Audit

## Goal
Create a production-quality 100-slide peptide finder quiz via seeder, then take it end-to-end in the browser as 3 different user personas to produce a comprehensive UX audit report covering admin, player, scoring, outcomes, and products.

## Quiz Structure (100 slides)

| Phase | Slides | Purpose |
|-------|--------|---------|
| Phase 1: Discovery | 1-20 | Demographics, health goals, lifestyle |
| Phase 2: Deep Dive | 21-45 | Specific health concerns, symptoms, routines + intermissions |
| Phase 3: Experience & Intent | 46-65 | Peptide experience, purchase intent, barriers (heavy TOF/MOF/BOF scoring) |
| Phase 4: Preferences & Fit | 66-85 | Budget, administration, vendor preferences + loading screens |
| Phase 5: Capture & Reveal | 86-100 | Email capture, loading, peptide reveal, vendor reveal, bridge CTA |

### Slide Type Distribution
- 55 Question (choice) with TOF/MOF/BOF scoring + tags
- 10 Question (text input) for open-ended responses
- 12 Intermission for educational content
- 5 Loading with animated checklists
- 3 Email capture at strategic points
- 5 Peptide reveal for recommendations
- 5 Vendor reveal for product/store options
- 5 Bridge (CTA) slides

### Scoring Design
Every choice question has intentional scoring:
- Beginner/curious answers → TOF +3-5
- Researching answers → MOF +3-5
- Ready-to-buy answers → BOF +3-5
- Tags from OPTION_TAGS constant on every answer

## Outcomes (6)
1. Ready to Buy — Knows Product (answer-based)
2. Ready to Buy — Needs Guidance (segment: bof)
3. Active Researcher (segment: mof)
4. Curious Explorer (segment: tof)
5. Health Goal Match (answer-based)
6. General Fallback (lowest priority)

## Audit Methodology
1. Admin UX — journey map, slide editing, outcome modal, product mapping
2. Player UX — 3 personas (TOF beginner, MOF researcher, BOF buyer)
3. Scoring — verify segments and outcome matching
4. Mobile — responsive check at mobile viewports
5. Edge Cases — back button, session resume, skip logic, empty states

## Output
`docs/plans/2026-03-05-quiz-audit-report.md` with:
- Critical Issues (bugs/blockers)
- UX Pain Points (friction/confusion)
- Missing Features (gaps)
- Scoring Issues (wrong segments/outcomes)
- Recommendations (prioritized fix list)
