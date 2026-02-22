<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizOutcome;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizFunnelSeeder extends Seeder
{
    private array $slideIds = [];
    private int $order = 0;

    public function run(): void
    {
        $quiz = Quiz::updateOrCreate(
            ['slug' => 'peptide-quiz-funnel'],
            [
                'name' => 'Peptide Quiz Funnel',
                'title' => 'Find Your Perfect Peptide',
                'type' => Quiz::TYPE_SEGMENTATION,
                'description' => 'Multi-journey quiz funnel with TOF/MOF/BOF paths',
                'settings' => [
                    'show_progress_bar' => true,
                    'allow_back' => true,
                    'require_email' => true,
                ],
                'is_active' => true,
            ]
        );

        // Idempotent: clear existing slides on re-run
        $quiz->questions()->delete();
        $this->order = 0;
        $this->slideIds = [];

        // Phase 1: Create all slides
        $this->seedSharedStart($quiz);
        $this->seedTofJourney($quiz);
        $this->seedMofJourney($quiz);
        $this->seedBofAJourney($quiz);
        $this->seedBofBJourney($quiz);
        $this->seedBofCJourney($quiz);

        // Phase 2: Link BOF sub-path skip_to references
        $this->linkSkipToReferences($quiz);

        // Phase 3: Create outcome records
        $this->seedOutcomes($quiz);

        $this->command->info("Seeded {$this->order} quiz funnel slides across 5 journeys.");
    }

    // ─── SHARED START (2 slides) ───────────────────────────────────────

    private function seedSharedStart(Quiz $quiz): void
    {
        // Slide 1: Segmentation
        $this->slide($quiz, 'seg', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'Where are you on your peptide journey?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'awareness_level',
            'options' => [
                ['value' => 'brand_new', 'label' => "I'm brand new to peptides", 'klaviyo_value' => 'brand_new', 'score_tof' => 10, 'score_mof' => 0, 'score_bof' => 0],
                ['value' => 'researching', 'label' => "I've been researching but haven't tried one yet", 'klaviyo_value' => 'researching', 'score_tof' => 0, 'score_mof' => 10, 'score_bof' => 0],
                ['value' => 'ready_to_buy', 'label' => 'I know what I want — ready to buy', 'klaviyo_value' => 'ready_to_buy', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 10],
            ],
        ]);

        // Slide 2: BOF Sub-Path selector (only for BOF users)
        $this->slide($quiz, 'bof_sub', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'Which best describes you?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'bof_intent',
            'show_conditions' => $this->cond('seg', 'ready_to_buy'),
            'options' => [
                // skip_to_question will be set in linkSkipToReferences()
                ['value' => 'know_what_i_want', 'label' => 'I know exactly which peptide I want', 'klaviyo_value' => 'know_what_i_want', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5],
                ['value' => 'know_my_goal', 'label' => 'I know my health goal — help me pick', 'klaviyo_value' => 'know_my_goal', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5],
                ['value' => 'want_to_stack', 'label' => "I'm on a peptide and want to explore adding another", 'klaviyo_value' => 'want_to_stack', 'score_tof' => 0, 'score_mof' => 0, 'score_bof' => 5],
            ],
        ]);
    }

    // ─── TOF JOURNEY (15 slides, orders 3-17) ─────────────────────────

    private function seedTofJourney(Quiz $quiz): void
    {
        $tofCond = $this->cond('seg', 'brand_new');

        // 3: Education intermission
        $this->slide($quiz, 'tof_edu', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'What Are Peptides?',
            'content_title' => 'What Are Peptides?',
            'content_body' => "Peptides are short chains of amino acids — the same building blocks your body already uses.\n\nUnlike supplements, peptides are bioidentical compounds that work with your body's own signaling systems.\n\nThink of them as precision tools for specific health goals — backed by real clinical research.",
            'show_conditions' => $tofCond,
        ]);

        // 4: 87% stat intermission
        $this->slide($quiz, 'tof_stat87', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => '87% Stat',
            'content_title' => 'You\'re Not Alone',
            'content_body' => "87% of people who start peptide therapy wish they had started sooner.\n\nLet's find out which peptide is right for your goals.",
            'content_source' => 'Based on Professor Peptides community survey, 2025',
            'show_conditions' => $tofCond,
        ]);

        // 5: Health Goal question
        $this->slide($quiz, 'tof_health_goal', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What\'s your #1 health goal right now?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'health_goal',
            'show_conditions' => $tofCond,
            'options' => $this->healthGoalOptions(),
        ]);

        // 6: Dynamic stat per health goal
        $this->slide($quiz, 'tof_dynamic_stat', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Goal-Based Stat',
            'content_title' => 'The Science Is Clear',
            'content_body' => 'Peptide therapy is backed by thousands of clinical studies. Let\'s find the right peptide for your specific goals.',
            'show_conditions' => $tofCond,
            'dynamic_content_key' => 'health_goal',
            'dynamic_content_map' => $this->healthGoalDynamicStats(),
        ]);

        // 7: Barrier question
        $this->slide($quiz, 'tof_barrier', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What\'s been the biggest thing holding you back from trying peptides?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'barrier',
            'show_conditions' => $tofCond,
            'options' => [
                ['value' => 'education', 'label' => 'I don\'t know enough about them yet', 'klaviyo_value' => 'education'],
                ['value' => 'sourcing', 'label' => 'I don\'t know where to get them safely', 'klaviyo_value' => 'sourcing'],
                ['value' => 'safety', 'label' => 'I\'m worried about side effects', 'klaviyo_value' => 'safety'],
                ['value' => 'needles', 'label' => 'I\'m not comfortable with injections', 'klaviyo_value' => 'needles'],
            ],
        ]);

        // 8: Objection killer intermission
        $this->slide($quiz, 'tof_objection', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Objection Killer',
            'content_title' => 'Good News — It\'s Simpler Than You Think',
            'content_body' => "Several peptides are already FDA-approved treatments. Many others are in late-stage clinical trials.\n\nAnd most protocols are simpler than you'd expect — similar to taking a daily vitamin.\n\nWe only recommend peptides from vendors with third-party purity testing (>98% purity guaranteed).",
            'show_conditions' => $tofCond,
        ]);

        // 9: Gender
        $this->slide($quiz, 'tof_gender', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What is your biological sex?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'gender',
            'show_conditions' => $tofCond,
            'options' => $this->genderOptions(),
        ]);

        // 10: Age range
        $this->slide($quiz, 'tof_age', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What\'s your age range?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'age_range',
            'show_conditions' => $tofCond,
            'options' => $this->ageOptions(),
        ]);

        // 11: Buying priority
        $this->slide($quiz, 'tof_buying_priority', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'When it comes to buying peptides, what matters most to you?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'buying_priority',
            'show_conditions' => $tofCond,
            'options' => $this->buyingPriorityOptions(),
        ]);

        // 12: Loading screen
        $this->slide($quiz, 'tof_loading', [
            'slide_type' => QuizQuestion::SLIDE_LOADING,
            'question_text' => 'Analyzing Your Profile',
            'content_title' => 'Building Your Personalized Recommendation',
            'content_body' => "Analyzing your health goal...\nReviewing clinical research...\nMatching peptides to your profile...\nChecking vendor availability...\nFinalizing your recommendation...",
            'auto_advance_seconds' => 4,
            'show_conditions' => $tofCond,
        ]);

        // 13: Email capture
        $this->slide($quiz, 'tof_email', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Get Your Results',
            'klaviyo_property' => 'email',
            'content_title' => 'Your personalized peptide recommendation is ready!',
            'content_body' => 'Enter your email to unlock your results and receive your free Peptide Quick-Start Guide.',
            'show_conditions' => $tofCond,
        ]);

        // 14: Peptide reveal
        $this->slide($quiz, 'tof_peptide_reveal', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Your #1 Peptide Match',
            'content_title' => 'Your #1 Peptide Match',
            'show_conditions' => $tofCond,
        ]);

        // 15: Vendor reveal
        $this->slide($quiz, 'tof_vendor_reveal', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Where To Get It',
            'content_title' => 'Where To Get {{peptide_name}}',
            'show_conditions' => $tofCond,
        ]);

        // 16: Feedback question
        $this->slide($quiz, 'tof_feedback', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What would you like to learn more about?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'content_interest',
            'show_conditions' => $tofCond,
            'options' => $this->feedbackOptions(),
        ]);

        // 17: Bridge CTA
        $this->slide($quiz, 'tof_bridge', [
            'slide_type' => QuizQuestion::SLIDE_BRIDGE,
            'question_text' => 'Your Next Step',
            'content_title' => 'Your personalized guide is on its way to your inbox!',
            'content_body' => "While you wait, compare prices for {{peptide_name}} across our trusted vendors.\n\nWe've already done the research — you just pick the best deal.",
            'cta_text' => 'Compare Prices Now',
            'cta_url' => '/stack-builder',
            'show_conditions' => $tofCond,
        ]);
    }

    // ─── MOF JOURNEY (15 slides, orders 18-32) ────────────────────────

    private function seedMofJourney(Quiz $quiz): void
    {
        $mofCond = $this->cond('seg', 'researching');

        // 18: Validation intermission
        $this->slide($quiz, 'mof_validation', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Validation',
            'content_title' => 'We Get It — There\'s a Ton of Conflicting Info Out There',
            'content_body' => "Reddit says one thing. TikTok says another. Your buddy swears by something else.\n\nLet's cut through the noise and match you with the peptide that actually fits YOUR goals — based on clinical data, not hype.",
            'show_conditions' => $mofCond,
        ]);

        // 19: Health goal
        $this->slide($quiz, 'mof_health_goal', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What\'s the #1 health goal you\'re researching peptides for?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'health_goal',
            'show_conditions' => $mofCond,
            'options' => $this->healthGoalOptions(),
        ]);

        // 20: Dynamic stat per health goal
        $this->slide($quiz, 'mof_dynamic_stat', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Goal-Based Stat',
            'content_title' => 'The Science Is Clear',
            'content_body' => 'Peptide therapy is backed by thousands of clinical studies. Let\'s find the right peptide for your specific goals.',
            'show_conditions' => $mofCond,
            'dynamic_content_key' => 'health_goal',
            'dynamic_content_map' => $this->healthGoalDynamicStats(),
        ]);

        // 21: Experience level
        $this->slide($quiz, 'mof_experience', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'How much experience do you have with peptides?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'experience_level',
            'show_conditions' => $mofCond,
            'options' => [
                ['value' => 'beginner', 'label' => 'Research only — haven\'t tried any yet', 'klaviyo_value' => 'beginner'],
                ['value' => 'beginner', 'label' => 'Tried one peptide before', 'klaviyo_value' => 'beginner'],
                ['value' => 'advanced', 'label' => 'Tried several — looking for the right one', 'klaviyo_value' => 'advanced'],
            ],
        ]);

        // 22: Hesitation
        $this->slide($quiz, 'mof_hesitation', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What\'s your biggest hesitation right now?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'hesitation',
            'show_conditions' => $mofCond,
            'options' => [
                ['value' => 'too_many_choices', 'label' => 'Too many options — don\'t know which to pick', 'klaviyo_value' => 'too_many_choices'],
                ['value' => 'vendor_trust', 'label' => 'Don\'t know which vendors to trust', 'klaviyo_value' => 'vendor_trust'],
                ['value' => 'hype_vs_real', 'label' => 'Hard to tell what\'s hype vs what actually works', 'klaviyo_value' => 'hype_vs_real'],
            ],
        ]);

        // 23: Dynamic objection buster per hesitation
        $this->slide($quiz, 'mof_objection', [
            'slide_type' => QuizQuestion::SLIDE_INTERMISSION,
            'question_text' => 'Objection Buster',
            'content_title' => 'We Understand Your Concerns',
            'content_body' => 'That\'s exactly why we built this quiz — to give you personalized, research-backed guidance.',
            'show_conditions' => $mofCond,
            'dynamic_content_key' => 'hesitation',
            'dynamic_content_map' => [
                'too_many_choices' => [
                    'title' => 'We\'ll Narrow It Down For You',
                    'body' => "There are 60+ peptides out there. You don't need to know them all.\n\nBased on your goal and experience, we'll match you with the ONE peptide that has the strongest clinical evidence for your specific situation.",
                ],
                'vendor_trust' => [
                    'title' => 'We Do the Vetting For You',
                    'body' => "Finding a trustworthy vendor is the #1 challenge in peptide therapy.\n\nOur vendor comparison tool shows you prices, third-party purity testing results, and real user reviews — all in one place.",
                ],
                'hype_vs_real' => [
                    'title' => 'The Research Speaks For Itself',
                    'body' => "Peptides aren't unproven supplements — they're bioactive compounds backed by peer-reviewed clinical trials.\n\nWe match you with the peptide that has the strongest evidence for YOUR specific goal. No hype, just data.",
                ],
                '_default' => [
                    'title' => 'We\'ve Got You Covered',
                    'body' => "Starting peptide therapy is a big decision, and it's smart to be cautious.\n\nThat's exactly why we built this quiz — to give you personalized, research-backed guidance.",
                ],
            ],
        ]);

        // 24: Gender
        $this->slide($quiz, 'mof_gender', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What is your biological sex?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'gender',
            'show_conditions' => $mofCond,
            'options' => $this->genderOptions(),
        ]);

        // 25: Age range
        $this->slide($quiz, 'mof_age', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What\'s your age range?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'age_range',
            'show_conditions' => $mofCond,
            'options' => $this->ageOptions(),
        ]);

        // 26: Buying priority
        $this->slide($quiz, 'mof_buying_priority', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'When it comes to buying peptides, what matters most to you?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'buying_priority',
            'show_conditions' => $mofCond,
            'options' => $this->buyingPriorityOptions(),
        ]);

        // 27: Loading screen
        $this->slide($quiz, 'mof_loading', [
            'slide_type' => QuizQuestion::SLIDE_LOADING,
            'question_text' => 'Refining Your Recommendation',
            'content_title' => 'Refining Your Personalized Match',
            'content_body' => "Cross-referencing your experience level...\nAnalyzing clinical data for your goal...\nFiltering by safety profile...\nComparing vendor options...\nLocking in your recommendation...",
            'auto_advance_seconds' => 4,
            'show_conditions' => $mofCond,
        ]);

        // 28: Email capture
        $this->slide($quiz, 'mof_email', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Get Your Results',
            'klaviyo_property' => 'email',
            'content_title' => 'Your personalized recommendation is ready!',
            'content_body' => 'Enter your email to see your matched peptide, vendor comparison, and receive our Peptide Research Guide.',
            'show_conditions' => $mofCond,
        ]);

        // 29: Peptide reveal
        $this->slide($quiz, 'mof_peptide_reveal', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Your #1 Peptide Match',
            'content_title' => 'Your #1 Peptide Match',
            'show_conditions' => $mofCond,
        ]);

        // 30: Vendor reveal
        $this->slide($quiz, 'mof_vendor_reveal', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Where To Get It',
            'content_title' => 'Where To Get {{peptide_name}}',
            'show_conditions' => $mofCond,
        ]);

        // 31: Feedback
        $this->slide($quiz, 'mof_feedback', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What would you like to learn more about?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'content_interest',
            'show_conditions' => $mofCond,
            'options' => $this->feedbackOptions(),
        ]);

        // 32: Bridge
        $this->slide($quiz, 'mof_bridge', [
            'slide_type' => QuizQuestion::SLIDE_BRIDGE,
            'question_text' => 'Your Next Step',
            'content_title' => 'Your personalized guide is on its way!',
            'content_body' => "Ready to compare prices for {{peptide_name}} across trusted vendors?\n\nWe've vetted every vendor for purity testing, shipping speed, and customer reviews.",
            'cta_text' => 'Compare Prices Now',
            'cta_url' => '/stack-builder',
            'show_conditions' => $mofCond,
        ]);
    }

    // ─── BOF-A: "I Know My Peptide" (6 slides, orders 33-38) ──────────

    private function seedBofAJourney(Quiz $quiz): void
    {
        $bofACond = $this->condAnd('seg', 'ready_to_buy', 'bof_sub', 'know_what_i_want');

        // 33: Peptide selection
        $this->slide($quiz, 'bofa_peptide', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'Which peptide are you looking for?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'selected_peptide',
            'show_conditions' => $bofACond,
            'options' => $this->peptideSelectionOptions(),
        ]);

        // 34: Buying context
        $this->slide($quiz, 'bofa_context', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What\'s your situation with this peptide?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'buying_context',
            'show_conditions' => $bofACond,
            'options' => [
                ['value' => 'first_time', 'label' => 'First time buying this peptide', 'klaviyo_value' => 'first_time'],
                ['value' => 'restocking', 'label' => 'Restocking — I\'ve used it before', 'klaviyo_value' => 'restocking'],
                ['value' => 'switching', 'label' => 'Switching vendors — looking for a better source', 'klaviyo_value' => 'switching'],
            ],
        ]);

        // 35: Buying confidence
        $this->slide($quiz, 'bofa_confidence', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What would make you most confident in a vendor?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'buying_confidence',
            'show_conditions' => $bofACond,
            'options' => $this->buyingConfidenceOptions(),
        ]);

        // 36: Email capture
        $this->slide($quiz, 'bofa_email', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Get Your Vendor Match',
            'klaviyo_property' => 'email',
            'content_title' => 'We found the best deals for your peptide!',
            'content_body' => 'Enter your email to see vendor pricing and receive exclusive deals.',
            'show_conditions' => $bofACond,
        ]);

        // 37: Vendor reveal
        $this->slide($quiz, 'bofa_vendor_reveal', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Best Vendors For Your Peptide',
            'content_title' => 'Best Vendors For Your Peptide',
            'show_conditions' => $bofACond,
        ]);

        // 38: Bridge
        $this->slide($quiz, 'bofa_bridge', [
            'slide_type' => QuizQuestion::SLIDE_BRIDGE,
            'question_text' => 'Your Next Step',
            'content_title' => 'Ready to compare all your options?',
            'content_body' => "Check your inbox for exclusive deals.\n\nOr head straight to our price comparison tool to see all vendors side by side.",
            'cta_text' => 'Compare All Vendors',
            'cta_url' => '/stack-builder',
            'show_conditions' => $bofACond,
        ]);
    }

    // ─── BOF-B: "I Know My Goal" (7 slides, orders 39-45) ─────────────

    private function seedBofBJourney(Quiz $quiz): void
    {
        $bofBCond = $this->condAnd('seg', 'ready_to_buy', 'bof_sub', 'know_my_goal');

        // 39: Health goal
        $this->slide($quiz, 'bofb_health_goal', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What health goal are you ready to tackle?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'health_goal',
            'show_conditions' => $bofBCond,
            'options' => $this->healthGoalOptions(),
        ]);

        // 40: Experience level
        $this->slide($quiz, 'bofb_experience', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'How much peptide experience do you have for this goal?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'experience_level',
            'show_conditions' => $bofBCond,
            'options' => [
                ['value' => 'beginner', 'label' => 'First time trying a peptide for this', 'klaviyo_value' => 'beginner'],
                ['value' => 'advanced', 'label' => 'Switching from another peptide', 'klaviyo_value' => 'advanced'],
                ['value' => 'advanced', 'label' => 'Restocking — I know what works', 'klaviyo_value' => 'advanced'],
            ],
        ]);

        // 41: Buying confidence
        $this->slide($quiz, 'bofb_confidence', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What would make you most confident in a vendor?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'buying_confidence',
            'show_conditions' => $bofBCond,
            'options' => $this->buyingConfidenceOptions(),
        ]);

        // 42: Email capture
        $this->slide($quiz, 'bofb_email', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Get Your Results',
            'klaviyo_property' => 'email',
            'content_title' => 'Your peptide match is ready!',
            'content_body' => 'Enter your email to see your recommendation and vendor comparison.',
            'show_conditions' => $bofBCond,
        ]);

        // 43: Peptide reveal
        $this->slide($quiz, 'bofb_peptide_reveal', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Your #1 Peptide Match',
            'content_title' => 'Your #1 Peptide Match',
            'show_conditions' => $bofBCond,
        ]);

        // 44: Vendor reveal
        $this->slide($quiz, 'bofb_vendor_reveal', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Where To Get It',
            'content_title' => 'Where To Get {{peptide_name}}',
            'show_conditions' => $bofBCond,
        ]);

        // 45: Bridge
        $this->slide($quiz, 'bofb_bridge', [
            'slide_type' => QuizQuestion::SLIDE_BRIDGE,
            'question_text' => 'Your Next Step',
            'content_title' => 'You\'re all set!',
            'content_body' => "Your guide for {{peptide_name}} is heading to your inbox.\n\nCompare prices across trusted vendors right now.",
            'cta_text' => 'Compare Prices Now',
            'cta_url' => '/stack-builder',
            'show_conditions' => $bofBCond,
        ]);
    }

    // ─── BOF-C: "Want to Stack / Explore" (8 slides, orders 46-53) ────

    private function seedBofCJourney(Quiz $quiz): void
    {
        $bofCCond = $this->condAnd('seg', 'ready_to_buy', 'bof_sub', 'want_to_stack');

        // 46: Current peptide (text input)
        $this->slide($quiz, 'bofc_current', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION_TEXT,
            'question_text' => 'What peptide are you currently taking?',
            'question_type' => QuizQuestion::TYPE_TEXT,
            'klaviyo_property' => 'current_peptide',
            'show_conditions' => $bofCCond,
        ]);

        // 47: Stacking intent
        $this->slide($quiz, 'bofc_intent', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What are you looking to do?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'stacking_intent',
            'show_conditions' => $bofCCond,
            'options' => [
                ['value' => 'add_to_stack', 'label' => 'Add another peptide to my current stack', 'klaviyo_value' => 'add_to_stack'],
                ['value' => 'upgrade', 'label' => 'Upgrade to something more effective', 'klaviyo_value' => 'upgrade'],
                ['value' => 'restart', 'label' => 'Start a fresh protocol', 'klaviyo_value' => 'restart'],
                ['value' => 'browsing', 'label' => 'Just exploring what\'s out there', 'klaviyo_value' => 'browsing'],
            ],
        ]);

        // 48: Goal to add (maps to health_goal for ResultsBank lookup)
        $this->slide($quiz, 'bofc_goal', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What health goal do you want to target next?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'health_goal',
            'show_conditions' => $bofCCond,
            'options' => $this->healthGoalOptions(),
        ]);

        // 49: Buying confidence
        $this->slide($quiz, 'bofc_confidence', [
            'slide_type' => QuizQuestion::SLIDE_QUESTION,
            'question_text' => 'What would make you most confident in a vendor?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'klaviyo_property' => 'buying_confidence',
            'show_conditions' => $bofCCond,
            'options' => $this->buyingConfidenceOptions(),
        ]);

        // 50: Email capture
        $this->slide($quiz, 'bofc_email', [
            'slide_type' => QuizQuestion::SLIDE_EMAIL_CAPTURE,
            'question_text' => 'Get Your Stacking Guide',
            'klaviyo_property' => 'email',
            'content_title' => 'Your stacking recommendation is ready!',
            'content_body' => 'Enter your email to see your next peptide match and our Stacking Safety Guide.',
            'show_conditions' => $bofCCond,
        ]);

        // 51: Peptide reveal
        $this->slide($quiz, 'bofc_peptide_reveal', [
            'slide_type' => QuizQuestion::SLIDE_PEPTIDE_REVEAL,
            'question_text' => 'Your Next Peptide',
            'content_title' => 'Add This To Your Stack',
            'show_conditions' => $bofCCond,
        ]);

        // 52: Vendor reveal
        $this->slide($quiz, 'bofc_vendor_reveal', [
            'slide_type' => QuizQuestion::SLIDE_VENDOR_REVEAL,
            'question_text' => 'Where To Get It',
            'content_title' => 'Where To Get {{peptide_name}}',
            'show_conditions' => $bofCCond,
        ]);

        // 53: Bridge
        $this->slide($quiz, 'bofc_bridge', [
            'slide_type' => QuizQuestion::SLIDE_BRIDGE,
            'question_text' => 'Your Next Step',
            'content_title' => 'Your stacking guide is on its way!',
            'content_body' => "We'll send you safety notes for combining {{peptide_name}} with your current protocol.\n\nIn the meantime, compare vendor prices.",
            'cta_text' => 'Compare Prices Now',
            'cta_url' => '/stack-builder',
            'show_conditions' => $bofCCond,
        ]);
    }

    // ─── PHASE 2: Link skip_to references ──────────────────────────────

    private function linkSkipToReferences(Quiz $quiz): void
    {
        $bofSubSlide = QuizQuestion::find($this->slideIds['bof_sub']);
        if (!$bofSubSlide) return;

        $options = $bofSubSlide->options;
        foreach ($options as &$option) {
            $skipTo = match ($option['value']) {
                'know_what_i_want' => $this->slideIds['bofa_peptide'] ?? null,
                'know_my_goal' => $this->slideIds['bofb_health_goal'] ?? null,
                'want_to_stack' => $this->slideIds['bofc_current'] ?? null,
                default => null,
            };
            if ($skipTo) {
                $option['skip_to_question'] = (string) $skipTo;
            }
        }
        unset($option);

        $bofSubSlide->update(['options' => $options]);
    }

    // ─── PHASE 3: Seed Outcomes ────────────────────────────────────────

    private function seedOutcomes(Quiz $quiz): void
    {
        // Clear existing outcomes for idempotency
        $quiz->outcomes()->delete();

        $outcomes = [
            [
                'name' => 'BOF-A: Known Peptide',
                'conditions' => ['type' => 'answer', 'question' => 'bof_intent', 'value' => 'know_what_i_want'],
                'priority' => 1,
                'result_title' => 'Great Choice!',
                'result_message' => "We found the best vendors and pricing for your peptide. Head to the Stack Builder to compare deals.",
                'redirect_url' => '/stack-builder',
            ],
            [
                'name' => 'BOF-B: Known Goal',
                'conditions' => ['type' => 'answer', 'question' => 'bof_intent', 'value' => 'know_my_goal'],
                'priority' => 2,
                'result_title' => 'Your Match is Ready',
                'result_message' => "Based on your health goal and experience level, we've found the ideal peptide. Compare vendor pricing now.",
                'redirect_url' => '/stack-builder',
            ],
            [
                'name' => 'BOF-C: Stacker',
                'conditions' => ['type' => 'answer', 'question' => 'bof_intent', 'value' => 'want_to_stack'],
                'priority' => 3,
                'result_title' => 'Your Stack Upgrade is Ready',
                'result_message' => "We've identified the perfect addition to your current peptide stack. Compare pricing across trusted vendors.",
                'redirect_url' => '/stack-builder',
            ],
            [
                'name' => 'TOF: Brand New',
                'conditions' => ['type' => 'answer', 'question' => 'awareness_level', 'value' => 'brand_new'],
                'priority' => 10,
                'result_title' => 'Your Peptide Journey Starts Here',
                'result_message' => "Based on your answers, we've matched you with the perfect peptide for your goals. Check your email for your personalized guide!",
                'redirect_url' => '/peptides',
            ],
            [
                'name' => 'MOF: Researching',
                'conditions' => ['type' => 'answer', 'question' => 'awareness_level', 'value' => 'researching'],
                'priority' => 20,
                'result_title' => 'Your Research Pays Off',
                'result_message' => "We've narrowed down the best peptide for your specific needs. Your personalized recommendation and vendor comparison are ready.",
                'redirect_url' => '/stack-builder',
            ],
        ];

        foreach ($outcomes as $data) {
            $quiz->outcomes()->create(array_merge($data, ['is_active' => true]));
        }

        $this->command->info('Seeded ' . count($outcomes) . ' quiz outcomes.');
    }

    // ─── HELPERS ───────────────────────────────────────────────────────

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

    private function condAnd(string $label1, string $val1, string $label2, string $val2): array
    {
        return [
            'type' => 'and',
            'conditions' => [
                ['question_id' => $this->slideIds[$label1], 'option_value' => $val1],
                ['question_id' => $this->slideIds[$label2], 'option_value' => $val2],
            ],
        ];
    }

    private function healthGoalOptions(): array
    {
        return [
            ['value' => 'fat_loss', 'label' => 'Lose fat & boost metabolism', 'klaviyo_value' => 'fat_loss'],
            ['value' => 'muscle_growth', 'label' => 'Build muscle & recover faster', 'klaviyo_value' => 'muscle_growth'],
            ['value' => 'anti_aging', 'label' => 'Look & feel younger', 'klaviyo_value' => 'anti_aging'],
            ['value' => 'injury_recovery', 'label' => 'Heal an injury faster', 'klaviyo_value' => 'injury_recovery'],
            ['value' => 'cognitive', 'label' => 'Think sharper & focus better', 'klaviyo_value' => 'cognitive'],
            ['value' => 'sleep', 'label' => 'Sleep deeper & wake refreshed', 'klaviyo_value' => 'sleep'],
            ['value' => 'immune', 'label' => 'Strengthen my immune system', 'klaviyo_value' => 'immune'],
            ['value' => 'sexual_health', 'label' => 'Improve sexual health & vitality', 'klaviyo_value' => 'sexual_health'],
            ['value' => 'gut_health', 'label' => 'Fix my gut & digestion', 'klaviyo_value' => 'gut_health'],
            ['value' => 'general_wellness', 'label' => 'General wellness & energy', 'klaviyo_value' => 'general_wellness'],
        ];
    }

    private function genderOptions(): array
    {
        return [
            ['value' => 'male', 'label' => 'Male', 'klaviyo_value' => 'male'],
            ['value' => 'female', 'label' => 'Female', 'klaviyo_value' => 'female'],
            ['value' => 'prefer_not', 'label' => 'Prefer not to say', 'klaviyo_value' => 'prefer_not'],
        ];
    }

    private function ageOptions(): array
    {
        return [
            ['value' => '18-29', 'label' => '18-29', 'klaviyo_value' => '18-29'],
            ['value' => '30-39', 'label' => '30-39', 'klaviyo_value' => '30-39'],
            ['value' => '40-49', 'label' => '40-49', 'klaviyo_value' => '40-49'],
            ['value' => '50-59', 'label' => '50-59', 'klaviyo_value' => '50-59'],
            ['value' => '60+', 'label' => '60+', 'klaviyo_value' => '60+'],
        ];
    }

    private function buyingPriorityOptions(): array
    {
        return [
            ['value' => 'doctor_guidance', 'label' => 'I want a doctor\'s guidance (telehealth)', 'klaviyo_value' => 'doctor_guidance'],
            ['value' => 'research_grade', 'label' => 'I want to do my own research (research-grade)', 'klaviyo_value' => 'research_grade'],
            ['value' => 'affordable', 'label' => 'I want the most affordable option', 'klaviyo_value' => 'affordable'],
        ];
    }

    private function buyingConfidenceOptions(): array
    {
        return [
            ['value' => 'price', 'label' => 'Best price', 'klaviyo_value' => 'price'],
            ['value' => 'lab_reports', 'label' => 'Third-party lab reports', 'klaviyo_value' => 'lab_reports'],
            ['value' => 'reviews', 'label' => 'Community reviews & social proof', 'klaviyo_value' => 'reviews'],
            ['value' => 'doctor', 'label' => 'Doctor consultation included', 'klaviyo_value' => 'doctor'],
        ];
    }

    private function feedbackOptions(): array
    {
        return [
            ['value' => 'dosing', 'label' => 'Dosing & protocol guides', 'klaviyo_value' => 'dosing'],
            ['value' => 'stacking', 'label' => 'Peptide stacking strategies', 'klaviyo_value' => 'stacking'],
            ['value' => 'research', 'label' => 'Latest research & studies', 'klaviyo_value' => 'research'],
            ['value' => 'community', 'label' => 'Community experiences & reviews', 'klaviyo_value' => 'community'],
        ];
    }

    private function peptideSelectionOptions(): array
    {
        return [
            ['value' => 'bpc-157', 'label' => 'BPC-157', 'klaviyo_value' => 'BPC-157'],
            ['value' => 'tirzepatide', 'label' => 'Tirzepatide', 'klaviyo_value' => 'Tirzepatide'],
            ['value' => 'cjc-1295-ipamorelin', 'label' => 'CJC-1295 / Ipamorelin', 'klaviyo_value' => 'CJC-1295/Ipamorelin'],
            ['value' => 'epithalon', 'label' => 'Epithalon', 'klaviyo_value' => 'Epithalon'],
            ['value' => 'tb-500', 'label' => 'TB-500', 'klaviyo_value' => 'TB-500'],
            ['value' => 'semax', 'label' => 'Semax', 'klaviyo_value' => 'Semax'],
            ['value' => 'dsip', 'label' => 'DSIP', 'klaviyo_value' => 'DSIP'],
            ['value' => 'thymosin-alpha-1', 'label' => 'Thymosin Alpha 1', 'klaviyo_value' => 'Thymosin Alpha 1'],
            ['value' => 'pt-141', 'label' => 'PT-141', 'klaviyo_value' => 'PT-141'],
            ['value' => 'ghk-cu', 'label' => 'GHK-Cu', 'klaviyo_value' => 'GHK-Cu'],
        ];
    }

    private function healthGoalDynamicStats(): array
    {
        return [
            'fat_loss' => [
                'title' => 'Science-Backed Fat Loss',
                'body' => "Clinical trials show Tirzepatide users lost an average of 22.5% body weight over 72 weeks.\n\nThat's not a fad diet — it's pharmaceutical-grade metabolic science.",
                'source' => 'SURMOUNT-1 Clinical Trial, NEJM 2022',
            ],
            'muscle_growth' => [
                'title' => 'Accelerate Your Gains',
                'body' => "BPC-157 users report up to 40% faster recovery between workouts.\n\nFaster recovery means more training volume and faster muscle growth.",
            ],
            'anti_aging' => [
                'title' => 'Turn Back the Clock',
                'body' => "Epithalon activates telomerase — the enzyme that maintains telomere length, a key marker of cellular age.\n\nStudies show measurable telomere extension after just 3 months.",
            ],
            'injury_recovery' => [
                'title' => 'Heal Faster Than You Thought Possible',
                'body' => "BPC-157 has been shown to accelerate healing of muscles, tendons, and ligaments.\n\nAthletes and patients alike are discovering this recovery breakthrough.",
            ],
            'cognitive' => [
                'title' => 'Sharpen Your Mind',
                'body' => "Semax increases BDNF (brain-derived neurotrophic factor) — your brain's natural growth signal.\n\nUsers report improved focus, memory, and mental clarity within days.",
            ],
            'sleep' => [
                'title' => 'Sleep Like You Used To',
                'body' => "DSIP (Delta Sleep-Inducing Peptide) promotes deep, restorative delta wave sleep.\n\n83% of users report significantly improved sleep quality within 2 weeks.",
            ],
            'immune' => [
                'title' => 'Fortify Your Immune System',
                'body' => "Thymosin Alpha 1 has been clinically proven to enhance T-cell function and immune response.\n\nIt's approved in over 30 countries for immune support.",
            ],
            'sexual_health' => [
                'title' => 'Reignite Your Vitality',
                'body' => "PT-141 is the first FDA-approved peptide for sexual desire.\n\nUnlike other options, it works through the central nervous system for more natural arousal.",
            ],
            'gut_health' => [
                'title' => 'Restore Your Gut',
                'body' => "BPC-157 is derived from a gastric protective protein with remarkable gut-healing properties.\n\n78% of users report significant improvement in digestive symptoms.",
            ],
            'general_wellness' => [
                'title' => 'Optimize Your Whole System',
                'body' => "Peptide therapy is the fastest-growing segment in personalized medicine.\n\nOver 500,000 Americans now use peptides as part of their wellness protocol.",
            ],
            '_default' => [
                'title' => 'The Science Is Clear',
                'body' => "Peptide therapy is backed by thousands of clinical studies.\n\nLet's find the right peptide for your specific goals.",
            ],
        ];
    }
}
