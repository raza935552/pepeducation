<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Peptide;
use Illuminate\Database\Seeder;

class PeptideSeeder extends Seeder
{
    public function run(): void
    {
        $peptides = [
            [
                'name' => 'BPC-157',
                'full_name' => 'Body Protection Compound-157',
                'abbreviation' => 'BPC',
                'type' => 'Pentadecapeptide',
                'typical_dose' => '250-500 mcg',
                'dose_frequency' => '1-2x daily',
                'route' => 'Subcutaneous',
                'injection_sites' => ['Belly', 'Thigh', 'Near injury site'],
                'cycle' => '4-12 weeks',
                'storage' => '2-6°C refrigerated',
                'research_status' => 'extensive',
                'is_published' => true,
                'overview' => 'BPC-157 is a synthetic peptide derived from a protein found in human gastric juice. It has been studied extensively for its potential healing and protective properties.',
                'key_benefits' => [
                    'Accelerates wound and tissue healing',
                    'Supports gut health and integrity',
                    'May protect against NSAID-induced damage',
                    'Potential tendon and ligament repair support',
                ],
                'mechanism_of_action' => 'BPC-157 is believed to work through multiple pathways including promoting angiogenesis, modulating nitric oxide synthesis, and interacting with the dopamine system.',
                'what_to_expect' => [
                    'Week 1-2: Minimal noticeable effects',
                    'Week 2-4: Initial healing response may begin',
                    'Week 4-8: Noticeable improvement in injuries',
                ],
                'safety_warnings' => [
                    'May cause mild injection site reactions',
                    'Not recommended during pregnancy',
                    'WADA prohibited substance',
                ],
                'molecular_weight' => 1419.53,
                'amino_acid_length' => 15,
                'peak_time' => '1 hour',
                'half_life' => '4 hours',
                'clearance_time' => '~20 hours',
                'categories' => ['Wound Healing', 'Gastrointestinal', 'Tissue Repair', 'Joint Health'],
            ],
            [
                'name' => 'Semaglutide',
                'full_name' => 'Semaglutide',
                'abbreviation' => 'SEMA',
                'type' => 'GLP-1 Receptor Agonist',
                'typical_dose' => '0.25-2.4 mg',
                'dose_frequency' => 'Once weekly',
                'route' => 'Subcutaneous',
                'injection_sites' => ['Belly', 'Thigh', 'Upper arm'],
                'cycle' => 'Ongoing as prescribed',
                'storage' => '2-8°C refrigerated',
                'research_status' => 'extensive',
                'is_published' => true,
                'overview' => 'Semaglutide is a GLP-1 receptor agonist approved for type 2 diabetes and chronic weight management. It mimics the incretin hormone GLP-1.',
                'key_benefits' => [
                    'Significant weight loss support',
                    'Improved blood sugar control',
                    'Reduced appetite and food cravings',
                    'Cardiovascular benefits',
                ],
                'mechanism_of_action' => 'Semaglutide mimics GLP-1, enhancing insulin secretion, suppressing glucagon, slowing gastric emptying, and reducing appetite through central nervous system effects.',
                'what_to_expect' => [
                    'Week 1-4: Appetite reduction begins',
                    'Month 2-3: Noticeable weight changes',
                    'Month 4-6: Significant results typically seen',
                ],
                'safety_warnings' => [
                    'Nausea and GI side effects common initially',
                    'Risk of pancreatitis',
                    'Not for those with MEN 2 or medullary thyroid carcinoma history',
                ],
                'molecular_weight' => 4113.58,
                'amino_acid_length' => 31,
                'peak_time' => '1-3 days',
                'half_life' => '~1 week',
                'clearance_time' => '5-7 weeks',
                'categories' => ['Weight Loss', 'Diabetes', 'Metabolism', 'Heart Health'],
            ],
            [
                'name' => 'TB-500',
                'full_name' => 'Thymosin Beta-4',
                'abbreviation' => 'TB4',
                'type' => 'Thymosin',
                'typical_dose' => '2-5 mg',
                'dose_frequency' => '2x weekly',
                'route' => 'Subcutaneous or Intramuscular',
                'injection_sites' => ['Belly', 'Deltoid', 'Near injury'],
                'cycle' => '4-6 weeks',
                'storage' => '2-8°C refrigerated',
                'research_status' => 'well',
                'is_published' => true,
                'overview' => 'TB-500 is a synthetic version of Thymosin Beta-4, a naturally occurring peptide involved in tissue repair, cell migration, and wound healing.',
                'key_benefits' => [
                    'Promotes tissue repair and regeneration',
                    'Supports muscle recovery',
                    'May reduce inflammation',
                    'Enhances flexibility and reduces injury risk',
                ],
                'mechanism_of_action' => 'TB-500 promotes cell migration, blood vessel formation, and reduces inflammation through actin sequestration and regulation of cell-building proteins.',
                'what_to_expect' => [
                    'Week 1-2: Subtle improvements in mobility',
                    'Week 3-4: Enhanced healing noticeable',
                    'Week 5-6: Significant recovery improvements',
                ],
                'safety_warnings' => [
                    'Limited human clinical data',
                    'Not for use in cancer patients',
                    'WADA prohibited substance',
                ],
                'molecular_weight' => 4963.50,
                'amino_acid_length' => 43,
                'peak_time' => '2-3 hours',
                'half_life' => '~4-6 hours',
                'clearance_time' => '~24 hours',
                'categories' => ['Tissue Repair', 'Athletic Recovery', 'Wound Healing'],
            ],
            [
                'name' => 'GHK-Cu',
                'full_name' => 'Copper Peptide GHK-Cu',
                'abbreviation' => 'GHK',
                'type' => 'Tripeptide',
                'typical_dose' => '1-2 mg',
                'dose_frequency' => 'Daily',
                'route' => 'Subcutaneous or Topical',
                'injection_sites' => ['Belly', 'Thigh'],
                'cycle' => '4-8 weeks',
                'storage' => '2-8°C refrigerated',
                'research_status' => 'well',
                'is_published' => true,
                'overview' => 'GHK-Cu is a naturally occurring copper peptide found in human plasma. It has regenerative and protective properties, particularly for skin and connective tissue.',
                'key_benefits' => [
                    'Promotes collagen and elastin synthesis',
                    'Supports wound healing',
                    'Anti-aging skin effects',
                    'Hair growth stimulation',
                ],
                'mechanism_of_action' => 'GHK-Cu attracts immune cells, stimulates collagen synthesis, promotes angiogenesis, and has antioxidant properties through copper ion delivery.',
                'what_to_expect' => [
                    'Week 2-4: Improved skin texture',
                    'Week 4-8: Visible anti-aging effects',
                    'Ongoing: Continued improvement',
                ],
                'safety_warnings' => [
                    'Generally well-tolerated',
                    'May cause injection site irritation',
                    'Avoid with copper sensitivity',
                ],
                'molecular_weight' => 403.88,
                'amino_acid_length' => 3,
                'peak_time' => '1-2 hours',
                'half_life' => '~2-4 hours',
                'clearance_time' => '~12 hours',
                'categories' => ['Anti-Aging', 'Skin & Beauty', 'Hair Growth', 'Wound Healing'],
            ],
            [
                'name' => 'Selank',
                'full_name' => 'Selank',
                'abbreviation' => 'SEL',
                'type' => 'Heptapeptide',
                'typical_dose' => '250-500 mcg',
                'dose_frequency' => '1-3x daily',
                'route' => 'Intranasal or Subcutaneous',
                'injection_sites' => ['Intranasal spray', 'Subcutaneous'],
                'cycle' => '2-4 weeks',
                'storage' => '2-8°C refrigerated',
                'research_status' => 'well',
                'is_published' => true,
                'overview' => 'Selank is a synthetic peptide developed in Russia, derived from tuftsin. It is used for its anxiolytic and nootropic effects.',
                'key_benefits' => [
                    'Reduces anxiety without sedation',
                    'Enhances cognitive function',
                    'Improves memory and learning',
                    'Supports immune function',
                ],
                'mechanism_of_action' => 'Selank modulates the expression of BDNF, affects serotonin metabolism, and interacts with GABA receptors to produce anxiolytic and cognitive effects.',
                'what_to_expect' => [
                    'Day 1-3: Subtle calming effects',
                    'Week 1-2: Noticeable anxiety reduction',
                    'Week 2-4: Cognitive improvements',
                ],
                'safety_warnings' => [
                    'Generally well-tolerated',
                    'Limited long-term safety data',
                    'May interact with other anxiolytics',
                ],
                'molecular_weight' => 751.87,
                'amino_acid_length' => 7,
                'peak_time' => '15-30 minutes',
                'half_life' => '~1-2 hours',
                'clearance_time' => '~6 hours',
                'categories' => ['Anxiety Relief', 'Cognitive Enhancement', 'Neuroprotection'],
            ],
        ];

        foreach ($peptides as $peptideData) {
            $categories = $peptideData['categories'];
            unset($peptideData['categories']);

            $peptide = Peptide::updateOrCreate(
                ['slug' => \Str::slug($peptideData['name'])],
                $peptideData
            );

            // Sync categories
            $categoryIds = Category::whereIn('name', $categories)->pluck('id');
            $peptide->categories()->sync($categoryIds);
        }
    }
}
