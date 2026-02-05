<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOutcome;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketingQuizSeeder extends Seeder
{
    public function run(): void
    {
        $this->createSegmentationQuiz();
        $this->createProductQuiz();
    }

    protected function createSegmentationQuiz(): void
    {
        // Delete existing segmentation quiz
        Quiz::where('slug', 'peptide-journey')->delete();

        $quiz = Quiz::create([
            'name' => 'Peptide Journey Quiz',
            'slug' => 'peptide-journey',
            'type' => Quiz::TYPE_SEGMENTATION,
            'title' => 'Discover Your Peptide Journey',
            'intro_text' => 'Answer 4 quick questions to get personalized recommendations based on your experience level.',
            'description' => 'Segments users into TOF, MOF, or BOF based on their peptide knowledge and intent.',
            'settings' => [
                'show_progress_bar' => true,
                'allow_back' => true,
                'require_email' => false,
            ],
            'klaviyo_start_event' => 'Segmentation Quiz Started',
            'klaviyo_complete_event' => 'Segmentation Quiz Completed',
            'is_active' => true,
        ]);

        // Question 1: Awareness Level (Key segmentation question)
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'How familiar are you with research peptides?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'order' => 1,
            'klaviyo_property' => 'peptide_awareness',
            'is_required' => true,
            'options' => [
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "I've never heard of them until now",
                    'klaviyo_value' => 'never_heard',
                    'score_tof' => 5,
                    'score_mof' => 0,
                    'score_bof' => 0,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "I've heard about them but don't know much",
                    'klaviyo_value' => 'heard_not_much',
                    'score_tof' => 3,
                    'score_mof' => 2,
                    'score_bof' => 0,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "I've researched them but never tried",
                    'klaviyo_value' => 'researched_not_tried',
                    'score_tof' => 0,
                    'score_mof' => 5,
                    'score_bof' => 0,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "I've used peptides before",
                    'klaviyo_value' => 'used_before',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 5,
                ],
            ],
        ]);

        // Question 2: Intent Level
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'What best describes your current situation?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'order' => 2,
            'klaviyo_property' => 'current_intent',
            'is_required' => true,
            'options' => [
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Just curious about what peptides can do",
                    'klaviyo_value' => 'curious',
                    'score_tof' => 4,
                    'score_mof' => 1,
                    'score_bof' => 0,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Comparing options and doing research",
                    'klaviyo_value' => 'researching',
                    'score_tof' => 1,
                    'score_mof' => 4,
                    'score_bof' => 0,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Looking for a reliable source to buy from",
                    'klaviyo_value' => 'looking_to_buy',
                    'score_tof' => 0,
                    'score_mof' => 1,
                    'score_bof' => 4,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Ready to purchase, just need the right product",
                    'klaviyo_value' => 'ready_to_buy',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 5,
                ],
            ],
        ]);

        // Question 3: Timeline
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'When are you looking to start your peptide journey?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'order' => 3,
            'klaviyo_property' => 'purchase_timeline',
            'is_required' => true,
            'options' => [
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Not sure yet, still learning",
                    'klaviyo_value' => 'not_sure',
                    'score_tof' => 4,
                    'score_mof' => 1,
                    'score_bof' => 0,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Within the next few months",
                    'klaviyo_value' => 'few_months',
                    'score_tof' => 1,
                    'score_mof' => 3,
                    'score_bof' => 1,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Within the next few weeks",
                    'klaviyo_value' => 'few_weeks',
                    'score_tof' => 0,
                    'score_mof' => 2,
                    'score_bof' => 3,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "As soon as possible",
                    'klaviyo_value' => 'asap',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 5,
                ],
            ],
        ]);

        // Question 4: Primary Health Goal
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => "What's your primary health goal?",
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'order' => 4,
            'klaviyo_property' => 'health_goal',
            'is_required' => true,
            'options' => [
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Fat loss & metabolism",
                    'klaviyo_value' => 'fat_loss',
                    'score_tof' => 1,
                    'score_mof' => 1,
                    'score_bof' => 1,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Muscle recovery & healing",
                    'klaviyo_value' => 'recovery',
                    'score_tof' => 1,
                    'score_mof' => 1,
                    'score_bof' => 1,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Anti-aging & skin health",
                    'klaviyo_value' => 'anti_aging',
                    'score_tof' => 1,
                    'score_mof' => 1,
                    'score_bof' => 1,
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'text' => "Cognitive enhancement & focus",
                    'klaviyo_value' => 'cognitive',
                    'score_tof' => 1,
                    'score_mof' => 1,
                    'score_bof' => 1,
                ],
            ],
        ]);

        // Create Outcomes for each segment
        // TOF Outcome
        QuizOutcome::create([
            'quiz_id' => $quiz->id,
            'name' => 'TOF - Education Path',
            'conditions' => ['type' => 'segment', 'segment' => 'tof'],
            'result_title' => 'Welcome to Your Peptide Education!',
            'result_message' => "You're at the perfect starting point. We've prepared a comprehensive guide that breaks down everything you need to know about peptides in simple terms. No science degree required!",
            'redirect_url' => '/peptides-101',
            'redirect_type' => 'page',
            'product_link' => null,
            'klaviyo_event' => 'Segmented as TOF',
            'klaviyo_properties' => ['segment' => 'TOF', 'funnel_stage' => 'awareness'],
            'priority' => 1,
            'is_active' => true,
        ]);

        // MOF Outcome
        QuizOutcome::create([
            'quiz_id' => $quiz->id,
            'name' => 'MOF - Research Path',
            'conditions' => ['type' => 'segment', 'segment' => 'mof'],
            'result_title' => "You're Ready for the Deep Dive!",
            'result_message' => "You've done your homework and you're ready for more. Check out our comprehensive peptide tier list to see which ones are right for your goals.",
            'redirect_url' => '/peptide-tier-list',
            'redirect_type' => 'page',
            'product_link' => null,
            'klaviyo_event' => 'Segmented as MOF',
            'klaviyo_properties' => ['segment' => 'MOF', 'funnel_stage' => 'consideration'],
            'priority' => 2,
            'is_active' => true,
        ]);

        // BOF Outcome
        QuizOutcome::create([
            'quiz_id' => $quiz->id,
            'name' => 'BOF - Purchase Path',
            'conditions' => ['type' => 'segment', 'segment' => 'bof'],
            'result_title' => "Let's Find Your Perfect Peptide!",
            'result_message' => "You know what you want and you're ready to go. Take our product quiz to get matched with the exact peptide for your goals, plus an exclusive welcome offer.",
            'redirect_url' => '/quiz/product-match',
            'redirect_type' => 'quiz',
            'product_link' => 'https://fastpeptix.com',
            'klaviyo_event' => 'Segmented as BOF',
            'klaviyo_properties' => ['segment' => 'BOF', 'funnel_stage' => 'decision'],
            'priority' => 3,
            'is_active' => true,
        ]);
    }

    protected function createProductQuiz(): void
    {
        // Delete existing product quiz
        Quiz::where('slug', 'product-match')->delete();

        $quiz = Quiz::create([
            'name' => 'Product Match Quiz',
            'slug' => 'product-match',
            'type' => Quiz::TYPE_PRODUCT,
            'title' => 'Find Your Perfect Peptide',
            'intro_text' => 'Answer a few questions about your goals and we\'ll recommend the ideal peptide for you.',
            'description' => 'Matches users to specific peptide products based on their goals.',
            'settings' => [
                'show_progress_bar' => true,
                'allow_back' => true,
                'require_email' => true,
                'email_step' => 'before_results',
            ],
            'klaviyo_start_event' => 'Product Quiz Started',
            'klaviyo_complete_event' => 'Product Quiz Completed',
            'is_active' => true,
        ]);

        // Question 1: Primary Goal (Key product selector)
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => "What's your #1 goal right now?",
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'order' => 1,
            'klaviyo_property' => 'primary_goal',
            'is_required' => true,
            'options' => [
                [
                    'id' => 'goal_fat_loss',
                    'text' => 'Lose stubborn fat & boost metabolism',
                    'klaviyo_value' => 'fat_loss',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 1,
                    'product_tags' => ['semaglutide', 'tirzepatide', 'aod9604'],
                ],
                [
                    'id' => 'goal_recovery',
                    'text' => 'Heal injuries & recover faster',
                    'klaviyo_value' => 'recovery',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 1,
                    'product_tags' => ['bpc157', 'tb500', 'pentadecapeptide'],
                ],
                [
                    'id' => 'goal_muscle',
                    'text' => 'Build muscle & improve strength',
                    'klaviyo_value' => 'muscle',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 1,
                    'product_tags' => ['cjc1295', 'ipamorelin', 'mk677'],
                ],
                [
                    'id' => 'goal_antiaging',
                    'text' => 'Anti-aging & better skin',
                    'klaviyo_value' => 'anti_aging',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 1,
                    'product_tags' => ['ghkcu', 'epithalon', 'thymosin'],
                ],
                [
                    'id' => 'goal_cognitive',
                    'text' => 'Sharper focus & mental clarity',
                    'klaviyo_value' => 'cognitive',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 1,
                    'product_tags' => ['semax', 'selank', 'dihexa'],
                ],
                [
                    'id' => 'goal_sleep',
                    'text' => 'Better sleep & relaxation',
                    'klaviyo_value' => 'sleep',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 1,
                    'product_tags' => ['dsip', 'ghrp6', 'sermorelin'],
                ],
            ],
        ]);

        // Question 2: Experience Level
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'Have you used injectable research compounds before?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'order' => 2,
            'klaviyo_property' => 'injection_experience',
            'is_required' => true,
            'options' => [
                [
                    'id' => 'exp_never',
                    'text' => 'No, this would be my first time',
                    'klaviyo_value' => 'beginner',
                    'score_tof' => 2,
                    'score_mof' => 0,
                    'score_bof' => 0,
                ],
                [
                    'id' => 'exp_some',
                    'text' => 'Yes, a few times',
                    'klaviyo_value' => 'intermediate',
                    'score_tof' => 0,
                    'score_mof' => 1,
                    'score_bof' => 1,
                ],
                [
                    'id' => 'exp_regular',
                    'text' => 'Yes, I do it regularly',
                    'klaviyo_value' => 'advanced',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 2,
                ],
            ],
        ]);

        // Question 3: Budget
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => "What's your monthly budget for research peptides?",
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'order' => 3,
            'klaviyo_property' => 'budget_range',
            'is_required' => true,
            'options' => [
                [
                    'id' => 'budget_low',
                    'text' => 'Under $100/month',
                    'klaviyo_value' => 'budget_conscious',
                    'score_tof' => 1,
                    'score_mof' => 1,
                    'score_bof' => 0,
                ],
                [
                    'id' => 'budget_mid',
                    'text' => '$100-$250/month',
                    'klaviyo_value' => 'moderate_budget',
                    'score_tof' => 0,
                    'score_mof' => 1,
                    'score_bof' => 1,
                ],
                [
                    'id' => 'budget_high',
                    'text' => '$250+/month',
                    'klaviyo_value' => 'premium_budget',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 2,
                ],
            ],
        ]);

        // Question 4: Urgency
        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_text' => 'How quickly do you want to see results?',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'order' => 4,
            'klaviyo_property' => 'result_timeline',
            'is_required' => true,
            'options' => [
                [
                    'id' => 'time_patient',
                    'text' => "I'm patient, 2-3 months is fine",
                    'klaviyo_value' => 'patient',
                    'score_tof' => 1,
                    'score_mof' => 1,
                    'score_bof' => 0,
                ],
                [
                    'id' => 'time_moderate',
                    'text' => 'I want to see something in 4-6 weeks',
                    'klaviyo_value' => 'moderate_urgency',
                    'score_tof' => 0,
                    'score_mof' => 1,
                    'score_bof' => 1,
                ],
                [
                    'id' => 'time_fast',
                    'text' => 'I need results as fast as possible',
                    'klaviyo_value' => 'high_urgency',
                    'score_tof' => 0,
                    'score_mof' => 0,
                    'score_bof' => 2,
                ],
            ],
        ]);

        // Create Product Outcomes
        $products = [
            'fat_loss' => [
                'name' => 'Fat Loss Stack',
                'title' => 'Your Perfect Match: Semaglutide',
                'message' => "Based on your goals, Semaglutide is your ideal peptide. It's the same active ingredient in Ozempic and Wegovy, proven to reduce appetite and accelerate fat loss. Clinical studies show average weight loss of 15% body weight.",
                'product_link' => 'https://fastpeptix.com/product/semaglutide',
            ],
            'recovery' => [
                'name' => 'Recovery Stack',
                'title' => 'Your Perfect Match: BPC-157',
                'message' => "BPC-157 is exactly what you need. Known as the 'Wolverine peptide', it accelerates healing of tendons, ligaments, and muscles. Athletes report significantly faster recovery times and reduced inflammation.",
                'product_link' => 'https://fastpeptix.com/product/bpc-157',
            ],
            'muscle' => [
                'name' => 'Muscle Stack',
                'title' => 'Your Perfect Match: CJC-1295 + Ipamorelin',
                'message' => "This powerful combination stimulates natural growth hormone production. You'll experience increased muscle mass, better recovery, and improved body composition without the risks of synthetic HGH.",
                'product_link' => 'https://fastpeptix.com/product/cjc-1295-ipamorelin',
            ],
            'anti_aging' => [
                'name' => 'Anti-Aging Stack',
                'title' => 'Your Perfect Match: GHK-Cu',
                'message' => "GHK-Cu is nature's anti-aging secret. It stimulates collagen production, improves skin elasticity, and promotes cellular regeneration. Users report visibly younger-looking skin within weeks.",
                'product_link' => 'https://fastpeptix.com/product/ghk-cu',
            ],
            'cognitive' => [
                'name' => 'Cognitive Stack',
                'title' => 'Your Perfect Match: Semax',
                'message' => "Semax is your brain's best friend. Originally developed for stroke patients, it enhances memory, focus, and mental clarity. Many users report feeling sharper and more productive within days.",
                'product_link' => 'https://fastpeptix.com/product/semax',
            ],
            'sleep' => [
                'name' => 'Sleep Stack',
                'title' => 'Your Perfect Match: DSIP',
                'message' => "DSIP (Delta Sleep-Inducing Peptide) naturally regulates your sleep cycle. Unlike sleeping pills, it promotes deep, restorative sleep without grogginess. Wake up refreshed and energized.",
                'product_link' => 'https://fastpeptix.com/product/dsip',
            ],
        ];

        $priority = 1;
        foreach ($products as $goal => $data) {
            QuizOutcome::create([
                'quiz_id' => $quiz->id,
                'name' => $data['name'],
                'conditions' => ['type' => 'answer', 'question' => 'primary_goal', 'value' => $goal],
                'result_title' => $data['title'],
                'result_message' => $data['message'] . "\n\n🎁 Use code WELCOME15 for 15% off your first order!",
                'redirect_url' => '/go/product-' . str_replace('_', '-', $goal),
                'redirect_type' => 'outbound',
                'product_link' => $data['product_link'],
                'klaviyo_event' => 'Product Recommended: ' . $data['name'],
                'klaviyo_properties' => [
                    'recommended_product' => $data['name'],
                    'primary_goal' => $goal,
                ],
                'priority' => $priority++,
                'is_active' => true,
            ]);
        }
    }
}
