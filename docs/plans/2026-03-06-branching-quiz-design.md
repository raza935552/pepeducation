# Branching Quiz with 3 Scenario Paths — Design

## Problem

The current quiz is a linear 100-slide funnel that treats all users the same. Users have different starting points:
- Some already know which peptide they want and just need vendor deals
- Some know their health goal but need peptide recommendations
- Some are switching from a peptide they don't like and need alternatives

## Solution

Create a new quiz with a first-slide fork into 3 self-contained paths. Each path is focused and ends at its results. The existing quiz (peptide-finder-100) stays untouched.

## The Three Paths

### Slide 1: The Fork

**"What brings you here today?"** — three options using `skip_to_question`:
- "I know my peptides" → Path 1
- "I know my goal" → Path 2
- "I want something new" → Path 3

### Path 1: "I Know My Peptides" (~5 slides)

Short, transactional flow for experienced users who know what they want.

| Slide | Type | Content |
|-------|------|---------|
| 2 | `question` | Select peptide from StackProducts list |
| 3 | `intermission` | "Great choice!" — peptide summary |
| 4 | `email_capture` | Capture email before deals |
| 5 | `peptide_reveal` | Peptide info (dynamic based on selection) |
| 6 | `vendor_reveal` | Vendor deals for selected product |
| 7 | `bridge` | CTA — "Learn more" / "Visit store" |

### Path 2: "I Know My Goal" (~25 slides)

Gets the bulk of existing profiling questions from the current quiz.

| Slide | Type | Content |
|-------|------|---------|
| 10 | `question` | Primary Health Goal |
| 11 | `intermission` | "You're Not Alone" education |
| 12-20 | `question` | Profiling: exercise, diet, supplements, sleep, stress, energy, conditions, etc. |
| 21 | `intermission` | Social proof |
| 22-28 | `question` | Deep dive: symptoms, weight mgmt, aging, gut health, etc. |
| 29 | `question` | Experience level |
| 30 | `question` | Budget / buying priority |
| 31 | `email_capture` | Email capture |
| 32 | `loading` | "Analyzing your profile..." |
| 33 | `peptide_reveal` | Primary recommendation + collapsible alternatives (multi-peptide feature) |
| 34 | `vendor_reveal` | Store comparison for all matched peptides |
| 35 | `bridge` | CTA |

### Path 3: "I Want Something New" (~10 slides)

Conversational replacement flow for users switching peptides.

| Slide | Type | Content |
|-------|------|---------|
| 40 | `question` | "What peptide were you using?" (select from StackProducts) |
| 41 | `question` | "Why are you looking to switch?" (Side effects / Not seeing results / Too expensive / Hard to source / Want variety) |
| 42 | `question` | "Stay in same category or try something different?" |
| 43 | `question` | Health goal picker — `show_conditions`: only if "Different category" on slide 42 |
| 44 | `intermission` | "Based on your experience, here's what we'd suggest..." |
| 45 | `email_capture` | Email capture |
| 46 | `loading` | "Finding your next peptide..." |
| 47 | `peptide_reveal` | Recommendation (excludes their previous peptide) + alternatives |
| 48 | `vendor_reveal` | Store comparison |
| 49 | `bridge` | CTA |

## Technical Implementation

- **New seeder**: `PeptideFinderProSeeder` — creates a new Quiz record with all slides
- **Branching**: `skip_to_question` on slide 1 options + `show_conditions` on slide 43
- **Reuses existing infrastructure**: ResultsBank, StackProducts, multi-peptide `resolveAll()`, exclusion logic, store-comparison partial
- **No new models or migrations** — just a new Quiz with QuizQuestions
- **Existing quiz untouched** — peptide-finder-100 remains as-is

## Exclusion Logic (Path 3)

When user selects "Stay in same category":
1. Look up the category of their previous peptide via ResultsBank
2. Use `resolveAll()` for that category
3. Exclude their previous peptide from results
4. Show remaining alternatives

When user selects "Different category":
1. Show health goal picker (slide 43 via `show_conditions`)
2. Use `resolveAll()` for the new goal
3. No exclusion needed (different category)

## Admin Capabilities

Paths are built in the seeder (Option A). Admin can:
- Edit slide content (text, options, intermission copy)
- Toggle slides active/inactive
- Modify `show_conditions` via the existing slide editor
- Cannot create new branching paths without developer help
