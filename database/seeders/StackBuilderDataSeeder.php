<?php

namespace Database\Seeders;

use App\Models\StackBundle;
use App\Models\StackBundleItem;
use App\Models\StackGoal;
use App\Models\StackProduct;
use App\Models\StackStore;
use Illuminate\Database\Seeder;

class StackBuilderDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Goals ──────────────────────────────────────────────
        $goals = [
            [
                'name' => 'Fat Loss',
                'slug' => 'fat-loss',
                'description' => 'Burn fat faster with targeted peptide protocols designed for maximum fat oxidation and metabolic support.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>',
                'color' => '#8B5CF6',
                'order' => 1,
            ],
            [
                'name' => 'Muscle Growth',
                'slug' => 'muscle-growth',
                'description' => 'Build lean muscle mass with growth-promoting peptides that enhance protein synthesis and recovery.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>',
                'color' => '#F97316',
                'order' => 2,
            ],
            [
                'name' => 'Anti-Aging',
                'slug' => 'anti-aging',
                'description' => 'Turn back the clock with regenerative peptides that promote cellular repair, skin health, and longevity.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'color' => '#14B8A6',
                'order' => 3,
            ],
            [
                'name' => 'Recovery',
                'slug' => 'recovery',
                'description' => 'Recover faster from training and injuries with healing peptides that reduce inflammation and repair tissue.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>',
                'color' => '#3B82F6',
                'order' => 4,
            ],
            [
                'name' => 'Sleep',
                'slug' => 'sleep',
                'description' => 'Improve sleep quality and duration naturally with peptides that regulate your circadian rhythm.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>',
                'color' => '#1E3A5F',
                'order' => 5,
            ],
            [
                'name' => 'Immune Health',
                'slug' => 'immune-health',
                'description' => 'Strengthen your immune system with proven peptides that enhance your body\'s natural defense mechanisms.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>',
                'color' => '#22C55E',
                'order' => 6,
            ],
            [
                'name' => 'Cognitive',
                'slug' => 'cognitive',
                'description' => 'Enhance focus, memory, and mental clarity with nootropic peptides that support brain health.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>',
                'color' => '#A855F7',
                'order' => 7,
            ],
            [
                'name' => 'Sexual Health',
                'slug' => 'sexual-health',
                'description' => 'Optimize hormonal health, libido, and vitality with peptides designed for sexual wellness.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>',
                'color' => '#EC4899',
                'order' => 8,
            ],
        ];

        $goalModels = [];
        foreach ($goals as $goal) {
            $slug = $goal['slug'];
            $goalModels[$slug] = StackGoal::updateOrCreate(
                ['slug' => $slug],
                array_merge($goal, ['is_active' => true])
            );
        }

        // ── Products ───────────────────────────────────────────
        $products = [
            [
                'name' => 'Semaglutide',
                'slug' => 'semaglutide',
                'subtitle' => 'GLP-1 Receptor Agonist',
                'description' => 'The gold standard for weight management. Semaglutide reduces appetite, slows gastric emptying, and promotes significant fat loss.',
                'price' => 89.99,
                'sale_price' => null,
                'dosing_info' => '0.25mg–2.4mg/week subcutaneous',
                'key_benefits' => ['Significant weight loss', 'Appetite suppression', 'Improved insulin sensitivity', 'Cardiovascular benefits'],
                'is_featured' => true,
                'order' => 1,
                'goals' => ['fat-loss'],
            ],
            [
                'name' => 'AOD-9604',
                'slug' => 'aod-9604',
                'subtitle' => 'Anti-Obesity Drug Fragment',
                'description' => 'A modified fragment of human growth hormone that stimulates fat burning without the growth-promoting effects.',
                'price' => 34.99,
                'sale_price' => null,
                'dosing_info' => '300mcg/day subcutaneous',
                'key_benefits' => ['Targeted fat reduction', 'No effect on blood sugar', 'Stimulates lipolysis', 'Well-tolerated'],
                'is_featured' => false,
                'order' => 2,
                'goals' => ['fat-loss'],
            ],
            [
                'name' => 'Tesamorelin',
                'slug' => 'tesamorelin',
                'subtitle' => 'Growth Hormone Releasing Hormone',
                'description' => 'FDA-approved peptide that specifically targets visceral adipose tissue, reducing belly fat while boosting GH.',
                'price' => 54.99,
                'sale_price' => 49.99,
                'dosing_info' => '2mg/day subcutaneous',
                'key_benefits' => ['Reduces visceral fat', 'FDA-approved', 'Increases IGF-1', 'Improves body composition'],
                'is_featured' => false,
                'order' => 3,
                'goals' => ['fat-loss', 'anti-aging'],
            ],
            [
                'name' => 'CJC-1295 / Ipamorelin',
                'slug' => 'cjc-1295-ipamorelin',
                'subtitle' => 'Growth Hormone Secretagogue Blend',
                'description' => 'The most popular GH-releasing peptide stack. Synergistic combination for sustained growth hormone elevation.',
                'price' => 59.99,
                'sale_price' => null,
                'dosing_info' => '100mcg/100mcg before bed subcutaneous',
                'key_benefits' => ['Increases growth hormone', 'Enhanced fat metabolism', 'Improved sleep quality', 'Muscle recovery'],
                'is_featured' => true,
                'order' => 4,
                'goals' => ['fat-loss', 'muscle-growth', 'anti-aging', 'sleep'],
            ],
            [
                'name' => 'BPC-157',
                'slug' => 'bpc-157',
                'subtitle' => 'Body Protection Compound',
                'description' => 'A powerful healing peptide derived from gastric juice. Accelerates recovery of muscles, tendons, ligaments, and the gut.',
                'price' => 39.99,
                'sale_price' => null,
                'dosing_info' => '250–500mcg/day subcutaneous',
                'key_benefits' => ['Accelerated tissue repair', 'Gut healing', 'Reduced inflammation', 'Tendon & ligament recovery'],
                'is_featured' => true,
                'order' => 5,
                'goals' => ['recovery'],
            ],
            [
                'name' => 'TB-500',
                'slug' => 'tb-500',
                'subtitle' => 'Thymosin Beta-4',
                'description' => 'A naturally occurring peptide that promotes healing, reduces inflammation, and supports new blood vessel formation.',
                'price' => 44.99,
                'sale_price' => null,
                'dosing_info' => '2.5–5mg twice/week subcutaneous',
                'key_benefits' => ['Wound healing', 'Reduces inflammation', 'Promotes angiogenesis', 'Hair regrowth potential'],
                'is_featured' => false,
                'order' => 6,
                'goals' => ['recovery'],
            ],
            [
                'name' => 'MK-677 (Ibutamoren)',
                'slug' => 'mk-677',
                'subtitle' => 'Growth Hormone Secretagogue',
                'description' => 'An oral GH secretagogue that increases growth hormone and IGF-1 levels. Excellent for muscle growth and sleep.',
                'price' => 54.99,
                'sale_price' => null,
                'dosing_info' => '10–25mg/day oral',
                'key_benefits' => ['Oral administration', 'Increases GH & IGF-1', 'Deep sleep enhancement', 'Muscle mass gains'],
                'is_featured' => false,
                'order' => 7,
                'goals' => ['muscle-growth', 'sleep'],
            ],
            [
                'name' => 'Sermorelin',
                'slug' => 'sermorelin',
                'subtitle' => 'Growth Hormone Releasing Hormone',
                'description' => 'A bioidentical GHRH analog that stimulates your pituitary to produce more natural growth hormone.',
                'price' => 39.99,
                'sale_price' => null,
                'dosing_info' => '200–300mcg before bed subcutaneous',
                'key_benefits' => ['Natural GH stimulation', 'Anti-aging effects', 'Improved sleep', 'FDA history of use'],
                'is_featured' => false,
                'order' => 8,
                'goals' => ['anti-aging', 'muscle-growth'],
            ],
            [
                'name' => 'GHK-Cu',
                'slug' => 'ghk-cu',
                'subtitle' => 'Copper Peptide Complex',
                'description' => 'A naturally occurring copper peptide with powerful anti-aging, skin-remodeling, and wound-healing properties.',
                'price' => 34.99,
                'sale_price' => null,
                'dosing_info' => '1–2mg/day subcutaneous or topical',
                'key_benefits' => ['Skin rejuvenation', 'Collagen synthesis', 'Hair growth support', 'Antioxidant effects'],
                'is_featured' => false,
                'order' => 9,
                'goals' => ['anti-aging'],
            ],
            [
                'name' => 'Epithalon',
                'slug' => 'epithalon',
                'subtitle' => 'Telomerase Activator',
                'description' => 'A tetrapeptide that activates telomerase, the enzyme responsible for maintaining telomere length — a key marker of biological aging.',
                'price' => 39.99,
                'sale_price' => null,
                'dosing_info' => '5–10mg/day for 10–20 days subcutaneous',
                'key_benefits' => ['Telomere lengthening', 'Cellular rejuvenation', 'Improved sleep cycles', 'Antioxidant properties'],
                'is_featured' => false,
                'order' => 10,
                'goals' => ['anti-aging'],
            ],
            [
                'name' => 'Thymosin Alpha-1',
                'slug' => 'thymosin-alpha-1',
                'subtitle' => 'Immune Modulator',
                'description' => 'A powerful immune-regulating peptide that enhances T-cell function and modulates the immune response.',
                'price' => 49.99,
                'sale_price' => null,
                'dosing_info' => '1.6mg twice/week subcutaneous',
                'key_benefits' => ['Enhanced T-cell immunity', 'Antiviral properties', 'FDA-approved globally', 'Autoimmune modulation'],
                'is_featured' => true,
                'order' => 11,
                'goals' => ['immune-health'],
            ],
            [
                'name' => 'DSIP',
                'slug' => 'dsip',
                'subtitle' => 'Delta Sleep-Inducing Peptide',
                'description' => 'A neuropeptide that promotes deep, restorative delta-wave sleep without the hangover effects of traditional sleep aids.',
                'price' => 29.99,
                'sale_price' => null,
                'dosing_info' => '100–200mcg before bed subcutaneous',
                'key_benefits' => ['Deep delta-wave sleep', 'No morning grogginess', 'Stress reduction', 'Natural sleep architecture'],
                'is_featured' => false,
                'order' => 12,
                'goals' => ['sleep'],
            ],
            [
                'name' => 'Selank',
                'slug' => 'selank',
                'subtitle' => 'Nootropic Anxiolytic Peptide',
                'description' => 'A synthetic peptide derived from tuftsin with potent anxiolytic and nootropic effects. Enhances focus without sedation.',
                'price' => 34.99,
                'sale_price' => null,
                'dosing_info' => '250–500mcg/day intranasal',
                'key_benefits' => ['Reduced anxiety', 'Enhanced memory', 'Improved focus', 'Immune modulation'],
                'is_featured' => false,
                'order' => 13,
                'goals' => ['cognitive'],
            ],
            [
                'name' => 'Semax',
                'slug' => 'semax',
                'subtitle' => 'Neuroprotective Nootropic',
                'description' => 'A synthetic peptide analog of ACTH with powerful neuroprotective and cognitive-enhancing properties.',
                'price' => 39.99,
                'sale_price' => null,
                'dosing_info' => '200–600mcg/day intranasal',
                'key_benefits' => ['Neuroprotection', 'Enhanced learning', 'BDNF increase', 'Improved attention'],
                'is_featured' => false,
                'order' => 14,
                'goals' => ['cognitive'],
            ],
            [
                'name' => 'PT-141',
                'slug' => 'pt-141',
                'subtitle' => 'Bremelanotide',
                'description' => 'FDA-approved peptide for sexual dysfunction. Works through the central nervous system to enhance arousal and desire.',
                'price' => 44.99,
                'sale_price' => null,
                'dosing_info' => '1.75mg as needed subcutaneous',
                'key_benefits' => ['FDA-approved', 'Enhanced libido', 'Works for both sexes', 'CNS mechanism of action'],
                'is_featured' => true,
                'order' => 15,
                'goals' => ['sexual-health'],
            ],
            [
                'name' => 'Kisspeptin-10',
                'slug' => 'kisspeptin-10',
                'subtitle' => 'Reproductive Hormone Regulator',
                'description' => 'A neuropeptide that stimulates GnRH release, supporting healthy testosterone and reproductive function.',
                'price' => 42.99,
                'sale_price' => null,
                'dosing_info' => '100–200mcg/day subcutaneous',
                'key_benefits' => ['Testosterone support', 'Reproductive health', 'Natural hormone signaling', 'LH/FSH stimulation'],
                'is_featured' => false,
                'order' => 16,
                'goals' => ['sexual-health'],
            ],
            [
                'name' => 'Tirzepatide',
                'slug' => 'tirzepatide',
                'subtitle' => 'Dual GIP/GLP-1 Receptor Agonist',
                'description' => 'A dual GIP/GLP-1 receptor agonist that has shown remarkable results for weight management by reducing appetite and improving metabolic function.',
                'price' => 149.99,
                'sale_price' => null,
                'dosing_info' => '2.5mg–15mg/week subcutaneous',
                'key_benefits' => ['Significant weight loss', 'Dual receptor action', 'Improved insulin sensitivity', 'Reduced appetite'],
                'is_featured' => true,
                'order' => 17,
                'goals' => ['fat-loss'],
            ],
            [
                'name' => 'LL-37',
                'slug' => 'll-37',
                'subtitle' => 'Antimicrobial Peptide',
                'description' => 'A human cathelicidin antimicrobial peptide with broad-spectrum antimicrobial, anti-biofilm, and immunomodulatory properties.',
                'price' => 54.99,
                'sale_price' => null,
                'dosing_info' => '50–100mcg/day subcutaneous',
                'key_benefits' => ['Broad-spectrum antimicrobial', 'Biofilm disruption', 'Immune modulation', 'Mucosal immunity support'],
                'is_featured' => false,
                'order' => 18,
                'goals' => ['immune-health'],
            ],
            [
                'name' => 'Larazotide',
                'slug' => 'larazotide',
                'subtitle' => 'Tight Junction Regulator',
                'description' => 'Larazotide acetate is a tight junction regulator that helps seal intestinal permeability, ideal for advanced gut healing protocols.',
                'price' => 59.99,
                'sale_price' => null,
                'dosing_info' => '0.5mg three times daily oral',
                'key_benefits' => ['Seals tight junctions', 'Reduces intestinal permeability', 'Targets leaky gut', 'Well-tolerated'],
                'is_featured' => false,
                'order' => 19,
                'goals' => ['recovery'],
            ],
        ];

        $productModels = [];
        foreach ($products as $productData) {
            $goalSlugs = $productData['goals'];
            unset($productData['goals']);

            $product = StackProduct::updateOrCreate(
                ['slug' => $productData['slug']],
                array_merge($productData, ['is_active' => true])
            );
            $productModels[$product->slug] = $product;

            // Sync goals
            $goalIds = [];
            foreach ($goalSlugs as $i => $slug) {
                if (isset($goalModels[$slug])) {
                    $goalIds[$goalModels[$slug]->id] = ['order' => $i];
                }
            }
            $product->goals()->sync($goalIds);
        }

        // ── Stores ────────────────────────────────────────────
        $store1 = StackStore::updateOrCreate(
            ['slug' => 'peptidesciences'],
            [
                'name' => 'PeptideSciences',
                'website_url' => 'https://www.peptidesciences.com',
                'description' => 'Premium research peptides with 99%+ purity. Third-party tested with COA for every batch.',
                'is_active' => true,
                'order' => 1,
            ]
        );

        $store2 = StackStore::updateOrCreate(
            ['slug' => 'swisschems'],
            [
                'name' => 'SwissChems',
                'website_url' => 'https://www.swisschems.is',
                'description' => 'High-quality research compounds with worldwide shipping. Known for competitive pricing.',
                'is_active' => true,
                'order' => 2,
            ]
        );

        // Mark PeptideSciences as globally recommended
        $store1->update(['is_recommended' => true]);

        // Attach products to stores with varied pricing
        $storePricing = [
            'semaglutide'          => ['store1' => 89.99, 'store2' => 94.99],
            'aod-9604'             => ['store1' => 34.99, 'store2' => 31.99],
            'tesamorelin'          => ['store1' => 54.99, 'store2' => 57.99],
            'cjc-1295-ipamorelin'  => ['store1' => 59.99, 'store2' => 56.99],
            'bpc-157'              => ['store1' => 39.99, 'store2' => 42.99],
            'tb-500'               => ['store1' => 44.99, 'store2' => 41.99],
            'mk-677'               => ['store1' => 54.99, 'store2' => 49.99],
            'sermorelin'           => ['store1' => 39.99, 'store2' => 43.99],
            'ghk-cu'               => ['store1' => 34.99, 'store2' => 32.99],
            'epithalon'            => ['store1' => 39.99, 'store2' => 44.99],
            'thymosin-alpha-1'     => ['store1' => 49.99, 'store2' => 52.99],
            'dsip'                 => ['store1' => 29.99, 'store2' => 27.99],
            'selank'               => ['store1' => 34.99, 'store2' => 36.99],
            'semax'                => ['store1' => 39.99, 'store2' => 37.99],
            'pt-141'               => ['store1' => 44.99, 'store2' => 46.99],
            'kisspeptin-10'        => ['store1' => 42.99, 'store2' => 39.99],
            'tirzepatide'          => ['store1' => 149.99, 'store2' => 154.99],
            'll-37'                => ['store1' => 54.99, 'store2' => 52.99],
            'larazotide'           => ['store1' => 59.99, 'store2' => 62.99],
        ];

        // Per-product recommendation overrides for SwissChems (cheaper products get recommended at that store)
        $swisschemsRecommended = ['aod-9604', 'cjc-1295-ipamorelin', 'tb-500', 'mk-677', 'dsip', 'kisspeptin-10', 'll-37'];

        foreach ($storePricing as $slug => $prices) {
            if (!isset($productModels[$slug])) continue;
            $product = $productModels[$slug];
            $product->stores()->syncWithoutDetaching([
                $store1->id => ['price' => $prices['store1'], 'is_in_stock' => true, 'is_recommended' => null],
                $store2->id => [
                    'price' => $prices['store2'],
                    'is_in_stock' => true,
                    'is_recommended' => in_array($slug, $swisschemsRecommended) ? true : null,
                ],
            ]);
        }

        // ── Bundles ────────────────────────────────────────────
        $bundles = [
            [
                'name' => 'Fat Loss Stack',
                'slug' => 'fat-loss-stack',
                'description' => 'Our most popular fat loss combination. Semaglutide for appetite control, Tesamorelin for visceral fat, and AOD-9604 for targeted lipolysis.',
                'goal' => 'fat-loss',
                'is_professor_pick' => true,
                'items' => [
                    ['slug' => 'semaglutide', 'qty' => 1],
                    ['slug' => 'tesamorelin', 'qty' => 1],
                    ['slug' => 'aod-9604', 'qty' => 1],
                ],
                'bundle_price' => 149.99,
                'order' => 1,
            ],
            [
                'name' => 'Immune Health Stack',
                'slug' => 'immune-health-stack',
                'description' => 'Comprehensive immune support combining Thymosin Alpha-1 for T-cell modulation with BPC-157 for gut immune barrier repair.',
                'goal' => 'immune-health',
                'is_professor_pick' => false,
                'items' => [
                    ['slug' => 'thymosin-alpha-1', 'qty' => 1],
                    ['slug' => 'bpc-157', 'qty' => 1],
                ],
                'bundle_price' => 79.99,
                'order' => 2,
            ],
            [
                'name' => 'Recovery Stack',
                'slug' => 'recovery-stack',
                'description' => 'The ultimate healing combination. BPC-157 and TB-500 work synergistically to accelerate tissue repair and reduce inflammation.',
                'goal' => 'recovery',
                'is_professor_pick' => true,
                'items' => [
                    ['slug' => 'bpc-157', 'qty' => 1],
                    ['slug' => 'tb-500', 'qty' => 1],
                ],
                'bundle_price' => 74.99,
                'order' => 3,
            ],
            [
                'name' => 'Sleeping Stack',
                'slug' => 'sleeping-stack',
                'description' => 'Deep, restorative sleep without grogginess. DSIP induces delta-wave sleep while CJC/Ipa boosts nighttime GH release.',
                'goal' => 'sleep',
                'is_professor_pick' => false,
                'items' => [
                    ['slug' => 'dsip', 'qty' => 1],
                    ['slug' => 'cjc-1295-ipamorelin', 'qty' => 1],
                ],
                'bundle_price' => 79.99,
                'order' => 4,
            ],
            [
                'name' => 'Muscle Building Stack',
                'slug' => 'muscle-building-stack',
                'description' => 'Maximize muscle growth with this powerful combination of GH secretagogues and recovery peptides.',
                'goal' => 'muscle-growth',
                'is_professor_pick' => true,
                'items' => [
                    ['slug' => 'cjc-1295-ipamorelin', 'qty' => 1],
                    ['slug' => 'mk-677', 'qty' => 1],
                    ['slug' => 'bpc-157', 'qty' => 1],
                ],
                'bundle_price' => 129.99,
                'order' => 5,
            ],
            [
                'name' => 'Anti-Aging Stack',
                'slug' => 'anti-aging-stack',
                'description' => 'Turn back the biological clock. Epithalon for telomere health, GHK-Cu for skin rejuvenation, and Sermorelin for GH optimization.',
                'goal' => 'anti-aging',
                'is_professor_pick' => true,
                'items' => [
                    ['slug' => 'epithalon', 'qty' => 1],
                    ['slug' => 'ghk-cu', 'qty' => 1],
                    ['slug' => 'sermorelin', 'qty' => 1],
                ],
                'bundle_price' => 99.99,
                'order' => 6,
            ],
            [
                'name' => 'Cognitive Boost Stack',
                'slug' => 'cognitive-boost-stack',
                'description' => 'Unlock peak mental performance. Selank reduces anxiety while Semax enhances focus, memory, and neuroprotection.',
                'goal' => 'cognitive',
                'is_professor_pick' => false,
                'items' => [
                    ['slug' => 'selank', 'qty' => 1],
                    ['slug' => 'semax', 'qty' => 1],
                ],
                'bundle_price' => 64.99,
                'order' => 7,
            ],
            [
                'name' => 'Simple Immune-Support Stack',
                'slug' => 'simple-immune-support-stack',
                'description' => 'A streamlined immune booster featuring Thymosin Alpha-1 — the gold standard for immune enhancement.',
                'goal' => 'immune-health',
                'is_professor_pick' => false,
                'items' => [
                    ['slug' => 'thymosin-alpha-1', 'qty' => 2],
                ],
                'bundle_price' => 89.99,
                'order' => 8,
            ],
            [
                'name' => 'Sexual Wellness Stack',
                'slug' => 'sexual-wellness-stack',
                'description' => 'Comprehensive sexual health support combining PT-141 for arousal with Kisspeptin-10 for hormonal optimization.',
                'goal' => 'sexual-health',
                'is_professor_pick' => true,
                'items' => [
                    ['slug' => 'pt-141', 'qty' => 1],
                    ['slug' => 'kisspeptin-10', 'qty' => 1],
                ],
                'bundle_price' => 77.99,
                'order' => 9,
            ],
            [
                'name' => 'Complete Recovery Stack',
                'slug' => 'complete-recovery-stack',
                'description' => 'Maximum recovery combining healing peptides with growth hormone support for faster and more complete tissue repair.',
                'goal' => 'recovery',
                'is_professor_pick' => false,
                'items' => [
                    ['slug' => 'bpc-157', 'qty' => 2],
                    ['slug' => 'tb-500', 'qty' => 1],
                    ['slug' => 'cjc-1295-ipamorelin', 'qty' => 1],
                ],
                'bundle_price' => 159.99,
                'order' => 10,
            ],
        ];

        foreach ($bundles as $bundleData) {
            $items = $bundleData['items'];
            $goalSlug = $bundleData['goal'];
            unset($bundleData['items'], $bundleData['goal']);

            $bundle = StackBundle::updateOrCreate(
                ['slug' => $bundleData['slug']],
                array_merge($bundleData, [
                    'stack_goal_id' => $goalModels[$goalSlug]->id,
                    'is_active' => true,
                ])
            );

            // Recreate bundle items
            $bundle->items()->delete();
            foreach ($items as $i => $item) {
                if (isset($productModels[$item['slug']])) {
                    StackBundleItem::create([
                        'stack_bundle_id' => $bundle->id,
                        'stack_product_id' => $productModels[$item['slug']]->id,
                        'quantity' => $item['qty'],
                        'order' => $i,
                    ]);
                }
            }
        }

        // ── Bundle Store Pricing ─────────────────────────────────
        $bundleStorePricing = [
            'fat-loss-stack'            => ['store1' => 149.99, 'store2' => 155.99],
            'immune-health-stack'       => ['store1' => 79.99,  'store2' => 82.99],
            'recovery-stack'            => ['store1' => 74.99,  'store2' => 71.99],
            'sleeping-stack'            => ['store1' => 79.99,  'store2' => 76.99],
            'muscle-building-stack'     => ['store1' => 129.99, 'store2' => 124.99],
            'anti-aging-stack'          => ['store1' => 99.99,  'store2' => 104.99],
            'cognitive-boost-stack'     => ['store1' => 64.99,  'store2' => 62.99],
            'simple-immune-support-stack' => ['store1' => 89.99, 'store2' => 94.99],
            'sexual-wellness-stack'     => ['store1' => 77.99,  'store2' => 74.99],
            'complete-recovery-stack'   => ['store1' => 159.99, 'store2' => 152.99],
        ];

        foreach ($bundleStorePricing as $slug => $prices) {
            $bundle = StackBundle::where('slug', $slug)->first();
            if (!$bundle) continue;
            $bundle->stores()->syncWithoutDetaching([
                $store1->id => ['price' => $prices['store1'], 'is_in_stock' => true, 'is_recommended' => null],
                $store2->id => ['price' => $prices['store2'], 'is_in_stock' => true, 'is_recommended' => null],
            ]);
        }
    }
}
