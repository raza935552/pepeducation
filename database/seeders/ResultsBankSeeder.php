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
                'peptide_name' => 'Tesamorelin',
                'peptide_slug' => 'tesamorelin',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'Tesamorelin is a growth hormone-releasing hormone (GHRH) analog that specifically targets visceral fat reduction while preserving lean muscle mass.',
                'benefits' => ['Targets visceral belly fat', 'Preserves muscle mass', 'Improves body composition', 'FDA-approved for lipodystrophy'],
                'testimonial' => 'As someone who already works out regularly, Tesamorelin helped me break through my plateau and lose that stubborn belly fat.',
                'testimonial_author' => 'Mike R., Age 41',
            ],

            // Muscle Growth & Recovery
            [
                'health_goal' => 'muscle_growth',
                'experience_level' => 'beginner',
                'peptide_name' => 'BPC-157',
                'peptide_slug' => 'bpc-157',
                'star_rating' => 4.8,
                'rating_label' => 'Excellent Match',
                'description' => 'BPC-157 (Body Protection Compound) is a synthetic peptide derived from a protein found in gastric juice. It accelerates healing and supports muscle recovery.',
                'benefits' => ['Accelerates muscle recovery', 'Supports tendon and ligament healing', 'Reduces inflammation', 'Gut-protective properties'],
                'testimonial' => 'My recovery time between workouts dropped dramatically. I can train harder and more frequently without the usual soreness.',
                'testimonial_author' => 'James T., Age 28',
            ],
            [
                'health_goal' => 'muscle_growth',
                'experience_level' => 'advanced',
                'peptide_name' => 'CJC-1295 / Ipamorelin',
                'peptide_slug' => 'cjc-1295-ipamorelin',
                'star_rating' => 4.8,
                'rating_label' => 'Excellent Match',
                'description' => 'The CJC-1295/Ipamorelin combination stimulates natural growth hormone release, promoting lean muscle gains, improved recovery, and better sleep.',
                'benefits' => ['Boosts natural GH production', 'Promotes lean muscle growth', 'Improves deep sleep quality', 'Enhanced fat metabolism'],
                'testimonial' => 'This combo took my training to the next level. Better sleep, faster recovery, and noticeable muscle gains within 8 weeks.',
                'testimonial_author' => 'David K., Age 38',
            ],

            // Anti-Aging & Longevity
            [
                'health_goal' => 'anti_aging',
                'experience_level' => 'beginner',
                'peptide_name' => 'Epithalon',
                'peptide_slug' => 'epithalon',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'Epithalon is a synthetic tetrapeptide that stimulates telomerase production, which helps maintain telomere length — a key marker of cellular aging.',
                'benefits' => ['Supports telomere maintenance', 'Promotes cellular longevity', 'Improves sleep patterns', 'Antioxidant properties'],
                'testimonial' => 'After 3 months on Epithalon, my energy levels improved and my skin looks noticeably better. I feel years younger.',
                'testimonial_author' => 'Linda P., Age 52',
            ],
            [
                'health_goal' => 'anti_aging',
                'experience_level' => 'advanced',
                'peptide_name' => 'GHK-Cu',
                'peptide_slug' => 'ghk-cu',
                'star_rating' => 4.6,
                'rating_label' => 'Strong Match',
                'description' => 'GHK-Cu (copper peptide) is a naturally occurring tripeptide with broad regenerative properties including skin remodeling, wound healing, and anti-inflammatory effects.',
                'benefits' => ['Stimulates collagen synthesis', 'Promotes skin regeneration', 'Reduces fine lines and wrinkles', 'Anti-inflammatory effects'],
                'testimonial' => 'GHK-Cu combined with my existing protocol has been incredible for skin quality and overall recovery. Visible results within weeks.',
                'testimonial_author' => 'Karen S., Age 47',
            ],

            // Injury Recovery & Healing
            [
                'health_goal' => 'injury_recovery',
                'experience_level' => 'beginner',
                'peptide_name' => 'BPC-157',
                'peptide_slug' => 'bpc-157',
                'star_rating' => 4.9,
                'rating_label' => 'Excellent Match',
                'description' => 'BPC-157 is considered the gold standard for injury recovery peptides. It promotes healing of muscles, tendons, ligaments, and even gut tissue.',
                'benefits' => ['Accelerates wound healing', 'Repairs tendons and ligaments', 'Reduces inflammation', 'Protects against NSAID damage'],
                'testimonial' => 'After my rotator cuff injury, BPC-157 cut my recovery time in half. My physical therapist was amazed at my progress.',
                'testimonial_author' => 'Tom W., Age 45',
            ],
            [
                'health_goal' => 'injury_recovery',
                'experience_level' => 'advanced',
                'peptide_name' => 'TB-500',
                'peptide_slug' => 'tb-500',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'TB-500 (Thymosin Beta-4) promotes cell migration and new blood vessel formation, making it highly effective for tissue repair and recovery from chronic injuries.',
                'benefits' => ['Promotes new blood vessel growth', 'Reduces scar tissue', 'Enhances flexibility', 'Systemic healing effects'],
                'testimonial' => 'I stack TB-500 with BPC-157 for serious injuries. This combination healed my chronic knee issue that I had for 2 years.',
                'testimonial_author' => 'Ryan L., Age 36',
            ],

            // Cognitive Enhancement
            [
                'health_goal' => 'cognitive',
                'experience_level' => 'beginner',
                'peptide_name' => 'Semax',
                'peptide_slug' => 'semax',
                'star_rating' => 4.6,
                'rating_label' => 'Strong Match',
                'description' => 'Semax is a synthetic analog of ACTH that enhances memory, focus, and cognitive function. It also has neuroprotective and anxiolytic properties.',
                'benefits' => ['Improves focus and concentration', 'Enhances memory retention', 'Reduces anxiety', 'Neuroprotective effects'],
                'testimonial' => 'Semax gave me the mental clarity I was looking for without any jittery side effects. My productivity has skyrocketed.',
                'testimonial_author' => 'Alex C., Age 32',
            ],
            [
                'health_goal' => 'cognitive',
                'experience_level' => 'advanced',
                'peptide_name' => 'Selank',
                'peptide_slug' => 'selank',
                'star_rating' => 4.5,
                'rating_label' => 'Good Match',
                'description' => 'Selank is a synthetic peptide analog of the immunomodulatory peptide tuftsin, known for its anxiolytic and nootropic effects without sedation.',
                'benefits' => ['Reduces anxiety without sedation', 'Enhances learning capacity', 'Modulates immune function', 'Stabilizes mood'],
                'testimonial' => 'Selank combined with my existing nootropic stack gives me all-day calm focus. No crashes, no anxiety — just clear thinking.',
                'testimonial_author' => 'Jennifer H., Age 29',
            ],

            // Sleep Optimization
            [
                'health_goal' => 'sleep',
                'experience_level' => 'beginner',
                'peptide_name' => 'DSIP',
                'peptide_slug' => 'dsip',
                'star_rating' => 4.5,
                'rating_label' => 'Good Match',
                'description' => 'DSIP (Delta Sleep-Inducing Peptide) naturally promotes deep, restorative delta wave sleep without the grogginess of traditional sleep aids.',
                'benefits' => ['Promotes deep delta wave sleep', 'Non-habit forming', 'Reduces stress hormones', 'Improves sleep onset time'],
                'testimonial' => 'For the first time in years, I am sleeping through the night and waking up refreshed. DSIP was a game-changer for my insomnia.',
                'testimonial_author' => 'Patricia N., Age 48',
            ],
            [
                'health_goal' => 'sleep',
                'experience_level' => 'advanced',
                'peptide_name' => 'CJC-1295 / Ipamorelin',
                'peptide_slug' => 'cjc-1295-ipamorelin',
                'star_rating' => 4.6,
                'rating_label' => 'Strong Match',
                'description' => 'The CJC-1295/Ipamorelin stack is known for dramatically improving deep sleep quality through enhanced natural growth hormone pulses during the night.',
                'benefits' => ['Enhances GH pulse during sleep', 'Deeper REM and delta sleep', 'Improved recovery during sleep', 'Better next-day energy'],
                'testimonial' => 'Adding this to my evening protocol transformed my sleep quality. I track my sleep and my deep sleep went from 45 min to 2+ hours.',
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
                'peptide_name' => 'BPC-157',
                'peptide_slug' => 'bpc-157',
                'star_rating' => 4.7,
                'rating_label' => 'Strong Match',
                'description' => 'BPC-157 is the most versatile peptide for general wellness, offering systemic protective and healing effects across multiple body systems.',
                'benefits' => ['Systemic healing effects', 'Gut and joint protection', 'Anti-inflammatory', 'Excellent safety profile'],
                'testimonial' => 'BPC-157 was my first peptide and I could not be happier. Better digestion, less joint pain, and overall feeling of wellness.',
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
            'kisspeptin' => 'kisspeptin-10', // ResultsBank uses short slug
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
