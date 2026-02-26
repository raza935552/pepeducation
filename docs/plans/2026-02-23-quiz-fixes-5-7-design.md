# Quiz Fixes 5-7 Design

## Fix #5: Email Validation
- Upgrade validation from `required|email` to `required|email:rfc`
- Add friendly custom error message
- Files: `QuizPlayer.php`, `email-capture.blade.php`

## Fix #6: Score-Based Outcome Matching
- Wire up existing `matchesScore()` as third tier in `determineOutcome()`
- Order: answer-based → segment-based → score-based → fallback
- File: `QuizPlayer.php`

## Fix #7: Per-Quiz Analytics Page
- Completion funnel (starts, completions, rate)
- Drop-off per question (how many answered each question)
- Outcome distribution (shown_count per outcome)
- Recent responses table (last 20)
- Files: `QuizController.php`, new `analytics.blade.php`, `routes/admin.php`
