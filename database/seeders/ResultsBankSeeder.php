<?php

namespace Database\Seeders;

use App\Models\ResultsBank;
use App\Models\StackProduct;
use Illuminate\Database\Seeder;

class ResultsBankSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            // Fat Loss & Metabolism
            [
                'health_goal' => 'fat_loss',
                'experience_level' => 'beginner',
                'peptide_name' => 'Tirzepatide',
                'peptide_slug' => 'tirzepatide',
                'star_rating' => 4.9,
                'rating_label' => 'Excellent Match',
                'description' => 'Tirzepatide is a dual GIP/GLP-1 receptor agonist that has shown remarkable results for weight management. It works by reducing appetite and improving metabolic function.',
                'benefits' => ['Reduces appetite naturally', 'Improves insulin sensitivity', 'Promotes sustained fat loss', 'Well-studied safety profile'],
                'testimonial' => 'I lost 30 pounds in 4 months without feeling hungry all the time. Tirzepatide changed my relationship with food completely.',
                'testimonial_author' => 'Sarah M., Age 34',
            ],
            [
                'health_goal' => 'fat_loss',
                'experience_level' => 'advanced',
                'peptide_name' => 'Retatrutide',
                'peptide_slug' => 'retatrutide',
                'star_rating' => 4.8,
                'rating_label' => 'Excellent Match',
                'description' => 'Retatrutide is a next-generation triple agonist (GIP/GLP-1/glucagon receptor) that has shown up to 24% body weight loss in clinical trials — the most potent weight loss peptide in development.',
                'benefits' => ['Triple receptor action', 'Up to 24% weight loss in trials', 'Superior to dual agonists', 'Enhanced metabolic effects'],
                'testimonial' => 'After plateauing on other GLP-1 options, Retatrutide broke through. The triple-action mechanism is a game-changer for experienced users.',
                'testimonial_author' => 'Mike R., Age 41',
            ],

            // Muscle Growth & Recovery
            [
                'health_goal' => 'muscle_growth',
                'experience_level' => 'beginner',
                'peptide_name' => 'CJC-1295 + Ipamorelin',
                'peptide_slug' => 'cjc-1295-ipamorelin',
                'star_rating' => 4.8,
                'rating_label' => 'Excellent Match',
                'description' => 'The CJC-1295/Ipamorelin combination stimulates natural growth hormone release, promoting lean muscle gains, improved recovery, and better sleep — ideal for beginners.',
                'benefits' => ['Boosts natural GH production', 'Promotes lean muscle growth', 'Improves deep sleep quality', 'Enhanced fat metabolism'],
                'testimonial' => 'As my first peptide stack, CJC/Ipa was perfect. Better sleep, faster recovery, and noticeable muscle gains within 8 weeks.',
                'testimonial_author' => 'James T., Age 28',
            ],
            [
                'health_goal' => 'muscle_growth',
                'experience_level' => 'advanced',
                'peptide_name' => 'IGF1-LR3',
                'peptide_slug' => 'igf1-lr3',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'IGF1-LR3 is a modified form of IGF-1 with an extended half-life, promoting significant muscle hypertrophy and enhanced protein synthesis for advanced users looking to maximize gains.',
                'benefits' => ['Potent muscle hypertrophy', 'Enhanced protein synthesis', 'Extended half-life vs native IGF-1', 'Hyperplasia potential'],
                'testimonial' => 'IGF1-LR3 took my training to the next level when CJC/Ipa was no longer enough. Noticeable fullness and growth in just 4 weeks.',
                'testimonial_author' => 'David K., Age 38',
            ],

            // Anti-Aging & Longevity
            [
                'health_goal' => 'anti_aging',
                'experience_level' => 'beginner',
                'peptide_name' => 'GHK-Cu',
                'peptide_slug' => 'ghk-cu',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'GHK-Cu (copper peptide) is a naturally occurring tripeptide with broad regenerative properties including skin remodeling, collagen synthesis, and anti-inflammatory effects — perfect for anti-aging beginners.',
                'benefits' => ['Stimulates collagen synthesis', 'Promotes skin regeneration', 'Reduces fine lines and wrinkles', 'Anti-inflammatory effects'],
                'testimonial' => 'After 3 months on GHK-Cu, my skin looks noticeably better and my energy levels improved. A gentle first step into anti-aging peptides.',
                'testimonial_author' => 'Linda P., Age 52',
            ],
            [
                'health_goal' => 'anti_aging',
                'experience_level' => 'advanced',
                'peptide_name' => 'GHK-Cu + Epithalon',
                'peptide_slug' => 'ghk-cu',
                'star_rating' => 4.8,
                'rating_label' => 'Excellent Match',
                'description' => 'The GHK-Cu and Epithalon combination addresses anti-aging from two angles: GHK-Cu for skin and tissue regeneration, Epithalon for telomere maintenance and cellular longevity.',
                'benefits' => ['Dual anti-aging mechanism', 'Telomere maintenance', 'Skin and tissue regeneration', 'Cellular longevity support'],
                'testimonial' => 'Combining GHK-Cu with Epithalon was the upgrade my protocol needed. Visible skin improvements plus deeper cellular benefits.',
                'testimonial_author' => 'Karen S., Age 47',
            ],

            // Injury Recovery & Healing
            [
                'health_goal' => 'injury_recovery',
                'experience_level' => 'beginner',
                'peptide_name' => 'BPC-157 + TB-500',
                'peptide_slug' => 'bpc-157',
                'star_rating' => 4.9,
                'rating_label' => 'Excellent Match',
                'description' => 'The BPC-157 and TB-500 combination is the gold standard for injury recovery. BPC-157 promotes local tissue repair while TB-500 provides systemic healing and new blood vessel formation.',
                'benefits' => ['Accelerates wound healing', 'Repairs tendons and ligaments', 'Systemic + local healing', 'Promotes new blood vessel growth'],
                'testimonial' => 'After my rotator cuff injury, BPC-157 + TB-500 cut my recovery time in half. My physical therapist was amazed at my progress.',
                'testimonial_author' => 'Tom W., Age 45',
            ],
            [
                'health_goal' => 'injury_recovery',
                'experience_level' => 'advanced',
                'peptide_name' => 'BPC-157 + TB-500 (Higher Dose)',
                'peptide_slug' => 'bpc-157',
                'star_rating' => 4.8,
                'rating_label' => 'Excellent Match',
                'description' => 'For experienced users, the BPC-157 + TB-500 stack at higher doses provides accelerated recovery for chronic and stubborn injuries. The synergistic effect addresses both local tissue repair and systemic healing.',
                'benefits' => ['Higher-dose protocol', 'Chronic injury recovery', 'Synergistic healing', 'Reduces scar tissue'],
                'testimonial' => 'The higher dose BPC/TB stack finally healed my chronic knee issue that I had for 2 years. Nothing else came close.',
                'testimonial_author' => 'Ryan L., Age 36',
            ],

            // Cognitive Enhancement
            [
                'health_goal' => 'cognitive',
                'experience_level' => 'beginner',
                'peptide_name' => 'NAD+',
                'peptide_slug' => 'nad-plus',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'NAD+ (Nicotinamide Adenine Dinucleotide) is a critical coenzyme that supports cellular energy production, DNA repair, and cognitive function. Levels decline with age, making supplementation increasingly important.',
                'benefits' => ['Cellular energy production', 'DNA repair support', 'Mental clarity and focus', 'Anti-aging at cellular level'],
                'testimonial' => 'NAD+ gave me the mental clarity I was looking for. Better focus, more energy, and none of the jitteriness of stimulants.',
                'testimonial_author' => 'Alex C., Age 32',
            ],
            [
                'health_goal' => 'cognitive',
                'experience_level' => 'advanced',
                'peptide_name' => 'Semax',
                'peptide_slug' => 'semax',
                'star_rating' => 4.6,
                'rating_label' => 'Strong Match',
                'description' => 'Semax is a synthetic analog of ACTH with powerful neuroprotective and cognitive-enhancing properties. It increases BDNF (brain-derived neurotrophic factor) for enhanced memory, focus, and learning.',
                'benefits' => ['Increases BDNF levels', 'Enhanced memory and learning', 'Neuroprotective effects', 'Improved attention span'],
                'testimonial' => 'Semax combined with my existing nootropic stack gives me all-day sharp focus. The BDNF boost is noticeable within days.',
                'testimonial_author' => 'Jennifer H., Age 29',
            ],

            // Sleep Optimization
            [
                'health_goal' => 'sleep',
                'experience_level' => 'beginner',
                'peptide_name' => 'CJC-1295 + Ipamorelin',
                'peptide_slug' => 'cjc-1295-ipamorelin',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'The CJC-1295/Ipamorelin combination enhances natural growth hormone pulses during sleep, promoting deeper and more restorative rest without the risks of dedicated sleep peptides.',
                'benefits' => ['Enhances GH pulse during sleep', 'Deeper REM and delta sleep', 'Improved recovery during sleep', 'Better next-day energy'],
                'testimonial' => 'CJC/Ipa transformed my sleep quality. I track my sleep and my deep sleep went from 45 minutes to 2+ hours.',
                'testimonial_author' => 'Patricia N., Age 48',
            ],
            [
                'health_goal' => 'sleep',
                'experience_level' => 'advanced',
                'peptide_name' => 'DSIP',
                'peptide_slug' => 'dsip',
                'star_rating' => 4.6,
                'rating_label' => 'Strong Match',
                'description' => 'DSIP (Delta Sleep-Inducing Peptide) directly promotes deep, restorative delta wave sleep without grogginess. For advanced users who need targeted sleep optimization beyond GH-based approaches.',
                'benefits' => ['Promotes deep delta wave sleep', 'Non-habit forming', 'Reduces stress hormones', 'Targeted sleep architecture'],
                'testimonial' => 'After trying CJC/Ipa for sleep, adding DSIP was the final piece. Pure, deep, restorative sleep every single night.',
                'testimonial_author' => 'Chris B., Age 43',
            ],

            // Immune Support
            [
                'health_goal' => 'immune',
                'experience_level' => 'beginner',
                'peptide_name' => 'Thymosin Alpha 1',
                'peptide_slug' => 'thymosin-alpha-1',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'Thymosin Alpha 1 is a naturally occurring peptide that modulates and enhances immune function, particularly T-cell mediated immunity.',
                'benefits' => ['Enhances T-cell function', 'Modulates immune response', 'Clinically studied extensively', 'Supports chronic infection recovery'],
                'testimonial' => 'Since starting Thymosin Alpha 1, I have not gotten sick once this winter. My immune system feels bulletproof.',
                'testimonial_author' => 'Robert G., Age 55',
            ],
            [
                'health_goal' => 'immune',
                'experience_level' => 'advanced',
                'peptide_name' => 'LL-37',
                'peptide_slug' => 'll-37',
                'star_rating' => 4.5,
                'rating_label' => 'Good Match',
                'description' => 'LL-37 is a human cathelicidin antimicrobial peptide with broad-spectrum antimicrobial, anti-biofilm, and immunomodulatory properties.',
                'benefits' => ['Broad-spectrum antimicrobial', 'Breaks down biofilms', 'Modulates inflammation', 'Supports mucosal immunity'],
                'testimonial' => 'LL-37 helped me address chronic sinus issues that antibiotics could not resolve. My functional medicine doctor is now a believer.',
                'testimonial_author' => 'Maria V., Age 42',
            ],

            // Sexual Health & Vitality
            [
                'health_goal' => 'sexual_health',
                'experience_level' => 'beginner',
                'peptide_name' => 'PT-141 (Bremelanotide)',
                'peptide_slug' => 'pt-141',
                'star_rating' => 4.6,
                'rating_label' => 'Strong Match',
                'description' => 'PT-141 works through the central nervous system to enhance sexual arousal and desire. It is FDA-approved for hypoactive sexual desire disorder.',
                'benefits' => ['Enhances sexual desire', 'Works via CNS pathway', 'FDA-approved (Vyleesi)', 'Effective for both men and women'],
                'testimonial' => 'PT-141 brought back the spark I thought was gone. It works differently than anything else I have tried — more natural feeling.',
                'testimonial_author' => 'Michelle D., Age 39',
            ],
            [
                'health_goal' => 'sexual_health',
                'experience_level' => 'advanced',
                'peptide_name' => 'Kisspeptin',
                'peptide_slug' => 'kisspeptin',
                'star_rating' => 4.4,
                'rating_label' => 'Good Match',
                'description' => 'Kisspeptin is a neuropeptide that plays a crucial role in reproductive hormone regulation, stimulating GnRH release and supporting healthy hormone levels.',
                'benefits' => ['Stimulates natural hormone production', 'Supports reproductive health', 'Enhances libido', 'Hormone-axis regulation'],
                'testimonial' => 'Kisspeptin helped normalize my hormone levels naturally. Combined with my TRT protocol, the results have been outstanding.',
                'testimonial_author' => 'Steve F., Age 46',
            ],

            // Gut Health
            [
                'health_goal' => 'gut_health',
                'experience_level' => 'beginner',
                'peptide_name' => 'BPC-157',
                'peptide_slug' => 'bpc-157',
                'star_rating' => 4.8,
                'rating_label' => 'Excellent Match',
                'description' => 'BPC-157 is derived from a gastric protective protein and has shown remarkable gut-healing properties, repairing intestinal lining and reducing inflammation.',
                'benefits' => ['Heals intestinal lining', 'Reduces gut inflammation', 'Protects against NSAID damage', 'Supports leaky gut recovery'],
                'testimonial' => 'Years of IBS and nothing helped until BPC-157. Within 3 weeks, my bloating and discomfort decreased dramatically.',
                'testimonial_author' => 'Amanda K., Age 37',
            ],
            [
                'health_goal' => 'gut_health',
                'experience_level' => 'advanced',
                'peptide_name' => 'Larazotide',
                'peptide_slug' => 'larazotide',
                'star_rating' => 4.5,
                'rating_label' => 'Good Match',
                'description' => 'Larazotide acetate is a tight junction regulator that helps seal intestinal permeability, making it ideal for advanced gut healing protocols.',
                'benefits' => ['Seals tight junctions', 'Reduces intestinal permeability', 'Targets leaky gut directly', 'Well-tolerated with few side effects'],
                'testimonial' => 'Adding Larazotide to my BPC-157 protocol was the final piece. My food sensitivities have reduced significantly.',
                'testimonial_author' => 'Daniel J., Age 40',
            ],

            // General Wellness
            [
                'health_goal' => 'general_wellness',
                'experience_level' => 'beginner',
                'peptide_name' => 'MOTS-C',
                'peptide_slug' => 'mots-c',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'MOTS-C is a mitochondrial-derived peptide that enhances metabolic homeostasis, improves exercise capacity, and supports overall cellular energy — making it an ideal first peptide for general wellness.',
                'benefits' => ['Metabolic optimization', 'Improved exercise capacity', 'Mitochondrial support', 'Cellular energy boost'],
                'testimonial' => 'MOTS-C was my first peptide and the results were incredible. More energy, better workouts, and a general sense of vitality I haven\'t felt in years.',
                'testimonial_author' => 'Nancy L., Age 44',
            ],
            [
                'health_goal' => 'general_wellness',
                'experience_level' => 'advanced',
                'peptide_name' => 'CJC-1295 / Ipamorelin',
                'peptide_slug' => 'cjc-1295-ipamorelin',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'The CJC-1295/Ipamorelin combination provides comprehensive wellness benefits through optimized growth hormone levels — better sleep, recovery, body composition, and energy.',
                'benefits' => ['Optimizes growth hormone', 'Improves body composition', 'Better sleep and recovery', 'Enhanced energy levels'],
                'testimonial' => 'This is the cornerstone of my wellness protocol. Better sleep, more energy, and I look better than I did 10 years ago.',
                'testimonial_author' => 'Greg M., Age 50',
            ],
        ];

        foreach ($entries as $entry) {
            ResultsBank::updateOrCreate(
                [
                    'health_goal' => $entry['health_goal'],
                    'experience_level' => $entry['experience_level'],
                ],
                $entry
            );
        }

        $this->command->info('Seeded ' . count($entries) . ' Results Bank entries.');

        // Link each ResultsBank entry to its matching StackProduct
        $slugMap = [
            'kisspeptin' => 'kisspeptin-10',
        ];

        $linked = 0;
        foreach (ResultsBank::all() as $entry) {
            $slug = $slugMap[$entry->peptide_slug] ?? $entry->peptide_slug;
            $stackProduct = StackProduct::where('slug', $slug)->first();
            if ($stackProduct) {
                $entry->update(['stack_product_id' => $stackProduct->id]);
                $linked++;
            }
        }

        $this->command->info("Linked {$linked} Results Bank entries to Stack Products.");
    }
}
