# Quiz Funnel Upgrade — Module Tracker

## Module 1: Slide Type System + Admin
**Status: COMPLETE (pending migration run)**

- [x] Migration: `2026_02_17_100000_extend_quiz_questions_for_slides.php`
- [x] Model: `QuizQuestion` — slide type constants, fillable, casts, helpers
- [x] Livewire: `QuizPlayer` — slide-type dispatch, advanceSlide, submitTextAnswer, navigation history
- [x] View: `quiz-player.blade.php` — slide-type router with @switch
- [x] Slide templates (8): question, question-text, intermission, loading, email-capture, peptide-reveal, vendor-reveal, bridge
- [x] Admin modal: slide_type dropdown, conditional fields per type
- [x] Admin row: color-coded badges, content preview, JSON data for edit
- [x] Controller: `QuizQuestionController` — slideRules() helper, all new fields in store/update
- [ ] **Run migration**: `php artisan migrate` (MySQL was offline)

### Browser Test Checklist
- [ ] Admin: Create quiz with 5 slides: question → intermission → question → loading → question
- [ ] Admin: Verify slide_type dropdown shows all types
- [ ] Admin: Verify fields change when switching slide types
- [ ] User: Play quiz, question slides work as before
- [ ] User: Intermission shows title + body + Next button
- [ ] User: Loading screen shows animation, auto-advances after timer
- [ ] User: Existing quizzes still work perfectly (backwards compatible)
- [ ] User: Progress bar works correctly across mixed slide types
- [ ] User: Back button works across slide types

---

## Module 2: Branching & Conditional Logic
**Status: COMPLETE**

- [x] `app/Services/Quiz/QuizFunnelEngine.php` — branching logic service
- [x] `QuizPlayer.php` — use engine for next/prev navigation
- [x] Admin: skip_to dropdown in option editor + show_conditions builder

---

## Module 3: Results Bank + Peptide Reveal
**Status: COMPLETE**

- [x] Migration: `results_bank` table
- [x] Model: `ResultsBank` + resolve() method
- [x] Controller + views: Admin CRUD
- [x] Route + sidebar menu item
- [x] Seeder: 20 entries from docx
- [x] `peptide-reveal.blade.php` — full reveal UI
- [x] `QuizPlayer.php` — ResultsBank lookup

---

## Module 4: ResultsBank → StackProduct → Vendor (REVISED)
**Status: COMPLETE**

Architecture: ResultsBank → StackProduct (FK) → stores() pivot → store-comparison partial
Result: 0 new tables, 0 new models, 0 new admin pages for vendors. Just 1 FK column + view updates.

- [x] Migration: `add_stack_product_id_to_results_bank` — nullable FK, nullOnDelete
- [x] Model: `ResultsBank` — `stackProduct()` BelongsTo, eager-load in resolve()
- [x] Seeder: 3 new StackProducts (Tirzepatide, LL-37, Larazotide) with store pricing
- [x] Seeder: `ResultsBankSeeder` — links all 20 entries to StackProducts by slug
- [x] Admin: StackProduct dropdown on results-bank form
- [x] Controller: `ResultsBankController` — accepts stack_product_id, passes $stackProducts
- [x] `QuizPlayer.php` — `getStackProductProperty()` computed property
- [x] `peptide-reveal.blade.php` — "Learn More" links to related peptide page via StackProduct
- [x] `vendor-reveal.blade.php` — full store comparison via `store-comparison.blade.php` partial
- [x] `bridge.blade.php` — context-aware CTA with peptide name + Stack Builder link

---

## Module 5: Dynamic Content + Loading Screen Polish
**Status: NOT STARTED**

- [ ] `QuizFunnelEngine` — resolveDynamicContent() + interpolate()
- [ ] `QuizPlayer.php` — dynamic content resolution per slide
- [ ] `intermission.blade.php` — render dynamic content
- [ ] `loading.blade.php` — polish animation
- [ ] Admin: dynamic content map editor

---

## Module 6: Full Journey Assembly + E2E
**Status: NOT STARTED**

- [ ] `QuizFunnelSeeder.php` — complete quiz with all paths
- [ ] E2E testing across TOF/MOF/BOF journeys
