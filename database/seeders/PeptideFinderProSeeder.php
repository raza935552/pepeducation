<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizOutcome;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class PeptideFinderProSeeder extends Seeder
{
    private array $slideIds = [];
    private int $order = 0;

    public function run(): void
    {
        $quiz = Quiz::updateOrCreate(
            ['slug' => 'peptide-finder-pro'],
            [
                'name' => 'Peptide Finder Pro',
                'title' => 'Find Your Perfect Peptide',
                'type' => Quiz::TYPE_SEGMENTATION,
                'description' => 'Branching quiz with 3 scenario paths: I Know My Peptides, I Know My Goal, I Want Something New',
                'settings' => [
                    'show_progress_bar' => true,
                    'allow_back' => true,
                    'require_email' => true,
                ],
                'is_active' => true,
            ]
        );

        // Idempotent: clear existing slides and outcomes on re-run
        $quiz->questions()->delete();
        $quiz->outcomes()->delete();
        $this->order = 0;
        $this->slideIds = [];

        // Phase 1: Create all slides
        $this->seedForkSlide($quiz);
        $this->seedPath1KnowMyPeptides($quiz);
        $this->seedPath2KnowMyGoal($quiz);
        $this->seedPath3WantSomethingNew($quiz);

        // Phase 2: Link skip_to references on fork slide
        $this->linkSkipToReferences($quiz);

        // Phase 3: Create outcomes
        $this->seedOutcomes($quiz);

        $this->command->info("Seeded {$this->order} Peptide Finder Pro slides across 3 paths.");
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    private function slide(Quiz $quiz, string $label, array $data): QuizQuestion
    {
        $this->order++;
        $question = $quiz->questions()->create(array_merge([
            'order' => $this->order,
            'is_required' => true,
        ], $data));
        $this->slideIds[$label] = $question->id;
        return $question;
    }

    private function cond(string $label, string $value): array
    {
        return [
            'type' => 'and',
            'conditions' => [
                ['question_id' => $this->slideIds[$label], 'option_value' => $value],
            ],
        ];
    }

    private function condOr(string $label, array $values): array
    {
        $conditions = [];
        foreach ($values as $value) {
            $conditions[] = ['question_id' => $this->slideIds[$label], 'option_value' => $value];
        }
        return ['type' => 'or', 'conditions' => $conditions];
    }

    // ──────────────────────────────────────────────
    // Reusable option sets
    // ──────────────────────────────────────────────

    private function healthGoalOptions(): array
    {
        return [
            ['value' => 'fat_loss', 'label' => 'Lose fat & boost metabolism', 'klaviyo_value' => 'fat_loss', 'tags' => ['interested_weight_loss']],
            ['value' => 'muscle_growth', 'label' => 'Build muscle & recover faster', 'klaviyo_value' => 'muscle_growth', 'tags' => ['interested_muscle_growth']],
            ['value' => 'anti_aging', 'label' => 'Look & feel younger', 'klaviyo_value' => 'anti_aging', 'tags' => ['interested_anti_aging']],
            ['value' => 'injury_recovery', 'label' => 'Heal an injury faster', 'klaviyo_value' => 'injury_recovery', 'tags' => ['interested_injury_recovery']],
            ['value' => 'cognitive', 'label' => 'Think sharper & focus better', 'klaviyo_value' => 'cognitive', 'tags' => ['interested_cognitive']],
            ['value' => 'sleep', 'label' => 'Sleep deeper & wake refreshed', 'klaviyo_value' => 'sleep', 'tags' => ['interested_sleep']],
            ['value' => 'immune', 'label' => 'Strengthen my immune system', 'klaviyo_value' => 'immune', 'tags' => ['interested_immune']],
            ['value' => 'sexual_health', 'label' => 'Improve sexual health & vitality', 'klaviyo_value' => 'sexual_health', 'tags' => ['interested_sexual_health']],
            ['value' => 'gut_health', 'label' => 'Fix my gut & digestion', 'klaviyo_value' => 'gut_health', 'tags' => ['interested_gut_health']],
            ['value' => 'general_wellness', 'label' => 'General wellness & energy', 'klaviyo_value' => 'general_wellness', 'tags' => ['interested_general_wellness']],
        ];
    }

    private function peptideSelectionOptions(): array
    {
        return [
            ['value' => 'bpc-157', 'label' => 'BPC-157', 'klaviyo_value' => 'BPC-157', 'tags' => ['interested_bpc157']],
            ['value' => 'tirzepatide', 'label' => 'Tirzepatide', 'klaviyo_value' => 'Tirzepatide', 'tags' => ['interested_tirzepatide']],
            ['value' => 'semaglutide', 'label' => 'Semaglutide', 'klaviyo_value' => 'Semaglutide', 'tags' => ['interested_semaglutide']],
            ['value' => 'cjc-1295-ipamorelin', 'label' => 'CJC-1295 / Ipamorelin', 'klaviyo_value' => 'CJC-1295/Ipamorelin', 'tags' => ['interested_cjc1295_ipamorelin']],
            ['value' => 'epithalon', 'label' => 'Epithalon', 'klaviyo_value' => 'Epithalon', 'tags' => ['interested_epithalon']],
            ['value' => 'tb-500', 'label' => 'TB-500', 'klaviyo_value' => 'TB-500', 'tags' => ['interested_tb500']],
            ['value' => 'ghk-cu', 'label' => 'GHK-Cu', 'klaviyo_value' => 'GHK-Cu', 'tags' => ['interested_ghk_cu']],
            ['value' => 'semax', 'label' => 'Semax', 'klaviyo_value' => 'Semax', 'tags' => ['interested_semax']],
            ['value' => 'dsip', 'label' => 'DSIP', 'klaviyo_value' => 'DSIP', 'tags' => ['interested_dsip']],
            ['value' => 'thymosin-alpha-1', 'label' => 'Thymosin Alpha 1', 'klaviyo_value' => 'Thymosin Alpha 1', 'tags' => ['interested_thymosin_alpha1']],
            ['value' => 'pt-141', 'label' => 'PT-141', 'klaviyo_value' => 'PT-141', 'tags' => ['interested_pt141']],
            ['value' => 'mk-677', 'label' => 'MK-677 (Ibutamoren)', 'klaviyo_value' => 'MK-677', 'tags' => []],
            ['value' => 'retatrutide', 'label' => 'Retatrutide', 'klaviyo_value' => 'Retatrutide', 'tags' => []],
            ['value' => 'nad-plus', 'label' => 'NAD+', 'klaviyo_value' => 'NAD+', 'tags' => []],
            ['value' => 'other', 'label' => 'Something else', 'klaviyo_value' => 'Other', 'tags' => []],
        ];
    }

    private function genderOptions(): array
    {
        return [
            ['value' => 'male', 'label' => 'Male', 'klaviyo_value' => 'male', 'tags' => ['male']],
            ['value' => 'female', 'label' => 'Female', 'klaviyo_value' => 'female', 'tags' => ['female']],
            ['value' => 'prefer_not', 'label' => 'Prefer not to say', 'klaviyo_value' => 'prefer_not', 'tags' => []],
        ];
    }

    private function ageOptions(): array
    {
        return [
            ['value' => '18-29', 'label' => '18-29', 'klaviyo_value' => '18-29', 'score_tof' => 2, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['age_18_29']],
            ['value' => '30-39', 'label' => '30-39', 'klaviyo_value' => '30-39', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['age_30_39']],
            ['value' => '40-49', 'label' => '40-49', 'klaviyo_value' => '40-49', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 1, 'tags' => ['age_40_49']],
            ['value' => '50-59', 'label' => '50-59', 'klaviyo_value' => '50-59', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 2, 'tags' => ['age_50_59']],
            ['value' => '60+', 'label' => '60+', 'klaviyo_value' => '60+', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 2, 'tags' => ['age_60_plus']],
        ];
    }

    private function buyingPriorityOptions(): array
    {
        return [
            ['value' => 'doctor_guidance', 'label' => "I want a doctor's guidance (telehealth)", 'klaviyo_value' => 'doctor_guidance', 'tags' => ['prefers_telehealth']],
            ['value' => 'research_grade', 'label' => 'I want to do my own research (research-grade)', 'klaviyo_value' => 'research_grade', 'tags' => ['prefers_research_grade']],
            ['value' => 'affordable', 'label' => 'I want the most affordable option', 'klaviyo_value' => 'affordable', 'tags' => ['prefers_affordable', 'price_sensitive']],
        ];
    }

    // ──────────────────────────────────────────────
    // Fork Slide
    // ──────────────────────────────────────────────

    private function seedForkSlide(Quiz $quiz): void
    {
        $this->slide($quiz, 'fork', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What brings you here today?',
            'question_subtext' => 'This helps us personalize your experience.',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'quiz_path',
            'options' => [
                [
                    'value' => 'know_my_peptides',
                    'label' => 'I know my peptides',
                    'klaviyo_value' => 'I Know My Peptides',
                    'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 10,
                    'tags' => ['high_intent', 'advanced'],
                ],
                [
                    'value' => 'know_my_goal',
                    'label' => 'I know my goal',
                    'klaviyo_value' => 'I Know My Goal',
                    'score_tof' => 0, 'score_mof' => 5, 'score_bof' => 5,
                    'tags' => ['researching'],
                ],
                [
                    'value' => 'want_something_new',
                    'label' => 'I want something new',
                    'klaviyo_value' => 'I Want Something New',
                    'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 10,
                    'tags' => ['advanced', 'wants_fresh_start'],
                ],
            ],
        ]);
    }

    private function linkSkipToReferences(Quiz $quiz): void
    {
        $forkSlide = QuizQuestion::find($this->slideIds['fork']);
        if (!$forkSlide) return;

        $options = $forkSlide->options;
        foreach ($options as &$option) {
            $skipTo = match ($option['value']) {
                'know_my_peptides' => $this->slideIds['p1_select_peptide'] ?? null,
                'know_my_goal' => $this->slideIds['p2_health_goal'] ?? null,
                'want_something_new' => $this->slideIds['p3_previous_peptide'] ?? null,
                default => null,
            };
            if ($skipTo) {
                $option['skip_to_question'] = (string) $skipTo;
            }
        }
        unset($option);
        $forkSlide->update(['options' => $options]);
    }

    // ──────────────────────────────────────────────
    // Path 1: "I Know My Peptides" (5 slides)
    // ──────────────────────────────────────────────

    private function seedPath1KnowMyPeptides(Quiz $quiz): void
    {
        $p1Cond = $this->cond('fork', 'know_my_peptides');

        $this->slide($quiz, 'p1_select_peptide', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'Which peptide are you looking for?',
            'question_subtext' => 'Select the peptide you want to buy.',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'peptide_preference',
            'show_conditions' => $p1Cond,
            'options' => $this->peptideSelectionOptions(),
        ]);

        $this->slide($quiz, 'p1_summary', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Great Choice!',
            'content_title' => 'Great Choice!',
            'content_body' => "We've got you covered. Let us find you the best deals from our verified vendors.\n\nAll our recommended vendors provide third-party purity testing (>98% purity guaranteed).",
            'show_conditions' => $p1Cond,
        ]);

        $this->slide($quiz, 'p1_email', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Get Your Deals',
            'klaviyo_property' => 'email',
            'content_title' => 'We found deals for your peptide!',
            'content_body' => 'Enter your email to see vendor pricing and receive exclusive discount codes.',
            'show_conditions' => $p1Cond,
        ]);

        $this->slide($quiz, 'p1_vendor_reveal', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Best Deals For Your Peptide',
            'content_title' => 'Best Deals For Your Peptide',
            'show_conditions' => $p1Cond,
        ]);

        $this->slide($quiz, 'p1_bridge', [
            'slide_type' => QuizQuestion::SLIDE_BRIDGE,
            'question_text' => 'Your Next Step',
            'content_title' => 'Ready to compare all vendors?',
            'content_body' => "Check your inbox for exclusive deals.\n\nOr head to our full price comparison tool to see every option.",
            'cta_text' => 'Compare All Vendors',
            'cta_url' => '/stack-builder',
            'show_conditions' => $p1Cond,
        ]);
    }

    // ──────────────────────────────────────────────
    // Path 2: "I Know My Goal" (23 slides)
    // ──────────────────────────────────────────────

    private function seedPath2KnowMyGoal(Quiz $quiz): void
    {
        $p2Cond = $this->cond('fork', 'know_my_goal');

        // P2-1: Primary Health Goal
        $this->slide($quiz, 'p2_health_goal', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => "What's your #1 health goal right now?",
            'question_subtext' => 'Choose the one that matters most to you.',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'health_goal',
            'show_conditions' => $p2Cond,
            'options' => $this->healthGoalOptions(),
        ]);

        // P2-2: Intermission
        $this->slide($quiz, 'p2_intro', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => "You're Not Alone",
            'content_title' => "You're Not Alone",
            'content_body' => "Over 500,000 Americans are now using peptides as part of their health optimization.\n\nPeptides are naturally occurring amino acid chains that signal your body to perform specific functions — from healing to fat loss to mental clarity.\n\nLet's find the right one for you.",
            'show_conditions' => $p2Cond,
        ]);

        // P2-3: Gender
        $this->slide($quiz, 'p2_gender', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'Which best describes you?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'gender',
            'show_conditions' => $p2Cond,
            'options' => $this->genderOptions(),
        ]);

        // P2-4: Age Range
        $this->slide($quiz, 'p2_age', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What is your age range?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'age_range',
            'show_conditions' => $p2Cond,
            'options' => $this->ageOptions(),
        ]);

        // P2-5: Health Rating
        $this->slide($quiz, 'p2_health_rating', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'How would you rate your overall health?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'health_rating',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'excellent', 'label' => 'Excellent', 'klaviyo_value' => 'excellent', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['high_intent']],
                ['value' => 'good', 'label' => 'Good', 'klaviyo_value' => 'good', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => []],
                ['value' => 'fair', 'label' => 'Fair', 'klaviyo_value' => 'fair', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => []],
                ['value' => 'poor', 'label' => 'Poor', 'klaviyo_value' => 'poor', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // P2-6: Exercise Frequency
        $this->slide($quiz, 'p2_exercise', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'How often do you exercise?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'exercise_frequency',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'daily', 'label' => 'Daily', 'klaviyo_value' => 'daily', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 2, 'tags' => []],
                ['value' => '3-5x', 'label' => '3-5x per week', 'klaviyo_value' => '3-5x', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => []],
                ['value' => '1-2x', 'label' => '1-2x per week', 'klaviyo_value' => '1-2x', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => []],
                ['value' => 'rarely', 'label' => 'Rarely', 'klaviyo_value' => 'rarely', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // P2-7: Diet Quality
        $this->slide($quiz, 'p2_diet', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'How would you describe your diet?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'diet_quality',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'strict', 'label' => 'Strict / optimized', 'klaviyo_value' => 'strict', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['high_intent']],
                ['value' => 'balanced', 'label' => 'Balanced', 'klaviyo_value' => 'balanced', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => []],
                ['value' => 'inconsistent', 'label' => 'Inconsistent', 'klaviyo_value' => 'inconsistent', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => []],
                ['value' => 'poor', 'label' => 'Needs work', 'klaviyo_value' => 'poor', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // P2-8: Current Supplements
        $this->slide($quiz, 'p2_supplements', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What supplements do you currently take?',
            'question_subtext' => 'Select all that apply.',
            'question_type' => QuizQuestion::TYPE_MULTIPLE,
            'max_selections' => 5,
            'klaviyo_property' => 'current_supplements',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'vitamins', 'label' => 'Vitamins / minerals', 'klaviyo_value' => 'vitamins', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => []],
                ['value' => 'protein', 'label' => 'Protein / creatine', 'klaviyo_value' => 'protein', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => []],
                ['value' => 'hrt', 'label' => 'Hormone therapy (TRT, HRT)', 'klaviyo_value' => 'hrt', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['advanced', 'high_intent']],
                ['value' => 'peptides', 'label' => 'Already using peptides', 'klaviyo_value' => 'peptides', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5, 'tags' => ['advanced', 'wants_to_add']],
                ['value' => 'nothing', 'label' => 'Nothing', 'klaviyo_value' => 'nothing', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner']],
            ],
        ]);

        // P2-9: Intermission — "What Are Peptides?"
        $this->slide($quiz, 'p2_what_peptides', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'What Are Peptides?',
            'content_title' => 'What Are Peptides?',
            'content_body' => "Peptides are short chains of amino acids — the same building blocks your body already uses.\n\nUnlike supplements, peptides are bioidentical compounds that work with your body's own signaling systems.\n\nThink of them as precision tools for specific health goals — backed by real clinical research.",
            'show_conditions' => $p2Cond,
        ]);

        // P2-10: Sleep Quality
        $this->slide($quiz, 'p2_sleep', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'How would you rate your sleep quality?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'sleep_quality',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'excellent', 'label' => 'Excellent — 7-9 hrs, wake refreshed', 'klaviyo_value' => 'excellent', 'tags' => []],
                ['value' => 'good', 'label' => 'Good — usually fine', 'klaviyo_value' => 'good', 'tags' => []],
                ['value' => 'fair', 'label' => 'Fair — could be better', 'klaviyo_value' => 'fair', 'tags' => []],
                ['value' => 'poor', 'label' => 'Poor — I struggle with sleep', 'klaviyo_value' => 'poor', 'tags' => ['interested_sleep']],
            ],
        ]);

        // P2-11: Stress Level
        $this->slide($quiz, 'p2_stress', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'How would you rate your stress level?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'stress_level',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'low', 'label' => 'Low — pretty relaxed', 'klaviyo_value' => 'low', 'tags' => []],
                ['value' => 'moderate', 'label' => 'Moderate — manageable', 'klaviyo_value' => 'moderate', 'tags' => []],
                ['value' => 'high', 'label' => 'High — affects daily life', 'klaviyo_value' => 'high', 'tags' => []],
                ['value' => 'very_high', 'label' => 'Very high — overwhelmed', 'klaviyo_value' => 'very_high', 'tags' => ['interested_cognitive']],
            ],
        ]);

        // P2-12: Energy Level
        $this->slide($quiz, 'p2_energy', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'How is your energy throughout the day?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'energy_level',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'great', 'label' => 'Great — steady all day', 'klaviyo_value' => 'great', 'tags' => []],
                ['value' => 'ok', 'label' => 'OK — occasional dips', 'klaviyo_value' => 'ok', 'tags' => []],
                ['value' => 'low', 'label' => 'Low — tired often', 'klaviyo_value' => 'low', 'tags' => []],
                ['value' => 'very_low', 'label' => 'Very low — exhausted', 'klaviyo_value' => 'very_low', 'tags' => ['interested_general_wellness']],
            ],
        ]);

        // P2-13: Intermission — Social Proof
        $this->slide($quiz, 'p2_social_proof', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Join Thousands',
            'content_title' => 'Join Thousands of Optimizers',
            'content_body' => "87% of people who start peptide therapy wish they had started sooner.\n\nYour answers are helping us build a personalized recommendation backed by clinical research.",
            'content_source' => 'Based on Professor Peptides community survey, 2025',
            'show_conditions' => $p2Cond,
        ]);

        // P2-14: Recovery Needs
        $this->slide($quiz, 'p2_recovery', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'Do you have any recovery or healing needs?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'recovery_needs',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'injury', 'label' => 'Yes — recovering from injury', 'klaviyo_value' => 'injury', 'tags' => ['interested_injury_recovery']],
                ['value' => 'chronic', 'label' => 'Yes — chronic pain or inflammation', 'klaviyo_value' => 'chronic', 'tags' => ['interested_injury_recovery']],
                ['value' => 'workout', 'label' => 'Yes — faster workout recovery', 'klaviyo_value' => 'workout', 'tags' => ['interested_muscle_growth']],
                ['value' => 'none', 'label' => 'No specific recovery needs', 'klaviyo_value' => 'none', 'tags' => []],
            ],
        ]);

        // P2-15: Experience Level
        $this->slide($quiz, 'p2_experience', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'How much do you know about peptides?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'experience_level',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'beginner', 'label' => "I'm brand new", 'klaviyo_value' => 'beginner', 'score_tof' => 5, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner', 'needs_education']],
                ['value' => 'intermediate', 'label' => "I've done some research", 'klaviyo_value' => 'intermediate', 'score_tof' => 0, 'score_mof' => 5, 'score_bof' => 0, 'tags' => ['intermediate', 'researching']],
                ['value' => 'advanced', 'label' => "I've used peptides before", 'klaviyo_value' => 'advanced', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5, 'tags' => ['advanced', 'high_intent']],
            ],
        ]);

        // P2-16: Injection Comfort
        $this->slide($quiz, 'p2_injection', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'How do you feel about injections?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'injection_comfort',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'comfortable', 'label' => 'Totally fine with it', 'klaviyo_value' => 'comfortable', 'tags' => []],
                ['value' => 'ok', 'label' => "I'd try it if needed", 'klaviyo_value' => 'ok', 'tags' => []],
                ['value' => 'prefer_oral', 'label' => 'I prefer oral / nasal options', 'klaviyo_value' => 'prefer_oral', 'tags' => ['barrier_needles']],
                ['value' => 'not_comfortable', 'label' => 'Not comfortable with needles', 'klaviyo_value' => 'not_comfortable', 'tags' => ['barrier_needles']],
            ],
        ]);

        // P2-17: Buying Priority
        $this->slide($quiz, 'p2_buying_priority', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'When it comes to buying peptides, what matters most?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'buying_priority',
            'show_conditions' => $p2Cond,
            'options' => $this->buyingPriorityOptions(),
        ]);

        // P2-18: Budget
        $this->slide($quiz, 'p2_budget', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What monthly budget are you comfortable with for peptide therapy?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'budget',
            'show_conditions' => $p2Cond,
            'options' => [
                ['value' => 'under_100', 'label' => 'Under $100/month', 'klaviyo_value' => 'under_100', 'tags' => ['price_sensitive']],
                ['value' => '100-250', 'label' => '$100-250/month', 'klaviyo_value' => '100-250', 'tags' => []],
                ['value' => '250-500', 'label' => '$250-500/month', 'klaviyo_value' => '250-500', 'tags' => ['high_intent']],
                ['value' => 'over_500', 'label' => '$500+/month', 'klaviyo_value' => 'over_500', 'tags' => ['high_intent']],
                ['value' => 'not_sure', 'label' => 'Not sure yet', 'klaviyo_value' => 'not_sure', 'tags' => ['researching']],
            ],
        ]);

        // P2-19: Email Capture
        $this->slide($quiz, 'p2_email', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Get Your Results',
            'klaviyo_property' => 'email',
            'content_title' => 'Your personalized recommendation is ready!',
            'content_body' => 'Enter your email to see your matched peptide, vendor comparison, and receive our Peptide Research Guide.',
            'show_conditions' => $p2Cond,
        ]);

        // P2-20: Loading Screen
        $this->slide($quiz, 'p2_loading', [
            'slide_type' => QuizQuestion::SLIDE_LOADING,
            'question_text' => 'Analyzing Your Profile',
            'content_title' => 'Analyzing Your Profile',
            'content_body' => "Cross-referencing your health goals...\nAnalyzing clinical data...\nMatching peptide profiles...\nComparing vendor options...\nPreparing your recommendation...",
            'auto_advance_seconds' => 4,
            'show_conditions' => $p2Cond,
        ]);

        // P2-21: Peptide Reveal
        $this->slide($quiz, 'p2_peptide_reveal', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Your #1 Peptide Match',
            'content_title' => 'Your #1 Peptide Match',
            'show_conditions' => $p2Cond,
        ]);

        // P2-22: Vendor Reveal
        $this->slide($quiz, 'p2_vendor_reveal', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Where To Get Your Peptides',
            'content_title' => 'Where To Get Your Peptides',
            'show_conditions' => $p2Cond,
        ]);

        // P2-23: Bridge CTA
        $this->slide($quiz, 'p2_bridge', [
            'slide_type' => QuizQuestion::SLIDE_BRIDGE,
            'question_text' => 'Your Next Step',
            'content_title' => 'Your personalized guide is on its way!',
            'content_body' => "Ready to compare prices for {{peptide_name}} across trusted vendors?\n\nWe've vetted every vendor for purity testing, shipping speed, and customer reviews.",
            'cta_text' => 'Compare Prices Now',
            'cta_url' => '/stack-builder',
            'show_conditions' => $p2Cond,
        ]);
    }

    // ──────────────────────────────────────────────
    // Path 3: "I Want Something New" (10 slides)
    // ──────────────────────────────────────────────

    private function seedPath3WantSomethingNew(Quiz $quiz): void
    {
        $p3Cond = $this->cond('fork', 'want_something_new');

        // P3-1: What were you using?
        $this->slide($quiz, 'p3_previous_peptide', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'Awesome! What peptide were you using?',
            'question_subtext' => 'Select the peptide you want to switch from.',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'peptide_preference',
            'show_conditions' => $p3Cond,
            'options' => $this->peptideSelectionOptions(),
        ]);

        // P3-2: Why switching?
        $this->slide($quiz, 'p3_switch_reason', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'Why are you looking to switch?',
            'question_subtext' => 'This helps us find a better match for you.',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'switch_reason',
            'show_conditions' => $p3Cond,
            'options' => [
                ['value' => 'side_effects', 'label' => 'Side effects', 'klaviyo_value' => 'side_effects', 'tags' => ['barrier_safety']],
                ['value' => 'not_working', 'label' => 'Not seeing results', 'klaviyo_value' => 'not_working', 'tags' => []],
                ['value' => 'too_expensive', 'label' => 'Too expensive', 'klaviyo_value' => 'too_expensive', 'tags' => ['price_sensitive']],
                ['value' => 'hard_to_source', 'label' => 'Hard to find a reliable source', 'klaviyo_value' => 'hard_to_source', 'tags' => ['barrier_sourcing']],
                ['value' => 'want_variety', 'label' => 'Just want to try something different', 'klaviyo_value' => 'want_variety', 'tags' => ['wants_fresh_start']],
            ],
        ]);

        // P3-3: Stay or switch category?
        $this->slide($quiz, 'p3_category_choice', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'Do you want to stay in the same category or try a different one?',
            'question_subtext' => "We'll find the best alternative for you either way.",
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'category_preference',
            'show_conditions' => $p3Cond,
            'options' => [
                ['value' => 'same_category', 'label' => 'Stay in the same category', 'klaviyo_value' => 'same_category', 'tags' => []],
                ['value' => 'different_category', 'label' => 'Try a different category', 'klaviyo_value' => 'different_category', 'tags' => []],
            ],
        ]);

        // P3-4: Health goal picker (only if "different_category" selected)
        $this->slide($quiz, 'p3_new_goal', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What health goal do you want to explore?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'health_goal',
            'show_conditions' => $this->cond('p3_category_choice', 'different_category'),
            'options' => $this->healthGoalOptions(),
        ]);

        // P3-5: Intermission
        $this->slide($quiz, 'p3_encouragement', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Smart Move',
            'content_title' => "Smart Move — Let's Find Something Better",
            'content_body' => "Based on your experience, we can recommend alternatives that might work better for you.\n\nOur recommendations are based on clinical data, user reviews, and verified vendor testing.",
            'show_conditions' => $p3Cond,
        ]);

        // P3-6: Email Capture
        $this->slide($quiz, 'p3_email', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Get Your Alternative',
            'klaviyo_property' => 'email',
            'content_title' => 'Your alternative recommendation is ready!',
            'content_body' => 'Enter your email to see your matched peptide and vendor comparison.',
            'show_conditions' => $p3Cond,
        ]);

        // P3-7: Loading
        $this->slide($quiz, 'p3_loading', [
            'slide_type' => QuizQuestion::SLIDE_LOADING,
            'question_text' => 'Finding Your Next Peptide',
            'content_title' => 'Finding Your Next Peptide',
            'content_body' => "Reviewing your previous experience...\nAnalyzing alternatives...\nComparing clinical data...\nChecking vendor availability...",
            'auto_advance_seconds' => 3,
            'show_conditions' => $p3Cond,
        ]);

        // P3-8: Peptide Reveal
        $this->slide($quiz, 'p3_peptide_reveal', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Your Next Peptide',
            'content_title' => 'Your Next Peptide',
            'show_conditions' => $p3Cond,
        ]);

        // P3-9: Vendor Reveal
        $this->slide($quiz, 'p3_vendor_reveal', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Where To Get It',
            'content_title' => 'Where To Get Your New Peptide',
            'show_conditions' => $p3Cond,
        ]);

        // P3-10: Bridge CTA
        $this->slide($quiz, 'p3_bridge', [
            'slide_type' => QuizQuestion::SLIDE_BRIDGE,
            'question_text' => 'Your Next Step',
            'content_title' => 'Time for something better!',
            'content_body' => "Your personalized recommendation has been sent to your inbox.\n\nCompare vendor pricing now to get started.",
            'cta_text' => 'Compare Prices Now',
            'cta_url' => '/stack-builder',
            'show_conditions' => $p3Cond,
        ]);
    }

    // ──────────────────────────────────────────────
    // Outcomes
    // ──────────────────────────────────────────────

    private function seedOutcomes(Quiz $quiz): void
    {
        $outcomes = [
            [
                'name' => 'Path 1: Known Peptide',
                'conditions' => ['type' => 'answer', 'question' => 'quiz_path', 'value' => 'know_my_peptides'],
                'priority' => 1,
                'result_title' => 'Great Choice!',
                'result_message' => 'We found the best vendors and pricing for your peptide.',
                'redirect_url' => '/stack-builder',
            ],
            [
                'name' => 'Path 2: Known Goal',
                'conditions' => ['type' => 'answer', 'question' => 'quiz_path', 'value' => 'know_my_goal'],
                'priority' => 2,
                'result_title' => 'Your Match is Ready',
                'result_message' => "Based on your health profile, we've found your ideal peptide match.",
                'redirect_url' => '/stack-builder',
            ],
            [
                'name' => 'Path 3: Switching',
                'conditions' => ['type' => 'answer', 'question' => 'quiz_path', 'value' => 'want_something_new'],
                'priority' => 3,
                'result_title' => 'Your New Recommendation',
                'result_message' => "We've found the perfect alternative based on your experience.",
                'redirect_url' => '/stack-builder',
            ],
            [
                'name' => 'General Fallback',
                'conditions' => ['type' => 'fallback'],
                'priority' => 10,
                'result_title' => 'Your Peptide Journey',
                'result_message' => 'Check out our peptide guide to learn more about your options.',
                'redirect_url' => '/peptides',
            ],
        ];

        foreach ($outcomes as $data) {
            $quiz->outcomes()->create(array_merge($data, ['is_active' => true]));
        }

        $this->command->info('Seeded ' . count($outcomes) . ' quiz outcomes.');
    }
}
