<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizOutcome;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class PeptideFinderSeeder extends Seeder
{
    private array $slideIds = [];
    private int $order = 0;

    public function run(): void
    {
        $quiz = Quiz::updateOrCreate(
            ['slug' => 'peptide-finder-100'],
            [
                'name' => 'Peptide Finder (100 Slides)',
                'title' => 'Find Your Perfect Peptide Match',
                'type' => Quiz::TYPE_SEGMENTATION,
                'description' => '100-slide comprehensive peptide finder quiz with full TOF/MOF/BOF scoring',
                'settings' => [
                    'show_progress_bar' => true,
                    'allow_back' => true,
                    'require_email' => true,
                ],
                'is_active' => true,
            ]
        );

        $quiz->questions()->delete();
        $quiz->outcomes()->delete();
        $this->order = 0;
        $this->slideIds = [];

        $this->seedPhase1Discovery($quiz);
        $this->seedPhase2DeepDive($quiz);
        $this->seedPhase3ExperienceIntent($quiz);
        $this->seedPhase4PreferencesFit($quiz);
        $this->seedPhase5CaptureReveal($quiz);
        $this->seedOutcomes($quiz);

        $this->command->info("Seeded {$this->order} peptide finder slides.");
    }

    // ─── HELPERS ─────────────────────────────────────────────────────────

    private function slide(Quiz $quiz, string $label, array $attrs): void
    {
        $this->order++;
        $defaults = [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'order' => $this->order,
            'is_required' => true,
            'options' => [],
        ];
        $q = $quiz->questions()->create(array_merge($defaults, $attrs));
        $this->slideIds[$label] = $q->id;
    }

    // ─── PHASE 1: DISCOVERY (Slides 1-20) ───────────────────────────────

    private function seedPhase1Discovery(Quiz $quiz): void
    {
        // 1. Gender
        $this->slide($quiz, 'gender', [
            'question_text' => 'Which best describes you?',
            'question_subtext' => 'This helps us personalize your peptide recommendations.',
            'klaviyo_property' => 'gender',
            'options' => [
                ['value' => 'male', 'label' => 'Male', 'klaviyo_value' => 'Male', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['male']],
                ['value' => 'female', 'label' => 'Female', 'klaviyo_value' => 'Female', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['female']],
                ['value' => 'prefer_not', 'label' => 'Prefer not to say', 'klaviyo_value' => 'Prefer not to say', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // 2. Age Range
        $this->slide($quiz, 'age', [
            'question_text' => 'What is your age range?',
            'klaviyo_property' => 'age_range',
            'options' => [
                ['value' => '18-29', 'label' => '18–29', 'klaviyo_value' => '18-29', 'score_tof' => 2, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['age_18_29']],
                ['value' => '30-39', 'label' => '30–39', 'klaviyo_value' => '30-39', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['age_30_39']],
                ['value' => '40-49', 'label' => '40–49', 'klaviyo_value' => '40-49', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 1, 'tags' => ['age_40_49']],
                ['value' => '50-59', 'label' => '50–59', 'klaviyo_value' => '50-59', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 2, 'tags' => ['age_50_59']],
                ['value' => '60+', 'label' => '60+', 'klaviyo_value' => '60+', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 2, 'tags' => ['age_60_plus']],
            ],
        ]);

        // 3. Primary Health Goal
        $this->slide($quiz, 'health_goal', [
            'question_text' => 'What is your #1 health goal right now?',
            'question_subtext' => 'Choose the one that matters most to you.',
            'klaviyo_property' => 'health_goal',
            'options' => [
                ['value' => 'fat_loss', 'label' => 'Lose weight / burn fat', 'klaviyo_value' => 'Fat Loss', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['interested_weight_loss']],
                ['value' => 'muscle_growth', 'label' => 'Build muscle / recover faster', 'klaviyo_value' => 'Muscle Growth', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_muscle_growth']],
                ['value' => 'anti_aging', 'label' => 'Anti-aging / longevity', 'klaviyo_value' => 'Anti-Aging', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_anti_aging']],
                ['value' => 'cognitive', 'label' => 'Mental clarity / focus', 'klaviyo_value' => 'Cognitive', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_cognitive']],
                ['value' => 'injury_recovery', 'label' => 'Heal an injury / joint pain', 'klaviyo_value' => 'Injury Recovery', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['interested_injury_recovery']],
                ['value' => 'sleep', 'label' => 'Better sleep', 'klaviyo_value' => 'Sleep', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_sleep']],
                ['value' => 'general_wellness', 'label' => 'Overall health & wellness', 'klaviyo_value' => 'General Wellness', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['interested_general_wellness']],
            ],
        ]);

        // 4. Intermission — educational
        $this->slide($quiz, 'intro_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Great choice!',
            'content_title' => 'You\'re Not Alone',
            'content_body' => "Over 500,000 Americans are now using peptides as part of their health optimization.\n\nPeptides are naturally occurring amino acid chains that signal your body to perform specific functions — from healing to fat loss to mental clarity.\n\nLet's find the right one for you.",
        ]);

        // 5. Secondary Health Goal
        $this->slide($quiz, 'secondary_goal', [
            'question_text' => 'What\'s your second most important health goal?',
            'question_subtext' => 'Many peptides address multiple goals.',
            'klaviyo_property' => 'secondary_goal',
            'options' => [
                ['value' => 'fat_loss', 'label' => 'Weight management', 'klaviyo_value' => 'Fat Loss', 'score_tof' => 1, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['interested_weight_loss']],
                ['value' => 'muscle_growth', 'label' => 'Muscle & recovery', 'klaviyo_value' => 'Muscle Growth', 'score_tof' => 1, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['interested_muscle_growth']],
                ['value' => 'anti_aging', 'label' => 'Anti-aging', 'klaviyo_value' => 'Anti-Aging', 'score_tof' => 1, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['interested_anti_aging']],
                ['value' => 'immune', 'label' => 'Immune support', 'klaviyo_value' => 'Immune', 'score_tof' => 1, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['interested_immune']],
                ['value' => 'sexual_health', 'label' => 'Sexual health', 'klaviyo_value' => 'Sexual Health', 'score_tof' => 1, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['interested_sexual_health']],
                ['value' => 'gut_health', 'label' => 'Gut health', 'klaviyo_value' => 'Gut Health', 'score_tof' => 1, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['interested_gut_health']],
                ['value' => 'none', 'label' => 'Just focusing on one goal', 'klaviyo_value' => 'None', 'score_tof' => 2, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // 6. Current Health Rating
        $this->slide($quiz, 'health_rating', [
            'question_text' => 'How would you rate your overall health right now?',
            'klaviyo_property' => 'health_rating',
            'options' => [
                ['value' => 'excellent', 'label' => 'Excellent — I\'m optimizing', 'klaviyo_value' => 'Excellent', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 3, 'tags' => ['high_intent']],
                ['value' => 'good', 'label' => 'Good — but could be better', 'klaviyo_value' => 'Good', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['researching']],
                ['value' => 'fair', 'label' => 'Fair — dealing with some issues', 'klaviyo_value' => 'Fair', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['needs_education']],
                ['value' => 'poor', 'label' => 'Poor — need significant help', 'klaviyo_value' => 'Poor', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['needs_education']],
            ],
        ]);

        // 7. Exercise Frequency
        $this->slide($quiz, 'exercise_freq', [
            'question_text' => 'How often do you exercise?',
            'klaviyo_property' => 'exercise_frequency',
            'options' => [
                ['value' => 'daily', 'label' => 'Daily or almost daily', 'klaviyo_value' => 'Daily', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 2, 'tags' => ['interested_muscle_growth']],
                ['value' => '3-5x', 'label' => '3–5 times per week', 'klaviyo_value' => '3-5x', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => []],
                ['value' => '1-2x', 'label' => '1–2 times per week', 'klaviyo_value' => '1-2x', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => []],
                ['value' => 'rarely', 'label' => 'Rarely or never', 'klaviyo_value' => 'Rarely', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['just_curious']],
            ],
        ]);

        // 8. Diet Quality
        $this->slide($quiz, 'diet', [
            'question_text' => 'How would you describe your diet?',
            'klaviyo_property' => 'diet_quality',
            'options' => [
                ['value' => 'strict', 'label' => 'Very strict — macros, meal prep, supplements', 'klaviyo_value' => 'Strict', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['high_intent']],
                ['value' => 'balanced', 'label' => 'Balanced — mostly healthy choices', 'klaviyo_value' => 'Balanced', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['researching']],
                ['value' => 'inconsistent', 'label' => 'Inconsistent — on and off', 'klaviyo_value' => 'Inconsistent', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['needs_education']],
                ['value' => 'poor', 'label' => 'Not great — working on it', 'klaviyo_value' => 'Poor', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['just_curious']],
            ],
        ]);

        // 9. Current Supplements
        $this->slide($quiz, 'supplements', [
            'question_text' => 'What supplements or treatments are you currently using?',
            'question_subtext' => 'Select all that apply.',
            'question_type' => QuizQuestion::TYPE_MULTIPLE,
            'max_selections' => 5,
            'klaviyo_property' => 'current_supplements',
            'options' => [
                ['value' => 'vitamins', 'label' => 'Vitamins & minerals', 'klaviyo_value' => 'Vitamins', 'score_tof' => 1, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
                ['value' => 'protein', 'label' => 'Protein powder / creatine', 'klaviyo_value' => 'Protein', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 1, 'tags' => ['interested_muscle_growth']],
                ['value' => 'hrt', 'label' => 'Hormone replacement (TRT/HRT)', 'klaviyo_value' => 'HRT', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['advanced', 'high_intent']],
                ['value' => 'peptides', 'label' => 'Already using peptides', 'klaviyo_value' => 'Peptides', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5, 'tags' => ['advanced', 'wants_to_add']],
                ['value' => 'nothing', 'label' => 'Nothing currently', 'klaviyo_value' => 'Nothing', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner']],
            ],
        ]);

        // 10. Intermission — peptide education
        $this->slide($quiz, 'education_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Did you know?',
            'content_title' => 'What Are Peptides?',
            'content_body' => "Peptides are short chains of amino acids — the building blocks of proteins.\n\nYour body naturally produces peptides to regulate everything from healing to metabolism to brain function.\n\nTherapeutic peptides are bioidentical copies designed to amplify these natural processes.",
            'content_source' => 'National Institutes of Health, Peptide Therapeutics Foundation',
        ]);

        // 11. Sleep Quality
        $this->slide($quiz, 'sleep_quality', [
            'question_text' => 'How is your sleep quality?',
            'klaviyo_property' => 'sleep_quality',
            'options' => [
                ['value' => 'great', 'label' => 'Great — 7-9 hours of deep sleep', 'klaviyo_value' => 'Great', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 1, 'tags' => []],
                ['value' => 'ok', 'label' => 'Okay — could be better', 'klaviyo_value' => 'Okay', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_sleep']],
                ['value' => 'poor', 'label' => 'Poor — I struggle to sleep well', 'klaviyo_value' => 'Poor', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_sleep']],
                ['value' => 'terrible', 'label' => 'Terrible — chronic insomnia', 'klaviyo_value' => 'Terrible', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['interested_sleep', 'needs_education']],
            ],
        ]);

        // 12. Stress Level
        $this->slide($quiz, 'stress_level', [
            'question_text' => 'How would you rate your daily stress level?',
            'klaviyo_property' => 'stress_level',
            'options' => [
                ['value' => 'low', 'label' => 'Low — I manage it well', 'klaviyo_value' => 'Low', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 1, 'tags' => []],
                ['value' => 'moderate', 'label' => 'Moderate — comes and goes', 'klaviyo_value' => 'Moderate', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_cognitive']],
                ['value' => 'high', 'label' => 'High — it affects my health', 'klaviyo_value' => 'High', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_cognitive', 'interested_sleep']],
                ['value' => 'extreme', 'label' => 'Extreme — burnout territory', 'klaviyo_value' => 'Extreme', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['needs_education']],
            ],
        ]);

        // 13. Energy Level
        $this->slide($quiz, 'energy_level', [
            'question_text' => 'How are your energy levels throughout the day?',
            'klaviyo_property' => 'energy_level',
            'options' => [
                ['value' => 'high', 'label' => 'High — sustained all day', 'klaviyo_value' => 'High', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 2, 'tags' => ['high_intent']],
                ['value' => 'moderate', 'label' => 'Moderate — afternoon crashes', 'klaviyo_value' => 'Moderate', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['interested_cognitive']],
                ['value' => 'low', 'label' => 'Low — fatigued often', 'klaviyo_value' => 'Low', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['needs_education']],
                ['value' => 'variable', 'label' => 'All over the place', 'klaviyo_value' => 'Variable', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_general_wellness']],
            ],
        ]);

        // 14. Medical Conditions
        $this->slide($quiz, 'medical', [
            'question_text' => 'Are you currently dealing with any of these?',
            'question_subtext' => 'Select all that apply. This helps us recommend safely.',
            'question_type' => QuizQuestion::TYPE_MULTIPLE,
            'max_selections' => 5,
            'klaviyo_property' => 'medical_conditions',
            'options' => [
                ['value' => 'joint_pain', 'label' => 'Joint pain or arthritis', 'klaviyo_value' => 'Joint Pain', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_injury_recovery']],
                ['value' => 'gut_issues', 'label' => 'Digestive / gut issues', 'klaviyo_value' => 'Gut Issues', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_gut_health']],
                ['value' => 'brain_fog', 'label' => 'Brain fog / poor concentration', 'klaviyo_value' => 'Brain Fog', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_cognitive']],
                ['value' => 'weight', 'label' => 'Weight management struggles', 'klaviyo_value' => 'Weight', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_weight_loss']],
                ['value' => 'none', 'label' => 'None of the above', 'klaviyo_value' => 'None', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 1, 'tags' => ['interested_general_wellness']],
            ],
        ]);

        // 15. Motivation Source
        $this->slide($quiz, 'motivation', [
            'question_text' => 'What motivated you to explore peptides?',
            'klaviyo_property' => 'motivation_source',
            'options' => [
                ['value' => 'social_media', 'label' => 'Saw it on social media / podcast', 'klaviyo_value' => 'Social Media', 'score_tof' => 4, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['just_curious', 'beginner']],
                ['value' => 'doctor', 'label' => 'Doctor or healthcare provider mentioned it', 'klaviyo_value' => 'Doctor', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 3, 'tags' => ['high_intent']],
                ['value' => 'friend', 'label' => 'Friend or family recommended', 'klaviyo_value' => 'Friend', 'score_tof' => 2, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['researching']],
                ['value' => 'research', 'label' => 'My own research', 'klaviyo_value' => 'Research', 'score_tof' => 0, 'score_mof' => 4, 'score_bof' => 1, 'tags' => ['researching']],
                ['value' => 'already_using', 'label' => 'Already using — want to optimize', 'klaviyo_value' => 'Already Using', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5, 'tags' => ['advanced', 'wants_to_upgrade']],
            ],
        ]);

        // 16. Intermission — social proof
        $this->slide($quiz, 'social_proof_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'You\'re in good company',
            'content_title' => 'Join Thousands of Optimizers',
            'content_body' => "Our quiz has helped 12,000+ people find their perfect peptide match.\n\n92% of quiz takers said they felt more confident about their peptide decision after completing this quiz.\n\nKeep going — your personalized recommendation is just minutes away.",
        ]);

        // 17. Lifestyle Type
        $this->slide($quiz, 'lifestyle', [
            'question_text' => 'Which best describes your lifestyle?',
            'klaviyo_property' => 'lifestyle_type',
            'options' => [
                ['value' => 'athlete', 'label' => 'Competitive athlete / bodybuilder', 'klaviyo_value' => 'Athlete', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['advanced', 'interested_muscle_growth']],
                ['value' => 'active', 'label' => 'Active professional — gym, outdoor sports', 'klaviyo_value' => 'Active', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['intermediate']],
                ['value' => 'desk', 'label' => 'Desk job — sedentary most of the day', 'klaviyo_value' => 'Desk Job', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['beginner']],
                ['value' => 'busy_parent', 'label' => 'Busy parent — no time for self', 'klaviyo_value' => 'Busy Parent', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner', 'needs_education']],
            ],
        ]);

        // 18. Recovery Needs
        $this->slide($quiz, 'recovery_needs', [
            'question_text' => 'Do you have any recovery or healing needs?',
            'klaviyo_property' => 'recovery_needs',
            'options' => [
                ['value' => 'post_surgery', 'label' => 'Recovering from surgery', 'klaviyo_value' => 'Post Surgery', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['interested_injury_recovery', 'interested_bpc157']],
                ['value' => 'sports_injury', 'label' => 'Sports or workout injury', 'klaviyo_value' => 'Sports Injury', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['interested_injury_recovery', 'interested_tb500']],
                ['value' => 'chronic_pain', 'label' => 'Chronic pain or inflammation', 'klaviyo_value' => 'Chronic Pain', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['interested_injury_recovery', 'interested_bpc157']],
                ['value' => 'no', 'label' => 'No — just optimizing', 'klaviyo_value' => 'No', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 1, 'tags' => ['interested_general_wellness']],
            ],
        ]);

        // 19. How Soon Do You Want Results?
        $this->slide($quiz, 'timeline', [
            'question_text' => 'How soon do you want to see results?',
            'klaviyo_property' => 'result_timeline',
            'options' => [
                ['value' => 'asap', 'label' => 'As soon as possible', 'klaviyo_value' => 'ASAP', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 4, 'tags' => ['high_intent', 'ready_to_buy']],
                ['value' => '1-3_months', 'label' => 'Within 1–3 months', 'klaviyo_value' => '1-3 Months', 'score_tof' => 0, 'score_mof' => 3, 'score_bof' => 1, 'tags' => ['researching']],
                ['value' => 'no_rush', 'label' => 'No rush — just exploring', 'klaviyo_value' => 'No Rush', 'score_tof' => 4, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['just_curious']],
                ['value' => 'already_started', 'label' => 'Already started — want to adjust', 'klaviyo_value' => 'Already Started', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 4, 'tags' => ['advanced', 'wants_to_upgrade']],
            ],
        ]);

        // 20. Open Text — What concerns you most?
        $this->slide($quiz, 'concerns_text', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION_TEXT,
            'question_text' => 'What concerns you most about your health right now?',
            'question_subtext' => 'Tell us in your own words — this helps personalize your recommendation.',
            'question_type' => QuizQuestion::TYPE_TEXT,
            'klaviyo_property' => 'health_concerns_text',
            'settings' => ['placeholder' => 'e.g., I\'ve been dealing with low energy and joint pain for months...'],
            'is_required' => false,
        ]);
    }

    // ─── PHASE 2: DEEP DIVE (Slides 21-45) ────────────────────────────

    private function seedPhase2DeepDive(Quiz $quiz): void
    {
        // 21. Specific Symptoms
        $this->slide($quiz, 'symptoms', [
            'question_text' => 'Which of these symptoms are you experiencing?',
            'question_subtext' => 'Select all that apply.',
            'question_type' => QuizQuestion::TYPE_MULTIPLE,
            'max_selections' => 6,
            'klaviyo_property' => 'symptoms',
            'options' => [
                ['value' => 'fatigue', 'label' => 'Chronic fatigue', 'klaviyo_value' => 'Fatigue', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_general_wellness']],
                ['value' => 'joint_pain', 'label' => 'Joint or muscle pain', 'klaviyo_value' => 'Joint Pain', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['interested_injury_recovery', 'interested_bpc157']],
                ['value' => 'brain_fog', 'label' => 'Brain fog', 'klaviyo_value' => 'Brain Fog', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_cognitive', 'interested_semax']],
                ['value' => 'weight_gain', 'label' => 'Unexplained weight gain', 'klaviyo_value' => 'Weight Gain', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_weight_loss', 'interested_tirzepatide']],
                ['value' => 'low_libido', 'label' => 'Low libido', 'klaviyo_value' => 'Low Libido', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_sexual_health', 'interested_pt141']],
                ['value' => 'none', 'label' => 'None of these', 'klaviyo_value' => 'None', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 1, 'tags' => []],
            ],
        ]);

        // 22. Pain Scale
        $this->slide($quiz, 'pain_scale', [
            'question_text' => 'On a scale of 1-10, how much does pain affect your daily life?',
            'klaviyo_property' => 'pain_scale',
            'options' => [
                ['value' => '1-3', 'label' => '1–3 (Minimal)', 'klaviyo_value' => 'Minimal', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 1, 'tags' => []],
                ['value' => '4-6', 'label' => '4–6 (Moderate)', 'klaviyo_value' => 'Moderate', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['interested_injury_recovery']],
                ['value' => '7-9', 'label' => '7–9 (Severe)', 'klaviyo_value' => 'Severe', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['interested_injury_recovery', 'interested_bpc157']],
                ['value' => '10', 'label' => '10 (Debilitating)', 'klaviyo_value' => 'Debilitating', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 3, 'tags' => ['interested_injury_recovery', 'high_intent']],
            ],
        ]);

        // 23. Intermission — healing science
        $this->slide($quiz, 'healing_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'The Science of Healing',
            'content_title' => 'How Peptides Accelerate Recovery',
            'content_body' => "BPC-157 and TB-500 are two of the most studied healing peptides.\n\nBPC-157 promotes angiogenesis (new blood vessel formation) and has shown remarkable results in healing tendons, ligaments, and gut tissue.\n\nTB-500 upregulates cell-building proteins to speed muscle and tissue repair.\n\nMany practitioners combine both for synergistic healing effects.",
            'content_source' => 'Journal of Physiology and Pharmacology, 2018',
        ]);

        // 24. Cognitive Function
        $this->slide($quiz, 'cognitive_function', [
            'question_text' => 'How sharp is your mental focus and memory?',
            'klaviyo_property' => 'cognitive_function',
            'options' => [
                ['value' => 'sharp', 'label' => 'Sharp — I want to get even better', 'klaviyo_value' => 'Sharp', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 2, 'tags' => ['interested_cognitive', 'high_intent']],
                ['value' => 'declining', 'label' => 'Noticeably declining', 'klaviyo_value' => 'Declining', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['interested_cognitive', 'interested_semax']],
                ['value' => 'struggling', 'label' => 'Really struggling', 'klaviyo_value' => 'Struggling', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_cognitive', 'needs_education']],
                ['value' => 'fine', 'label' => 'Fine — not my main concern', 'klaviyo_value' => 'Fine', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // 25. Weight Management Approach
        $this->slide($quiz, 'weight_approach', [
            'question_text' => 'What has your weight management journey been like?',
            'klaviyo_property' => 'weight_approach',
            'options' => [
                ['value' => 'tried_everything', 'label' => 'Tried everything — nothing sticks', 'klaviyo_value' => 'Tried Everything', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['interested_weight_loss', 'interested_tirzepatide']],
                ['value' => 'need_boost', 'label' => 'On track but need a boost', 'klaviyo_value' => 'Need Boost', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['interested_weight_loss', 'interested_semaglutide']],
                ['value' => 'just_starting', 'label' => 'Just starting my weight loss journey', 'klaviyo_value' => 'Just Starting', 'score_tof' => 3, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_weight_loss', 'beginner']],
                ['value' => 'not_relevant', 'label' => 'Weight isn\'t my concern', 'klaviyo_value' => 'Not Relevant', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // 26. Aging Concerns
        $this->slide($quiz, 'aging_concerns', [
            'question_text' => 'Which aging-related concern matters most to you?',
            'klaviyo_property' => 'aging_concern',
            'options' => [
                ['value' => 'skin', 'label' => 'Skin aging, wrinkles', 'klaviyo_value' => 'Skin', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_anti_aging', 'interested_ghk_cu']],
                ['value' => 'energy', 'label' => 'Loss of energy and vitality', 'klaviyo_value' => 'Energy', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['interested_anti_aging', 'interested_cjc1295_ipamorelin']],
                ['value' => 'hormonal', 'label' => 'Hormonal decline', 'klaviyo_value' => 'Hormonal', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 3, 'tags' => ['interested_anti_aging', 'advanced']],
                ['value' => 'cellular', 'label' => 'Cellular aging / telomeres', 'klaviyo_value' => 'Cellular', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['interested_anti_aging', 'interested_epithalon']],
                ['value' => 'not_concerned', 'label' => 'Not my main concern', 'klaviyo_value' => 'Not Concerned', 'score_tof' => 1, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // 27. Intermission — anti-aging science
        $this->slide($quiz, 'aging_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'The Future of Anti-Aging',
            'content_title' => 'Peptides & Longevity',
            'content_body' => "Epithalon is the only known substance that activates telomerase in human cells.\n\nTelomeres — the protective caps on your chromosomes — shorten with age. Epithalon helps maintain their length.\n\nGHK-Cu, a copper peptide, has been shown to reset over 4,000 genes to a more youthful pattern of expression.",
            'content_source' => 'Khavinson et al., Bulletin of Experimental Biology, 2003',
        ]);

        // 28. Immune System
        $this->slide($quiz, 'immune_system', [
            'question_text' => 'How often do you get sick?',
            'klaviyo_property' => 'immune_strength',
            'options' => [
                ['value' => 'rarely', 'label' => 'Rarely — strong immune system', 'klaviyo_value' => 'Rarely', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 1, 'tags' => []],
                ['value' => 'occasionally', 'label' => 'Occasionally — a few times a year', 'klaviyo_value' => 'Occasionally', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_immune']],
                ['value' => 'frequently', 'label' => 'Frequently — always catching something', 'klaviyo_value' => 'Frequently', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_immune', 'interested_thymosin_alpha1']],
                ['value' => 'compromised', 'label' => 'I have an autoimmune condition', 'klaviyo_value' => 'Compromised', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['interested_immune', 'interested_thymosin_alpha1']],
            ],
        ]);

        // 29. Gut Health
        $this->slide($quiz, 'gut_health', [
            'question_text' => 'How would you describe your digestive health?',
            'klaviyo_property' => 'gut_health',
            'options' => [
                ['value' => 'great', 'label' => 'Great — no issues', 'klaviyo_value' => 'Great', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 1, 'tags' => []],
                ['value' => 'bloating', 'label' => 'Frequent bloating or discomfort', 'klaviyo_value' => 'Bloating', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['interested_gut_health', 'interested_bpc157']],
                ['value' => 'ibs', 'label' => 'IBS or similar condition', 'klaviyo_value' => 'IBS', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['interested_gut_health', 'interested_bpc157']],
                ['value' => 'leaky_gut', 'label' => 'Leaky gut / food sensitivities', 'klaviyo_value' => 'Leaky Gut', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['interested_gut_health', 'interested_bpc157']],
            ],
        ]);

        // 30. Sexual Health
        $this->slide($quiz, 'sexual_health', [
            'question_text' => 'Are you experiencing any sexual health concerns?',
            'klaviyo_property' => 'sexual_health',
            'options' => [
                ['value' => 'low_desire', 'label' => 'Low desire / libido', 'klaviyo_value' => 'Low Desire', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['interested_sexual_health', 'interested_pt141']],
                ['value' => 'performance', 'label' => 'Performance issues', 'klaviyo_value' => 'Performance', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['interested_sexual_health', 'interested_pt141']],
                ['value' => 'no_concerns', 'label' => 'No concerns', 'klaviyo_value' => 'No Concerns', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
                ['value' => 'prefer_not', 'label' => 'Prefer not to answer', 'klaviyo_value' => 'Prefer Not', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // 31. Intermission — personalized approach
        $this->slide($quiz, 'personalized_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Building your profile...',
            'content_title' => 'Your Personalized Approach',
            'content_body' => "Based on your answers so far, we\'re building a comprehensive health profile.\n\nThe next section will help us understand your experience level and intent — so we can match you with the right peptide AND the right vendor.\n\nAlmost there!",
        ]);

        // 32. Body Composition Goals
        $this->slide($quiz, 'body_comp', [
            'question_text' => 'What\'s your body composition goal?',
            'klaviyo_property' => 'body_comp_goal',
            'options' => [
                ['value' => 'lose_fat', 'label' => 'Primarily lose fat', 'klaviyo_value' => 'Lose Fat', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_weight_loss', 'interested_tirzepatide']],
                ['value' => 'build_muscle', 'label' => 'Primarily build muscle', 'klaviyo_value' => 'Build Muscle', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 1, 'tags' => ['interested_muscle_growth', 'interested_cjc1295_ipamorelin']],
                ['value' => 'recomp', 'label' => 'Both — recomposition', 'klaviyo_value' => 'Recomp', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['interested_muscle_growth', 'interested_weight_loss']],
                ['value' => 'maintain', 'label' => 'Maintain current physique', 'klaviyo_value' => 'Maintain', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 1, 'tags' => ['interested_general_wellness']],
            ],
        ]);

        // 33. Hormone History
        $this->slide($quiz, 'hormone_history', [
            'question_text' => 'Have you ever had your hormones tested?',
            'klaviyo_property' => 'hormone_history',
            'options' => [
                ['value' => 'yes_optimized', 'label' => 'Yes — and I\'m on TRT/HRT', 'klaviyo_value' => 'Yes Optimized', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 4, 'tags' => ['advanced', 'high_intent']],
                ['value' => 'yes_low', 'label' => 'Yes — my levels were low', 'klaviyo_value' => 'Yes Low', 'score_tof' => 0, 'score_mof' => 3, 'score_bof' => 1, 'tags' => ['researching']],
                ['value' => 'no_interested', 'label' => 'No — but I\'m interested', 'klaviyo_value' => 'No Interested', 'score_tof' => 2, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['researching', 'needs_education']],
                ['value' => 'no_not_relevant', 'label' => 'No — not relevant to me', 'klaviyo_value' => 'No', 'score_tof' => 2, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner']],
            ],
        ]);

        // 34. Daily Routine Text
        $this->slide($quiz, 'routine_text', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION_TEXT,
            'question_text' => 'Describe your current daily health routine in a few words.',
            'question_subtext' => 'This helps us understand where peptides could fit in.',
            'question_type' => QuizQuestion::TYPE_TEXT,
            'klaviyo_property' => 'daily_routine',
            'settings' => ['placeholder' => 'e.g., Morning gym, supplements, healthy lunch, 8hrs sleep...'],
            'is_required' => false,
        ]);

        // 35. Previous Health Investments
        $this->slide($quiz, 'health_investments', [
            'question_text' => 'What have you invested in for your health before?',
            'question_subtext' => 'Select all that apply.',
            'question_type' => QuizQuestion::TYPE_MULTIPLE,
            'max_selections' => 5,
            'klaviyo_property' => 'health_investments',
            'options' => [
                ['value' => 'gym', 'label' => 'Gym membership / personal trainer', 'klaviyo_value' => 'Gym', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 0, 'tags' => []],
                ['value' => 'supplements', 'label' => 'Premium supplements ($100+/mo)', 'klaviyo_value' => 'Supplements', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 2, 'tags' => ['high_intent']],
                ['value' => 'functional_med', 'label' => 'Functional medicine / naturopath', 'klaviyo_value' => 'Functional Med', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['advanced', 'high_intent']],
                ['value' => 'telehealth', 'label' => 'Telehealth / online clinic', 'klaviyo_value' => 'Telehealth', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['prefers_telehealth', 'high_intent']],
                ['value' => 'nothing_much', 'label' => 'Not much — this is new for me', 'klaviyo_value' => 'Nothing Much', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner', 'price_sensitive']],
            ],
        ]);

        // 36. Intermission — midpoint encouragement
        $this->slide($quiz, 'midpoint_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'You\'re halfway there!',
            'content_title' => 'Your Profile Is Taking Shape',
            'content_body' => "We\'ve gathered key insights about your health goals, lifestyle, and concerns.\n\nThe next few questions will help us understand your readiness and match you with the perfect peptide and vendor.\n\nYour personalized recommendation is just minutes away.",
        ]);

        // 37. Specific Peptide Interest
        $this->slide($quiz, 'peptide_interest', [
            'question_text' => 'Have you heard of any of these peptides?',
            'question_subtext' => 'Select any you recognize.',
            'question_type' => QuizQuestion::TYPE_MULTIPLE,
            'max_selections' => 10,
            'klaviyo_property' => 'peptides_recognized',
            'options' => [
                ['value' => 'bpc-157', 'label' => 'BPC-157', 'klaviyo_value' => 'BPC-157', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['interested_bpc157', 'researching']],
                ['value' => 'tirzepatide', 'label' => 'Tirzepatide / Mounjaro', 'klaviyo_value' => 'Tirzepatide', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['interested_tirzepatide', 'researching']],
                ['value' => 'semaglutide', 'label' => 'Semaglutide / Ozempic', 'klaviyo_value' => 'Semaglutide', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['interested_semaglutide', 'researching']],
                ['value' => 'ghk-cu', 'label' => 'GHK-Cu', 'klaviyo_value' => 'GHK-Cu', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['interested_ghk_cu', 'researching']],
                ['value' => 'none', 'label' => 'None — I\'m new to all of this', 'klaviyo_value' => 'None', 'score_tof' => 4, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner', 'needs_education']],
            ],
        ]);

        // 38. Growth Hormone Interest
        $this->slide($quiz, 'gh_interest', [
            'question_text' => 'Are you interested in growth hormone optimization?',
            'question_subtext' => 'Growth hormone secretagogues like CJC-1295/Ipamorelin are popular peptides.',
            'klaviyo_property' => 'gh_interest',
            'options' => [
                ['value' => 'very', 'label' => 'Very interested — I\'ve researched it', 'klaviyo_value' => 'Very', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['interested_cjc1295_ipamorelin', 'researching']],
                ['value' => 'somewhat', 'label' => 'Somewhat — tell me more', 'klaviyo_value' => 'Somewhat', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['interested_cjc1295_ipamorelin', 'needs_education']],
                ['value' => 'not_sure', 'label' => 'Not sure what that means', 'klaviyo_value' => 'Not Sure', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner', 'needs_education']],
                ['value' => 'no', 'label' => 'Not interested', 'klaviyo_value' => 'No', 'score_tof' => 1, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // 39. Intermission — GH science
        $this->slide($quiz, 'gh_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Growth Hormone 101',
            'content_title' => 'Why Growth Hormone Matters',
            'content_body' => "Growth hormone naturally declines ~14% per decade after age 30.\n\nPeptides like CJC-1295/Ipamorelin stimulate your pituitary gland to produce more GH naturally — without shutting down your own production.\n\nBenefits include improved body composition, better sleep, faster recovery, and increased energy.",
            'content_source' => 'Endocrine Reviews, 2020',
        ]);

        // 40. Skin & Hair Concerns
        $this->slide($quiz, 'skin_hair', [
            'question_text' => 'Do you have any skin or hair concerns?',
            'klaviyo_property' => 'skin_hair_concerns',
            'options' => [
                ['value' => 'wrinkles', 'label' => 'Wrinkles / fine lines', 'klaviyo_value' => 'Wrinkles', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_anti_aging', 'interested_ghk_cu']],
                ['value' => 'hair_loss', 'label' => 'Hair thinning / loss', 'klaviyo_value' => 'Hair Loss', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_anti_aging', 'interested_ghk_cu']],
                ['value' => 'both', 'label' => 'Both skin and hair concerns', 'klaviyo_value' => 'Both', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['interested_anti_aging', 'interested_ghk_cu']],
                ['value' => 'none', 'label' => 'No concerns', 'klaviyo_value' => 'None', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // 41. Sleep Optimization
        $this->slide($quiz, 'sleep_optimization', [
            'question_text' => 'What have you tried for better sleep?',
            'question_subtext' => 'Select all that apply.',
            'question_type' => QuizQuestion::TYPE_MULTIPLE,
            'max_selections' => 4,
            'klaviyo_property' => 'sleep_tried',
            'options' => [
                ['value' => 'melatonin', 'label' => 'Melatonin / magnesium', 'klaviyo_value' => 'Melatonin', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_sleep']],
                ['value' => 'prescription', 'label' => 'Prescription sleep aids', 'klaviyo_value' => 'Prescription', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 2, 'tags' => ['interested_sleep', 'interested_dsip']],
                ['value' => 'nothing', 'label' => 'Nothing yet', 'klaviyo_value' => 'Nothing', 'score_tof' => 2, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['interested_sleep', 'beginner']],
                ['value' => 'sleep_fine', 'label' => 'My sleep is fine', 'klaviyo_value' => 'Fine', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 0, 'tags' => []],
            ],
        ]);

        // 42. Workout Recovery Time
        $this->slide($quiz, 'recovery_time', [
            'question_text' => 'How long does it take you to recover after a hard workout?',
            'klaviyo_property' => 'recovery_time',
            'options' => [
                ['value' => '1_day', 'label' => '1 day or less', 'klaviyo_value' => '1 Day', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 1, 'tags' => []],
                ['value' => '2-3_days', 'label' => '2–3 days', 'klaviyo_value' => '2-3 Days', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['interested_muscle_growth']],
                ['value' => '4_plus', 'label' => '4+ days', 'klaviyo_value' => '4+ Days', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['interested_muscle_growth', 'interested_bpc157']],
                ['value' => 'dont_workout', 'label' => 'I don\'t work out regularly', 'klaviyo_value' => 'No Workout', 'score_tof' => 2, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner']],
            ],
        ]);

        // 43. Intermission — stats
        $this->slide($quiz, 'stats_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Personalized for You',
            'content_title' => 'We\'re Narrowing It Down',
            'content_body' => "Based on your profile, we\'re already filtering through 50+ peptide options to find your best matches.\n\nThe next section focuses on your experience level and purchasing preferences — this is where we get specific about recommendations.",
        ]);

        // 44. Open Text — what would you change about your health?
        $this->slide($quiz, 'change_text', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION_TEXT,
            'question_text' => 'If you could change ONE thing about your health overnight, what would it be?',
            'question_type' => QuizQuestion::TYPE_TEXT,
            'klaviyo_property' => 'one_change',
            'settings' => ['placeholder' => 'e.g., I\'d fix my chronic back pain / lose 30 pounds / sleep through the night...'],
            'is_required' => false,
        ]);

        // 45. Intermission — transition to Phase 3
        $this->slide($quiz, 'transition_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Almost there!',
            'content_title' => 'Let\'s Talk About Your Peptide Journey',
            'content_body' => "Great news — we have a strong picture of your health goals and needs.\n\nNow let\'s understand where you are with peptides specifically. This helps us recommend not just the right peptide, but the right source and price point for you.",
        ]);
    }

    // ─── PHASE 3: EXPERIENCE & INTENT (Slides 46-65) ───────────────────

    private function seedPhase3ExperienceIntent(Quiz $quiz): void
    {
        // 46. Peptide Awareness Level
        $this->slide($quiz, 'awareness_level', [
            'question_text' => 'Where are you on your peptide journey?',
            'question_subtext' => 'Be honest — there\'s no wrong answer.',
            'klaviyo_property' => 'awareness_level',
            'options' => [
                ['value' => 'brand_new', 'label' => 'Brand new — just heard about peptides', 'klaviyo_value' => 'Brand New', 'score_tof' => 5, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner', 'just_curious']],
                ['value' => 'researching', 'label' => 'Been researching but haven\'t tried any', 'klaviyo_value' => 'Researching', 'score_tof' => 0, 'score_mof' => 5, 'score_bof' => 0, 'tags' => ['intermediate', 'researching']],
                ['value' => 'tried_one', 'label' => 'Tried one or two peptides before', 'klaviyo_value' => 'Tried One', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 4, 'tags' => ['intermediate', 'wants_to_upgrade']],
                ['value' => 'experienced', 'label' => 'Experienced — currently using peptides', 'klaviyo_value' => 'Experienced', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5, 'tags' => ['advanced', 'wants_to_add']],
            ],
        ]);

        // 47. Experience Level (if experienced)
        $this->slide($quiz, 'experience_level', [
            'question_text' => 'How would you describe your peptide knowledge?',
            'klaviyo_property' => 'experience_level',
            'options' => [
                ['value' => 'beginner', 'label' => 'Beginner — I need guidance on everything', 'klaviyo_value' => 'Beginner', 'score_tof' => 4, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner', 'barrier_education']],
                ['value' => 'intermediate', 'label' => 'Intermediate — I know the basics', 'klaviyo_value' => 'Intermediate', 'score_tof' => 0, 'score_mof' => 4, 'score_bof' => 0, 'tags' => ['intermediate', 'researching']],
                ['value' => 'advanced', 'label' => 'Advanced — I understand protocols', 'klaviyo_value' => 'Advanced', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 4, 'tags' => ['advanced', 'ready_to_buy']],
            ],
        ]);

        // 48. Purchase Intent
        $this->slide($quiz, 'purchase_intent', [
            'question_text' => 'How likely are you to purchase a peptide in the next 30 days?',
            'klaviyo_property' => 'purchase_intent',
            'options' => [
                ['value' => 'definitely', 'label' => 'Definitely — I\'m ready to buy', 'klaviyo_value' => 'Definitely', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5, 'tags' => ['ready_to_buy', 'high_intent']],
                ['value' => 'probably', 'label' => 'Probably — if I find the right one', 'klaviyo_value' => 'Probably', 'score_tof' => 0, 'score_mof' => 3, 'score_bof' => 2, 'tags' => ['researching']],
                ['value' => 'maybe', 'label' => 'Maybe — still researching', 'klaviyo_value' => 'Maybe', 'score_tof' => 1, 'score_mof' => 3, 'score_bof' => 0, 'tags' => ['researching']],
                ['value' => 'just_learning', 'label' => 'Just learning for now', 'klaviyo_value' => 'Just Learning', 'score_tof' => 4, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['just_curious', 'needs_education']],
            ],
        ]);

        // 49. Intermission — purchase journey
        $this->slide($quiz, 'purchase_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Smart Shopping',
            'content_title' => 'How to Buy Peptides Safely',
            'content_body' => "Not all peptide sources are created equal.\n\nKey things to look for:\n• Third-party lab testing (COA)\n• Proper storage and shipping\n• Transparent ingredient lists\n• Medical oversight option\n\nWe only recommend vendors that meet these criteria.",
        ]);

        // 50. Biggest Barrier
        $this->slide($quiz, 'biggest_barrier', [
            'question_text' => 'What\'s your biggest barrier to trying peptides?',
            'klaviyo_property' => 'biggest_barrier',
            'options' => [
                ['value' => 'knowledge', 'label' => 'Don\'t know enough about them', 'klaviyo_value' => 'Knowledge', 'score_tof' => 4, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['barrier_education', 'needs_education']],
                ['value' => 'sourcing', 'label' => 'Don\'t know where to buy safely', 'klaviyo_value' => 'Sourcing', 'score_tof' => 0, 'score_mof' => 3, 'score_bof' => 1, 'tags' => ['barrier_sourcing']],
                ['value' => 'safety', 'label' => 'Worried about side effects', 'klaviyo_value' => 'Safety', 'score_tof' => 3, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['barrier_safety']],
                ['value' => 'needles', 'label' => 'Don\'t want to inject', 'klaviyo_value' => 'Needles', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['barrier_needles']],
                ['value' => 'cost', 'label' => 'Cost / affordability', 'klaviyo_value' => 'Cost', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['price_sensitive']],
                ['value' => 'no_barrier', 'label' => 'No barrier — I\'m ready', 'klaviyo_value' => 'No Barrier', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 4, 'tags' => ['ready_to_buy', 'high_intent']],
            ],
        ]);

        // 51. Injection Comfort
        $this->slide($quiz, 'injection_comfort', [
            'question_text' => 'How comfortable are you with self-injection?',
            'klaviyo_property' => 'injection_comfort',
            'options' => [
                ['value' => 'comfortable', 'label' => 'Very comfortable — I do it already', 'klaviyo_value' => 'Comfortable', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['advanced']],
                ['value' => 'willing', 'label' => 'Willing to learn', 'klaviyo_value' => 'Willing', 'score_tof' => 0, 'score_mof' => 3, 'score_bof' => 0, 'tags' => ['researching']],
                ['value' => 'prefer_not', 'label' => 'Prefer oral or nasal peptides', 'klaviyo_value' => 'Prefer Non-Injection', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['barrier_needles']],
                ['value' => 'no_way', 'label' => 'Absolutely not — no needles', 'klaviyo_value' => 'No Way', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['barrier_needles', 'beginner']],
            ],
        ]);

        // 52. Trust Factors
        $this->slide($quiz, 'trust_factors', [
            'question_text' => 'What matters most when choosing a peptide vendor?',
            'question_subtext' => 'Select up to 3.',
            'question_type' => QuizQuestion::TYPE_MULTIPLE,
            'max_selections' => 3,
            'klaviyo_property' => 'trust_factors',
            'options' => [
                ['value' => 'lab_reports', 'label' => 'Third-party lab reports (COA)', 'klaviyo_value' => 'Lab Reports', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['values_lab_reports']],
                ['value' => 'reviews', 'label' => 'Customer reviews & reputation', 'klaviyo_value' => 'Reviews', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['values_reviews']],
                ['value' => 'medical', 'label' => 'Medical oversight / doctor involved', 'klaviyo_value' => 'Medical', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 2, 'tags' => ['prefers_telehealth']],
                ['value' => 'price', 'label' => 'Best price', 'klaviyo_value' => 'Price', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['price_sensitive', 'prefers_affordable']],
            ],
        ]);

        // 53. Hesitations
        $this->slide($quiz, 'hesitations', [
            'question_text' => 'What worries you most about peptides?',
            'klaviyo_property' => 'hesitations',
            'options' => [
                ['value' => 'too_many_choices', 'label' => 'Too many options — overwhelmed', 'klaviyo_value' => 'Overwhelmed', 'score_tof' => 3, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['hesitation_too_many_choices']],
                ['value' => 'vendor_trust', 'label' => 'Don\'t know which vendors to trust', 'klaviyo_value' => 'Vendor Trust', 'score_tof' => 1, 'score_mof' => 3, 'score_bof' => 0, 'tags' => ['hesitation_vendor_trust']],
                ['value' => 'hype', 'label' => 'Worried it\'s all hype', 'klaviyo_value' => 'Hype', 'score_tof' => 3, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['hesitation_hype_vs_real']],
                ['value' => 'side_effects', 'label' => 'Potential side effects', 'klaviyo_value' => 'Side Effects', 'score_tof' => 2, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['barrier_safety']],
                ['value' => 'nothing', 'label' => 'Nothing — I\'m confident', 'klaviyo_value' => 'Nothing', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['ready_to_buy']],
            ],
        ]);

        // 54. Intermission — safety reassurance
        $this->slide($quiz, 'safety_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Your Safety Matters',
            'content_title' => 'Peptide Safety Profile',
            'content_body' => "Most therapeutic peptides have excellent safety profiles when sourced properly and dosed correctly.\n\nBPC-157, for example, has been studied in over 100 clinical trials with minimal reported side effects.\n\nThe key is: quality sourcing + proper dosing + medical guidance when needed.",
            'content_source' => 'Current Pharmaceutical Design, 2018',
        ]);

        // 55. Stacking Interest
        $this->slide($quiz, 'stacking_interest', [
            'question_text' => 'Are you interested in combining multiple peptides (stacking)?',
            'klaviyo_property' => 'stacking_interest',
            'options' => [
                ['value' => 'yes_know_what', 'label' => 'Yes — I know what I want to stack', 'klaviyo_value' => 'Yes Know What', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 4, 'tags' => ['wants_to_stack', 'advanced']],
                ['value' => 'yes_need_help', 'label' => 'Yes — but I need guidance', 'klaviyo_value' => 'Yes Need Help', 'score_tof' => 0, 'score_mof' => 3, 'score_bof' => 1, 'tags' => ['wants_to_stack', 'researching']],
                ['value' => 'one_at_time', 'label' => 'No — one at a time for now', 'klaviyo_value' => 'One at a Time', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['beginner']],
                ['value' => 'not_sure', 'label' => 'What does stacking mean?', 'klaviyo_value' => 'Not Sure', 'score_tof' => 4, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner', 'needs_education']],
            ],
        ]);

        // 56. Which Peptide for You (if aware)
        $this->slide($quiz, 'peptide_preference', [
            'question_text' => 'If you could try any peptide right now, which would it be?',
            'klaviyo_property' => 'peptide_preference',
            'options' => [
                ['value' => 'bpc-157', 'label' => 'BPC-157 (healing & gut)', 'klaviyo_value' => 'BPC-157', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 3, 'tags' => ['interested_bpc157', 'ready_to_buy']],
                ['value' => 'tirzepatide', 'label' => 'Tirzepatide (weight loss)', 'klaviyo_value' => 'Tirzepatide', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 3, 'tags' => ['interested_tirzepatide', 'ready_to_buy']],
                ['value' => 'cjc-ipa', 'label' => 'CJC-1295/Ipamorelin (growth hormone)', 'klaviyo_value' => 'CJC/Ipa', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 3, 'tags' => ['interested_cjc1295_ipamorelin', 'ready_to_buy']],
                ['value' => 'not_sure', 'label' => 'Not sure — recommend for me', 'klaviyo_value' => 'Not Sure', 'score_tof' => 2, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['needs_education']],
                ['value' => 'other', 'label' => 'Something else', 'klaviyo_value' => 'Other', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['researching']],
            ],
        ]);

        // 57. Open text — why that peptide?
        $this->slide($quiz, 'why_peptide_text', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION_TEXT,
            'question_text' => 'Why did you choose that peptide (or what peptide interests you)?',
            'question_type' => QuizQuestion::TYPE_TEXT,
            'klaviyo_property' => 'peptide_reason',
            'settings' => ['placeholder' => 'e.g., I heard BPC-157 helps with gut healing...'],
            'is_required' => false,
        ]);

        // 58. How Did You Hear About Us?
        $this->slide($quiz, 'referral_source', [
            'question_text' => 'How did you find this quiz?',
            'klaviyo_property' => 'referral_source',
            'options' => [
                ['value' => 'google', 'label' => 'Google search', 'klaviyo_value' => 'Google', 'score_tof' => 2, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['researching']],
                ['value' => 'social', 'label' => 'Social media (Instagram, TikTok, X)', 'klaviyo_value' => 'Social', 'score_tof' => 3, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['just_curious']],
                ['value' => 'youtube', 'label' => 'YouTube / Podcast', 'klaviyo_value' => 'YouTube', 'score_tof' => 1, 'score_mof' => 3, 'score_bof' => 0, 'tags' => ['researching']],
                ['value' => 'friend', 'label' => 'Friend or family', 'klaviyo_value' => 'Friend', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 1, 'tags' => []],
                ['value' => 'direct', 'label' => 'Went straight to the site', 'klaviyo_value' => 'Direct', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 3, 'tags' => ['high_intent']],
            ],
        ]);

        // 59. Buying Confidence
        $this->slide($quiz, 'buying_confidence', [
            'question_text' => 'How confident are you in making a peptide purchase decision?',
            'klaviyo_property' => 'buying_confidence',
            'options' => [
                ['value' => 'very', 'label' => 'Very — I know what I need', 'klaviyo_value' => 'Very', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5, 'tags' => ['ready_to_buy', 'high_intent']],
                ['value' => 'somewhat', 'label' => 'Somewhat — need a bit more info', 'klaviyo_value' => 'Somewhat', 'score_tof' => 0, 'score_mof' => 3, 'score_bof' => 1, 'tags' => ['researching']],
                ['value' => 'low', 'label' => 'Low — need lots of guidance', 'klaviyo_value' => 'Low', 'score_tof' => 3, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['needs_education', 'beginner']],
                ['value' => 'zero', 'label' => 'No confidence yet — just exploring', 'klaviyo_value' => 'Zero', 'score_tof' => 4, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['just_curious']],
            ],
        ]);

        // 60. Intermission — trust building
        $this->slide($quiz, 'trust_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'We\'ve Got You',
            'content_title' => 'Your Recommendation Is Almost Ready',
            'content_body' => "Whether you\'re a complete beginner or an experienced user, our algorithm matches you with:\n\n• The right peptide for your specific goals\n• The best vendor based on your preferences\n• A protocol that fits your comfort level\n\nJust a few more questions to finalize your match.",
        ]);

        // 61. Buying Priority
        $this->slide($quiz, 'buying_priority', [
            'question_text' => 'When buying health products, what\'s your top priority?',
            'klaviyo_property' => 'buying_priority',
            'options' => [
                ['value' => 'quality', 'label' => 'Quality — I want the best regardless of price', 'klaviyo_value' => 'Quality', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['high_intent', 'prefers_research_grade']],
                ['value' => 'value', 'label' => 'Value — good quality at a fair price', 'klaviyo_value' => 'Value', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['prefers_affordable']],
                ['value' => 'convenience', 'label' => 'Convenience — make it easy', 'klaviyo_value' => 'Convenience', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 2, 'tags' => ['prefers_telehealth']],
                ['value' => 'cheapest', 'label' => 'Cheapest option available', 'klaviyo_value' => 'Cheapest', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['price_sensitive', 'prefers_affordable']],
            ],
        ]);

        // 62. Email Capture #1
        $this->slide($quiz, 'email_capture_1', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Enter your email to see your personalized peptide recommendation',
            'question_subtext' => 'We\'ll send your results and a custom peptide guide to your inbox.',
            'question_type' => QuizQuestion::TYPE_EMAIL,
            'klaviyo_property' => 'email',
            'content_title' => 'Get Your Results',
            'content_body' => 'Your personalized peptide match is ready. Enter your email to unlock your recommendation.',
        ]);

        // 63. Loading Screen #1
        $this->slide($quiz, 'loading_1', [
            'slide_type' => QuizQuestion::SLIDE_LOADING,
            'question_text' => 'Analyzing your profile...',
            'content_title' => 'Matching Your Profile',
            'content_body' => "Analyzing your health goals...\nEvaluating symptom patterns...\nMatching peptide compatibility...\nIdentifying best vendors...",
            'auto_advance_seconds' => 6,
        ]);

        // 64. Open text — anything else?
        $this->slide($quiz, 'anything_else_text', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION_TEXT,
            'question_text' => 'Anything else you want us to know?',
            'question_subtext' => 'Optional — any details help us personalize further.',
            'question_type' => QuizQuestion::TYPE_TEXT,
            'klaviyo_property' => 'additional_notes',
            'settings' => ['placeholder' => 'e.g., I\'m also interested in peptides for my spouse...'],
            'is_required' => false,
        ]);

        // 65. Intermission — transition to preferences
        $this->slide($quiz, 'prefs_transition', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Final Stretch!',
            'content_title' => 'Let\'s Dial In Your Preferences',
            'content_body' => "We know what you need — now let\'s figure out how you want to get it.\n\nBudget, delivery method, vendor type — these final questions ensure we recommend the perfect match.",
        ]);
    }

    // ─── PHASE 4: PREFERENCES & FIT (Slides 66-85) ─────────────────────

    private function seedPhase4PreferencesFit(Quiz $quiz): void
    {
        // 66. Monthly Budget
        $this->slide($quiz, 'budget', [
            'question_text' => 'What\'s your monthly budget for peptide therapy?',
            'klaviyo_property' => 'monthly_budget',
            'options' => [
                ['value' => 'under_100', 'label' => 'Under $100/month', 'klaviyo_value' => 'Under $100', 'score_tof' => 3, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['price_sensitive', 'prefers_affordable']],
                ['value' => '100-250', 'label' => '$100–$250/month', 'klaviyo_value' => '$100-$250', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['prefers_affordable']],
                ['value' => '250-500', 'label' => '$250–$500/month', 'klaviyo_value' => '$250-$500', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['high_intent']],
                ['value' => '500_plus', 'label' => '$500+/month — willing to invest', 'klaviyo_value' => '$500+', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 4, 'tags' => ['high_intent', 'prefers_research_grade']],
            ],
        ]);

        // 67. Administration Preference
        $this->slide($quiz, 'admin_pref', [
            'question_text' => 'What\'s your preferred way to take peptides?',
            'klaviyo_property' => 'administration_preference',
            'options' => [
                ['value' => 'injection', 'label' => 'Subcutaneous injection (most effective)', 'klaviyo_value' => 'Injection', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 3, 'tags' => ['advanced']],
                ['value' => 'nasal', 'label' => 'Nasal spray', 'klaviyo_value' => 'Nasal', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['barrier_needles']],
                ['value' => 'oral', 'label' => 'Oral / sublingual', 'klaviyo_value' => 'Oral', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['barrier_needles', 'beginner']],
                ['value' => 'topical', 'label' => 'Topical cream / patch', 'klaviyo_value' => 'Topical', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['barrier_needles']],
                ['value' => 'any', 'label' => 'Whatever works best', 'klaviyo_value' => 'Any', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['researching']],
            ],
        ]);

        // 68. Vendor Type Preference
        $this->slide($quiz, 'vendor_type', [
            'question_text' => 'What type of vendor do you prefer?',
            'klaviyo_property' => 'vendor_type_preference',
            'options' => [
                ['value' => 'telehealth', 'label' => 'Telehealth clinic (doctor-prescribed)', 'klaviyo_value' => 'Telehealth', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 3, 'tags' => ['prefers_telehealth']],
                ['value' => 'research', 'label' => 'Research chemical company (affordable)', 'klaviyo_value' => 'Research', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['prefers_research_grade']],
                ['value' => 'compounding', 'label' => 'Compounding pharmacy', 'klaviyo_value' => 'Compounding', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['prefers_telehealth', 'high_intent']],
                ['value' => 'not_sure', 'label' => 'Not sure — recommend for me', 'klaviyo_value' => 'Not Sure', 'score_tof' => 3, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['needs_education']],
            ],
        ]);

        // 69. Intermission — vendor types
        $this->slide($quiz, 'vendor_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Vendor Guide',
            'content_title' => 'Understanding Your Options',
            'content_body' => "Telehealth Clinics: Doctor consultation, prescription-grade peptides, higher cost but full medical oversight.\n\nResearch Companies: More affordable, third-party tested, requires self-dosing knowledge.\n\nCompounding Pharmacies: Custom formulations, prescription required, pharmacy-grade quality.\n\nWe\'ll match you with the right type based on your experience and preferences.",
        ]);

        // 70. Shipping Preference
        $this->slide($quiz, 'shipping_pref', [
            'question_text' => 'How important is fast shipping?',
            'klaviyo_property' => 'shipping_preference',
            'options' => [
                ['value' => 'asap', 'label' => 'Need it ASAP — willing to pay for express', 'klaviyo_value' => 'ASAP', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['high_intent', 'ready_to_buy']],
                ['value' => 'standard', 'label' => 'Standard shipping is fine', 'klaviyo_value' => 'Standard', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['researching']],
                ['value' => 'not_in_rush', 'label' => 'Not in a rush', 'klaviyo_value' => 'Not In Rush', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['just_curious']],
            ],
        ]);

        // 71. Protocol Guidance
        $this->slide($quiz, 'protocol_guidance', [
            'question_text' => 'How much guidance do you need with dosing and protocols?',
            'klaviyo_property' => 'protocol_guidance',
            'options' => [
                ['value' => 'full', 'label' => 'Full guidance — doctor-supervised', 'klaviyo_value' => 'Full', 'score_tof' => 1, 'score_mof' => 0, 'score_bof' => 2, 'tags' => ['prefers_telehealth', 'beginner']],
                ['value' => 'some', 'label' => 'Some guidance — I can follow instructions', 'klaviyo_value' => 'Some', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['intermediate']],
                ['value' => 'none', 'label' => 'I know my protocols — just need the product', 'klaviyo_value' => 'None', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 4, 'tags' => ['advanced', 'ready_to_buy']],
            ],
        ]);

        // 72. Loading Screen #2
        $this->slide($quiz, 'loading_2', [
            'slide_type' => QuizQuestion::SLIDE_LOADING,
            'question_text' => 'Finding your best vendors...',
            'content_title' => 'Matching Vendors',
            'content_body' => "Checking vendor quality scores...\nVerifying lab testing status...\nComparing pricing tiers...\nMatching delivery methods...",
            'auto_advance_seconds' => 5,
        ]);

        // 73. Community Value
        $this->slide($quiz, 'community_value', [
            'question_text' => 'Would you value a peptide community or support group?',
            'klaviyo_property' => 'community_interest',
            'options' => [
                ['value' => 'very', 'label' => 'Yes — I\'d love to connect with others', 'klaviyo_value' => 'Very', 'score_tof' => 2, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['values_reviews']],
                ['value' => 'somewhat', 'label' => 'Maybe — for tips and protocols', 'klaviyo_value' => 'Somewhat', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['researching']],
                ['value' => 'no', 'label' => 'No — I prefer to research alone', 'klaviyo_value' => 'No', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 1, 'tags' => []],
            ],
        ]);

        // 74. Content Preference
        $this->slide($quiz, 'content_pref', [
            'question_text' => 'What type of content would help you most?',
            'klaviyo_property' => 'content_preference',
            'options' => [
                ['value' => 'dosing', 'label' => 'Dosing & protocol guides', 'klaviyo_value' => 'Dosing', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['researching']],
                ['value' => 'research', 'label' => 'Research & clinical studies', 'klaviyo_value' => 'Research', 'score_tof' => 0, 'score_mof' => 3, 'score_bof' => 0, 'tags' => ['researching']],
                ['value' => 'testimonials', 'label' => 'User testimonials & before/after', 'klaviyo_value' => 'Testimonials', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['needs_education']],
                ['value' => 'comparison', 'label' => 'Peptide comparisons & guides', 'klaviyo_value' => 'Comparison', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['researching']],
            ],
        ]);

        // 75. Email Capture #2
        $this->slide($quiz, 'email_capture_2', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Want your full peptide report emailed to you?',
            'question_subtext' => 'We\'ll include your recommended peptides, dosing guides, and vendor comparisons.',
            'question_type' => QuizQuestion::TYPE_EMAIL,
            'klaviyo_property' => 'email',
            'content_title' => 'Get Your Full Report',
            'content_body' => 'Your personalized peptide report will include specific recommendations, dosing protocols, and trusted vendor options.',
        ]);

        // 76. Use Case Specificity
        $this->slide($quiz, 'use_case', [
            'question_text' => 'What best describes your situation?',
            'klaviyo_property' => 'use_case',
            'options' => [
                ['value' => 'personal', 'label' => 'Personal use — for myself', 'klaviyo_value' => 'Personal', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 1, 'tags' => []],
                ['value' => 'couple', 'label' => 'For me and my partner', 'klaviyo_value' => 'Couple', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 2, 'tags' => ['high_intent']],
                ['value' => 'family', 'label' => 'Researching for a family member', 'klaviyo_value' => 'Family', 'score_tof' => 2, 'score_mof' => 2, 'score_bof' => 0, 'tags' => ['researching']],
                ['value' => 'professional', 'label' => 'Healthcare professional researching', 'klaviyo_value' => 'Professional', 'score_tof' => 0, 'score_mof' => 3, 'score_bof' => 1, 'tags' => ['advanced']],
            ],
        ]);

        // 77. Commitment Level
        $this->slide($quiz, 'commitment', [
            'question_text' => 'How long are you willing to commit to a peptide protocol?',
            'klaviyo_property' => 'commitment_length',
            'options' => [
                ['value' => '1_month', 'label' => '1 month trial', 'klaviyo_value' => '1 Month', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['just_curious']],
                ['value' => '3_months', 'label' => '3 months (recommended)', 'klaviyo_value' => '3 Months', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['researching']],
                ['value' => '6_months', 'label' => '6+ months', 'klaviyo_value' => '6+ Months', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 4, 'tags' => ['high_intent', 'ready_to_buy']],
                ['value' => 'ongoing', 'label' => 'Ongoing — this is a lifestyle', 'klaviyo_value' => 'Ongoing', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5, 'tags' => ['advanced', 'wants_to_stack']],
            ],
        ]);

        // 78. Intermission — commitment info
        $this->slide($quiz, 'commitment_intermission', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Good to Know',
            'content_title' => 'Peptide Protocols & Timelines',
            'content_body' => "Most peptides need at least 4-8 weeks to show noticeable results.\n\nBPC-157: Results in 2-4 weeks for healing.\nTirzepatide: Weight loss starts in 4-8 weeks.\nEpithalon: Telomere changes measurable at 3 months.\n\nConsistency is key — the best results come from committed protocols.",
        ]);

        // 79. Open Text — ideal outcome
        $this->slide($quiz, 'ideal_outcome_text', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION_TEXT,
            'question_text' => 'Paint a picture: what does your ideal health outcome look like?',
            'question_type' => QuizQuestion::TYPE_TEXT,
            'klaviyo_property' => 'ideal_outcome',
            'settings' => ['placeholder' => 'e.g., In 6 months, I want to be 20 lbs lighter, pain-free, and sleeping 8 hours...'],
            'is_required' => false,
        ]);

        // 80. Lab Testing Importance
        $this->slide($quiz, 'lab_testing', [
            'question_text' => 'How important are third-party lab reports (COA) to you?',
            'klaviyo_property' => 'lab_testing_importance',
            'options' => [
                ['value' => 'essential', 'label' => 'Essential — won\'t buy without them', 'klaviyo_value' => 'Essential', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 2, 'tags' => ['values_lab_reports']],
                ['value' => 'important', 'label' => 'Important — nice to have', 'klaviyo_value' => 'Important', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['values_lab_reports']],
                ['value' => 'not_sure', 'label' => 'Not sure what those are', 'klaviyo_value' => 'Not Sure', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['beginner', 'needs_education']],
            ],
        ]);

        // 81. Loading Screen #3
        $this->slide($quiz, 'loading_3', [
            'slide_type' => QuizQuestion::SLIDE_LOADING,
            'question_text' => 'Building your recommendation...',
            'content_title' => 'Almost Done',
            'content_body' => "Scoring your health profile...\nRanking peptide matches...\nFiltering vendor options...\nGenerating your personalized plan...",
            'auto_advance_seconds' => 7,
        ]);

        // 82. Subscription vs One-Time
        $this->slide($quiz, 'purchase_model', [
            'question_text' => 'How would you prefer to purchase?',
            'klaviyo_property' => 'purchase_model',
            'options' => [
                ['value' => 'subscription', 'label' => 'Monthly subscription (save 10-20%)', 'klaviyo_value' => 'Subscription', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 3, 'tags' => ['ready_to_buy', 'high_intent']],
                ['value' => 'one_time', 'label' => 'One-time purchase first', 'klaviyo_value' => 'One Time', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['researching']],
                ['value' => 'trial', 'label' => 'Sample or trial size', 'klaviyo_value' => 'Trial', 'score_tof' => 2, 'score_mof' => 1, 'score_bof' => 0, 'tags' => ['just_curious']],
                ['value' => 'not_buying', 'label' => 'Not ready to buy yet', 'klaviyo_value' => 'Not Buying', 'score_tof' => 3, 'score_mof' => 0, 'score_bof' => 0, 'tags' => ['just_curious', 'needs_education']],
            ],
        ]);

        // 83. Feedback Preference
        $this->slide($quiz, 'feedback_pref', [
            'question_text' => 'What type of follow-up would you appreciate?',
            'question_subtext' => 'Select all that apply.',
            'question_type' => QuizQuestion::TYPE_MULTIPLE,
            'max_selections' => 3,
            'klaviyo_property' => 'feedback_preference',
            'options' => [
                ['value' => 'email_guide', 'label' => 'Email guide with my results', 'klaviyo_value' => 'Email Guide', 'score_tof' => 1, 'score_mof' => 1, 'score_bof' => 0, 'tags' => []],
                ['value' => 'dosing_protocol', 'label' => 'Dosing protocol PDF', 'klaviyo_value' => 'Dosing Protocol', 'score_tof' => 0, 'score_mof' => 2, 'score_bof' => 1, 'tags' => ['researching']],
                ['value' => 'vendor_deals', 'label' => 'Vendor deals & discounts', 'klaviyo_value' => 'Vendor Deals', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 2, 'tags' => ['ready_to_buy', 'high_intent']],
            ],
        ]);

        // 84. Open Text — questions for us
        $this->slide($quiz, 'questions_text', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION_TEXT,
            'question_text' => 'Do you have any specific questions about peptides?',
            'question_subtext' => 'We\'ll address these in your personalized report.',
            'question_type' => QuizQuestion::TYPE_TEXT,
            'klaviyo_property' => 'user_questions',
            'settings' => ['placeholder' => 'e.g., Can I take BPC-157 with my current medications?'],
            'is_required' => false,
        ]);

        // 85. Intermission — final transition
        $this->slide($quiz, 'reveal_transition', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Your results are ready!',
            'content_title' => 'Here Comes Your Peptide Match',
            'content_body' => "We\'ve analyzed your responses across 80+ data points.\n\nYour personalized peptide recommendation, matched vendors, and custom protocol are ready.\n\nLet\'s reveal your results!",
        ]);
    }

    // ─── PHASE 5: CAPTURE & REVEAL (Slides 86-100) ─────────────────────

    private function seedPhase5CaptureReveal(Quiz $quiz): void
    {
        // 86. Email Capture #3 — final
        $this->slide($quiz, 'email_capture_3', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Last step — confirm your email to unlock your peptide match',
            'question_subtext' => 'Your full results, dosing guide, and vendor recommendations will be sent here.',
            'question_type' => QuizQuestion::TYPE_EMAIL,
            'klaviyo_property' => 'email',
            'content_title' => 'Unlock Your Results',
            'content_body' => 'Enter your email to receive your complete peptide recommendation report.',
        ]);

        // 87. Loading Screen #4 — final analysis
        $this->slide($quiz, 'loading_4', [
            'slide_type' => QuizQuestion::SLIDE_LOADING,
            'question_text' => 'Generating your personalized results...',
            'content_title' => 'Final Analysis',
            'content_body' => "Cross-referencing 100+ peptide studies...\nMatching your symptom profile...\nRanking vendor compatibility...\nPreparing your recommendation report...",
            'auto_advance_seconds' => 8,
        ]);

        // 88. Peptide Reveal #1 — Primary recommendation
        $this->slide($quiz, 'peptide_reveal_1', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Your #1 Peptide Match',
            'content_title' => 'Your Top Peptide Recommendation',
            'content_body' => "Based on your health goals, symptoms, and preferences, we\'ve identified your ideal peptide match.\n\nThis recommendation is personalized from your unique response profile across 85+ questions.",
            'dynamic_content_key' => 'health_goal',
            'dynamic_content_map' => [
                'fat_loss' => ['title' => 'Tirzepatide — Your Fat Loss Ally', 'body' => "Clinical trials show average 22.5% body weight loss over 72 weeks.\n\nTirzepatide activates both GIP and GLP-1 receptors for dual metabolic action."],
                'muscle_growth' => ['title' => 'CJC-1295/Ipamorelin — Growth Optimized', 'body' => "Stimulates natural growth hormone production for lean muscle gains.\n\nExpect improved recovery, better sleep, and increased training capacity."],
                'anti_aging' => ['title' => 'Epithalon — The Longevity Peptide', 'body' => "The only known telomerase activator — directly addresses cellular aging.\n\nStudies show measurable telomere extension after 3 months of use."],
                'cognitive' => ['title' => 'Semax — Sharpen Your Mind', 'body' => "Increases BDNF (brain-derived neurotrophic factor) for improved cognitive performance.\n\nUsers report enhanced focus, memory, and mental clarity within days."],
                'injury_recovery' => ['title' => 'BPC-157 — Accelerated Healing', 'body' => "The gold standard healing peptide with 100+ clinical studies.\n\nAccelerates healing of muscles, tendons, ligaments, and gut tissue."],
                'sleep' => ['title' => 'DSIP — Deep Sleep Restored', 'body' => "Delta Sleep-Inducing Peptide promotes restorative deep sleep.\n\n83% of users report significantly improved sleep quality within 2 weeks."],
                'general_wellness' => ['title' => 'BPC-157 — The Foundation Peptide', 'body' => "BPC-157 is the most versatile peptide — supporting healing, gut health, and overall wellness.\n\nA great starting point for anyone new to peptides."],
            ],
        ]);

        // 89. Peptide Reveal #2 — Secondary
        $this->slide($quiz, 'peptide_reveal_2', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Your #2 Peptide Match',
            'content_title' => 'Your Secondary Recommendation',
            'content_body' => "Many of our quiz takers benefit from combining two peptides for synergistic results.\n\nHere\'s your second-best match based on your complete profile.",
            'dynamic_content_key' => 'secondary_goal',
            'dynamic_content_map' => [
                'fat_loss' => ['title' => 'Semaglutide — Weight Management Support', 'body' => "GLP-1 receptor agonist shown to reduce appetite and support sustainable weight loss.\n\nA strong complement to your primary recommendation."],
                'muscle_growth' => ['title' => 'TB-500 — Recovery Amplifier', 'body' => "Upregulates cell-building proteins for accelerated muscle and tissue repair.\n\nOften stacked with BPC-157 for comprehensive healing."],
                'anti_aging' => ['title' => 'GHK-Cu — Youth at the Cellular Level', 'body' => "Resets 4,000+ genes to more youthful patterns of expression.\n\nPowerful skin, hair, and tissue rejuvenation properties."],
                'immune' => ['title' => 'Thymosin Alpha 1 — Immune Fortifier', 'body' => "Clinically proven to enhance T-cell function.\n\nApproved in 30+ countries for immune support and recovery."],
                'sexual_health' => ['title' => 'PT-141 — Vitality Restored', 'body' => "First FDA-approved peptide for sexual desire.\n\nWorks through the central nervous system for natural arousal enhancement."],
                'gut_health' => ['title' => 'BPC-157 — Gut Healing Specialist', 'body' => "Derived from gastric protective protein with remarkable gut-healing properties.\n\n78% of users report significant improvement in digestive symptoms."],
            ],
        ]);

        // 90. Loading Screen #5
        $this->slide($quiz, 'loading_5', [
            'slide_type' => QuizQuestion::SLIDE_LOADING,
            'question_text' => 'Finding your best vendor matches...',
            'content_title' => 'Vendor Matching',
            'content_body' => "Verifying product availability...\nChecking current pricing...\nValidating lab reports...\nPreparing vendor comparison...",
            'auto_advance_seconds' => 5,
        ]);

        // 91. Vendor Reveal #1
        $this->slide($quiz, 'vendor_reveal_1', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Your #1 Vendor Match',
            'content_title' => 'Top Vendor Recommendation',
            'content_body' => "Based on your budget, preferences, and comfort level, this vendor is your best match.\n\nAll recommended vendors have been vetted for quality, lab testing, and customer service.",
        ]);

        // 92. Vendor Reveal #2
        $this->slide($quiz, 'vendor_reveal_2', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Alternative Vendor Option',
            'content_title' => 'Budget-Friendly Alternative',
            'content_body' => "This vendor offers a great balance of quality and affordability.\n\nThird-party tested, fast shipping, and strong customer reviews.",
        ]);

        // 93. Vendor Reveal #3
        $this->slide($quiz, 'vendor_reveal_3', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Premium Vendor Option',
            'content_title' => 'Premium Option',
            'content_body' => "For those who want doctor oversight and prescription-grade peptides.\n\nIncludes medical consultation, custom dosing, and ongoing monitoring.",
        ]);

        // 94. Peptide Reveal #3 — Stack suggestion
        $this->slide($quiz, 'peptide_reveal_3', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Bonus: Your Ideal Stack',
            'content_title' => 'Your Peptide Stack Suggestion',
            'content_body' => "For maximum results, consider combining your top recommendations into a stack.\n\nStackable peptides work synergistically — amplifying each other\'s benefits.\n\nYour recommended stack is based on compatibility and your specific health goals.",
        ]);

        // 95. Peptide Reveal #4 — protocol overview
        $this->slide($quiz, 'peptide_reveal_4', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Your Protocol Overview',
            'content_title' => 'Suggested Protocol',
            'content_body' => "Here\'s a high-level protocol overview for your recommended peptides.\n\nTimeline: 8-12 weeks minimum\nFrequency: Daily or 5 days on / 2 days off\nTracking: Monthly check-ins recommended\n\nDetailed dosing instructions will be in your email report.",
        ]);

        // 96. Vendor Reveal #4
        $this->slide($quiz, 'vendor_reveal_4', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Research-Grade Option',
            'content_title' => 'Research-Grade Vendor',
            'content_body' => "For experienced users who know their protocols.\n\nHighest purity at the best prices, with COAs for every batch.",
        ]);

        // 97. Vendor Reveal #5
        $this->slide($quiz, 'vendor_reveal_5', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'All-In-One Platform',
            'content_title' => 'Complete Peptide Platform',
            'content_body' => "Everything in one place: consultation, prescription, peptides, and monitoring.\n\nIdeal for beginners who want a guided experience from start to finish.",
        ]);

        // 98. Peptide Reveal #5 — summary
        $this->slide($quiz, 'peptide_reveal_5', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Your Complete Recommendation Summary',
            'content_title' => 'Your Peptide Journey Starts Here',
            'content_body' => "Your personalized peptide recommendation is complete.\n\nWe\'ve matched you with the right peptides AND the right vendors based on your unique profile.\n\nCheck your email for your detailed report, or take action now with the links below.",
        ]);

        // 99. Bridge CTA #1 — primary action
        $this->slide($quiz, 'bridge_1', [
            'slide_type' => QuizQuestion::SLIDE_BRIDGE,
            'question_text' => 'Ready to Start?',
            'content_title' => 'Get Your Peptides Now',
            'content_body' => "Your top vendor match is ready for you.\n\nClick below to visit their site with your personalized recommendation pre-loaded.",
            'cta_text' => 'Shop My #1 Match',
            'cta_url' => '/vendors/recommended',
        ]);

        // 100. Bridge CTA #2 — secondary action
        $this->slide($quiz, 'bridge_2', [
            'slide_type' => QuizQuestion::SLIDE_BRIDGE,
            'question_text' => 'Want to Learn More First?',
            'content_title' => 'Continue Your Research',
            'content_body' => "Not ready to buy yet? No problem.\n\nWe\'ve sent your full report to your email, including:\n• Your peptide recommendations with dosing protocols\n• Vendor comparison chart\n• Beginner\'s guide to peptide therapy\n\nTake your time — your recommendation will be waiting.",
            'cta_text' => 'Read My Full Report',
            'cta_url' => '/peptide-guide',
        ]);
    }

    // ─── OUTCOMES ────────────────────────────────────────────────────────

    private function seedOutcomes(Quiz $quiz): void
    {
        // Outcome 1: Ready to Buy — Knows Product (answer-based)
        QuizOutcome::create([
            'quiz_id' => $quiz->id,
            'name' => 'Ready to Buy — Knows Product',
            'result_title' => 'You Know Exactly What You Need',
            'result_message' => 'Based on your answers, you have a clear peptide preference and are ready to take action.',
            'redirect_url' => '/vendors/recommended',
            'priority' => 1,
            'is_active' => true,
            'conditions' => [
                'type' => 'answer',
                'question' => 'purchase_intent',
                'value' => 'definitely',
            ],
        ]);

        // Outcome 2: Ready to Buy — Needs Guidance (segment: bof)
        QuizOutcome::create([
            'quiz_id' => $quiz->id,
            'name' => 'Ready to Buy — Needs Guidance',
            'result_title' => 'You\'re Ready — Let Us Guide You',
            'result_message' => 'You have strong intent and good knowledge. Let us match you with the perfect vendor.',
            'redirect_url' => '/vendors/recommended',
            'priority' => 2,
            'is_active' => true,
            'conditions' => [
                'type' => 'segment',
                'segment' => 'bof',
            ],
        ]);

        // Outcome 3: Active Researcher (segment: mof)
        QuizOutcome::create([
            'quiz_id' => $quiz->id,
            'name' => 'Active Researcher',
            'result_title' => 'You\'re Doing Your Homework',
            'result_message' => 'You\'re smart to research before buying. Here\'s everything you need to make a confident decision.',
            'redirect_url' => '/peptide-guide',
            'priority' => 3,
            'is_active' => true,
            'conditions' => [
                'type' => 'segment',
                'segment' => 'mof',
            ],
        ]);

        // Outcome 4: Curious Explorer (segment: tof)
        QuizOutcome::create([
            'quiz_id' => $quiz->id,
            'name' => 'Curious Explorer',
            'result_title' => 'Welcome to the World of Peptides',
            'result_message' => 'You\'re at the beginning of an exciting journey. Let us help you understand what peptides can do for you.',
            'redirect_url' => '/peptide-101',
            'priority' => 4,
            'is_active' => true,
            'conditions' => [
                'type' => 'segment',
                'segment' => 'tof',
            ],
        ]);

        // Outcome 5: Health Goal Match (answer-based)
        QuizOutcome::create([
            'quiz_id' => $quiz->id,
            'name' => 'Health Goal Match',
            'result_title' => 'Your Health Goal Has a Peptide Solution',
            'result_message' => 'Based on your specific health goals, we\'ve identified peptides that directly address your needs.',
            'redirect_url' => '/results',
            'priority' => 5,
            'is_active' => true,
            'conditions' => [
                'type' => 'answer',
                'question' => 'health_goal',
                'value' => 'injury_recovery',
            ],
        ]);

        // Outcome 6: General Fallback
        QuizOutcome::create([
            'quiz_id' => $quiz->id,
            'name' => 'General Recommendation',
            'result_title' => 'Your Personalized Peptide Report Is Ready',
            'result_message' => 'We\'ve analyzed your responses and prepared a customized recommendation. Check your email for the full report.',
            'redirect_url' => '/peptide-guide',
            'priority' => 10,
            'is_active' => true,
            'conditions' => ['type' => 'fallback'],
        ]);
    }
}
