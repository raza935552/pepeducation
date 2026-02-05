<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOutcome;
use App\Models\QuizResponse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data in correct order (respecting FK constraints)
        Schema::disableForeignKeyConstraints();
        QuizResponse::truncate();
        QuizOutcome::truncate();
        QuizQuestion::truncate();
        Quiz::truncate();
        Schema::enableForeignKeyConstraints();

        $quiz = Quiz::create([
            'name' => 'Peptide Finder Quiz',
            'slug' => 'find-your-peptide',
            'title' => 'Find Your Ideal Peptide',
            'description' => 'Answer a few questions to discover which peptide matches your research goals.',
            'type' => 'segmentation',
            'is_active' => true,
            'settings' => [
                'require_email' => true,
                'email_step' => 'before_results',
                'show_progress_bar' => true,
                'allow_back' => true,
                'auto_advance' => true,
            ],
            'design' => [
                'primary_color' => '#9A7B4F',
                'background_color' => '#FDFCFA',
                'button_style' => 'rounded',
            ],
        ]);

        // Question 1: Research Focus
        $quiz->questions()->create([
            'question_text' => 'What is your primary research focus?',
            'question_type' => 'single',
            'order' => 1,
            'klaviyo_property' => 'research_focus',
            'options' => [
                ['id' => 'opt_a', 'text' => 'Recovery & Healing', 'klaviyo_value' => 'recovery', 'score_tof' => 1, 'score_mof' => 2, 'score_bof' => 3],
                ['id' => 'opt_b', 'text' => 'Weight Management', 'klaviyo_value' => 'weight', 'score_tof' => 2, 'score_mof' => 2, 'score_bof' => 2],
                ['id' => 'opt_c', 'text' => 'Cognitive Function', 'klaviyo_value' => 'cognitive', 'score_tof' => 3, 'score_mof' => 2, 'score_bof' => 1],
                ['id' => 'opt_d', 'text' => 'General Wellness', 'klaviyo_value' => 'wellness', 'score_tof' => 3, 'score_mof' => 1, 'score_bof' => 1],
            ],
        ]);

        // Question 2: Experience Level
        $quiz->questions()->create([
            'question_text' => 'How familiar are you with peptide research?',
            'question_type' => 'single',
            'order' => 2,
            'klaviyo_property' => 'experience_level',
            'options' => [
                ['id' => 'opt_a', 'text' => 'Just starting to learn', 'klaviyo_value' => 'beginner', 'score_tof' => 3, 'score_mof' => 1, 'score_bof' => 0],
                ['id' => 'opt_b', 'text' => 'Some knowledge', 'klaviyo_value' => 'intermediate', 'score_tof' => 1, 'score_mof' => 3, 'score_bof' => 1],
                ['id' => 'opt_c', 'text' => 'Experienced researcher', 'klaviyo_value' => 'advanced', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 3],
            ],
        ]);

        // Question 3: Timeline
        $quiz->questions()->create([
            'question_text' => 'When do you plan to begin your research?',
            'question_type' => 'single',
            'order' => 3,
            'klaviyo_property' => 'timeline',
            'options' => [
                ['id' => 'opt_a', 'text' => 'Within the next week', 'klaviyo_value' => 'immediate', 'score_tof' => 0, 'score_mof' => 1, 'score_bof' => 5],
                ['id' => 'opt_b', 'text' => 'Within the next month', 'klaviyo_value' => 'soon', 'score_tof' => 1, 'score_mof' => 3, 'score_bof' => 2],
                ['id' => 'opt_c', 'text' => 'Still researching options', 'klaviyo_value' => 'researching', 'score_tof' => 3, 'score_mof' => 2, 'score_bof' => 0],
            ],
        ]);

        // Outcome 1: BOF (Ready to Start)
        $quiz->outcomes()->create([
            'name' => 'Ready to Start',
            'conditions' => [
                'type' => 'segment',
                'segment' => 'bof',
            ],
            'result_title' => 'Ready to Start',
            'result_message' => 'You have the experience and timeline to begin your peptide research journey.',
            'redirect_url' => '/peptides',
            'redirect_type' => 'internal',
            'klaviyo_event' => 'Quiz Outcome BOF',
            'priority' => 1,
            'is_active' => true,
        ]);

        // Outcome 2: MOF (Researcher in Training)
        $quiz->outcomes()->create([
            'name' => 'Researcher in Training',
            'conditions' => [
                'type' => 'segment',
                'segment' => 'mof',
            ],
            'result_title' => 'Researcher in Training',
            'result_message' => 'You are building knowledge and getting ready. Our calculator and guides can help.',
            'redirect_url' => '/calculator',
            'redirect_type' => 'internal',
            'klaviyo_event' => 'Quiz Outcome MOF',
            'priority' => 2,
            'is_active' => true,
        ]);

        // Outcome 3: TOF (Explorer)
        $quiz->outcomes()->create([
            'name' => 'Explorer',
            'conditions' => [
                'type' => 'segment',
                'segment' => 'tof',
            ],
            'result_title' => 'Explorer',
            'result_message' => 'You are just getting started. Browse our peptide encyclopedia to learn more.',
            'redirect_url' => '/peptides',
            'redirect_type' => 'internal',
            'klaviyo_event' => 'Quiz Outcome TOF',
            'priority' => 3,
            'is_active' => true,
        ]);
    }
}
