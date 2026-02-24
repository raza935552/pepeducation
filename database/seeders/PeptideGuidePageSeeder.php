<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PeptideGuidePageSeeder extends Seeder
{
    public function run(): void
    {
        $content = $this->getGrapesJsContent();
        $html = $this->renderHtml();
        $css = $this->renderCss();

        Page::updateOrCreate(
            ['slug' => 'peptides-for-dummies'],
            [
                'title' => 'Peptides For Dummies (2026)',
                'slug' => 'peptides-for-dummies',
                'content' => $content,
                'html' => $html,
                'css' => $css,
                'meta_title' => 'Peptides For Dummies (2026) — The Complete Beginner Guide | Professor Peptides',
                'meta_description' => 'Everything you need to know about peptides in one place. Tier rankings, dosing, safety, myths busted, and how to get started. Free guide by Professor Peptides.',
                'template' => 'landing',
                'status' => 'published',
                'published_at' => now(),
            ]
        );
    }

    private function getGrapesJsContent(): array
    {
        return [
            'components' => [
                [
                    'type' => 'wrapper',
                    'components' => [
                        $this->heroSection(),
                        $this->tierListSection(),
                        $this->tocSection(),
                        $this->beforeTakingSection(),
                        $this->whatArePeptidesSection(),
                        $this->ageDeclineSection(),
                        $this->howTheyHelpSection(),
                        $this->findYourPeptideCtaSection(),
                        $this->combineSection(),
                        $this->cyclingSection(),
                        $this->dosingCtaSection(),
                        $this->mythsSection(),
                        $this->howToGetSection(),
                        $this->comparisonSection(),
                        $this->grayMarketSection(),
                        $this->vendorCtaSection(),
                        $this->groceryListSection(),
                        $this->disclaimerSection(),
                    ],
                ],
            ],
            'styles' => [],
        ];
    }

    // ── Section builders (GrapesJS component trees) ─────────────

    private function heroSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-hero'],
            'style' => ['padding' => '60px 20px 40px', 'background' => 'linear-gradient(135deg, #1a1714 0%, #2d2520 100%)', 'text-align' => 'center'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['color' => '#9A7B4F', 'font-size' => '14px', 'text-transform' => 'uppercase', 'letter-spacing' => '3px', 'margin-bottom' => '16px', 'font-weight' => '600'], 'content' => 'Professor Peptides Presents'],
                    ['tagName' => 'h1', 'type' => 'text', 'style' => ['font-size' => '48px', 'line-height' => '1.15', 'color' => '#f8f5f0', 'margin-bottom' => '16px', 'font-weight' => '800'], 'content' => 'Peptides For Dummies'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '20px', 'color' => '#C9A227', 'margin-bottom' => '24px', 'font-weight' => '500'], 'content' => 'The 2026 Edition'],
                    ['tagName' => 'div', 'style' => ['display' => 'flex', 'justify-content' => 'center', 'gap' => '20px', 'color' => '#a09080', 'font-size' => '14px', 'margin-bottom' => '30px', 'flex-wrap' => 'wrap'], 'components' => [
                        ['tagName' => 'span', 'type' => 'text', 'content' => 'By Professor Peptides'],
                        ['tagName' => 'span', 'type' => 'text', 'content' => 'Updated Feb 2026'],
                        ['tagName' => 'span', 'type' => 'text', 'content' => '15 min read'],
                    ]],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '17px', 'line-height' => '1.7', 'color' => '#d4c5b0', 'max-width' => '600px', 'margin' => '0 auto'], 'content' => 'Everything you need to know about peptides in one place. No PhD required. No sales pitch. Just the straight facts.'],
                ]],
            ],
        ];
    }

    private function tierListSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-tierlist'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '8px', 'text-align' => 'center'], 'content' => 'TL;DR — Peptide Tier List'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['color' => '#666', 'text-align' => 'center', 'margin-bottom' => '30px', 'font-size' => '16px'], 'content' => 'Ranked by community results, research depth, and real-world reliability.'],
                    ['tagName' => 'div', 'attributes' => ['class' => 'pfd-tier-table'], 'type' => 'text', 'content' => $this->tierTableHtml()],
                ]],
            ],
        ];
    }

    private function tocSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-toc'],
            'style' => ['padding' => '50px 20px', 'background-color' => '#ffffff'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '24px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '20px'], 'content' => 'Table of Contents'],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-toc-list'], 'content' => $this->tocHtml()],
                ]],
            ],
        ];
    }

    private function beforeTakingSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-before'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '16px'], 'content' => 'Before Taking Peptides'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '24px'], 'content' => "Peptides are powerful, but they're not a shortcut. They amplify what you're already doing right. If your foundation is broken, peptides won't fix it — they'll just amplify the mess. Get these right first:"],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-checklist'], 'content' => $this->checklistHtml()],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-top' => '24px', 'font-style' => 'italic'], 'content' => "You don't have to be perfect. But if you're sleeping 4 hours, living on fast food, and never moving your body — a peptide isn't the answer. Fix the basics first, then layer peptides on top."],
                ]],
            ],
        ];
    }

    private function whatArePeptidesSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-what'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#ffffff'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '16px'], 'content' => 'What Are Peptides?'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '16px'], 'content' => "A peptide is a short chain of amino acids — typically between 2 and 50. They're smaller than proteins and act as signaling molecules, telling your body to do specific things like repair tissue, burn fat, or produce growth hormone."],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '24px'], 'content' => "Your body already makes peptides naturally. When you take one, you're amplifying a signal your body already recognizes — that's why they tend to work with your biology instead of against it."],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-compare-table'], 'content' => $this->compareTableHtml()],
                ]],
            ],
        ];
    }

    private function ageDeclineSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-age'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '16px', 'text-align' => 'center'], 'content' => 'Why Your Body Needs Help'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '30px', 'text-align' => 'center'], 'content' => "As you age, your body makes fewer peptides naturally. Here's what that decline looks like:"],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-timeline'], 'content' => $this->ageTimelineHtml()],
                ]],
            ],
        ];
    }

    private function howTheyHelpSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-help'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#ffffff'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '8px', 'text-align' => 'center'], 'content' => 'How They Help You'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['color' => '#666', 'text-align' => 'center', 'margin-bottom' => '30px', 'font-size' => '16px'], 'content' => 'Peptides target specific pathways. Here are the 8 most common goals:'],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-categories-grid'], 'content' => $this->categoriesGridHtml()],
                ]],
            ],
        ];
    }

    private function findYourPeptideCtaSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-cta1'],
            'style' => ['padding' => '50px 20px', 'background' => 'linear-gradient(135deg, #1a1714 0%, #2d2520 100%)', 'text-align' => 'center'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '600px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h3', 'type' => 'text', 'style' => ['font-size' => '28px', 'font-weight' => '700', 'color' => '#f8f5f0', 'margin-bottom' => '12px'], 'content' => 'Find Your Peptide'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['color' => '#d4c5b0', 'margin-bottom' => '24px', 'font-size' => '16px'], 'content' => 'Take the PepQuiz to find the right peptide for your specific goal. 4 minutes. No account needed.'],
                    ['tagName' => 'a', 'type' => 'link', 'attributes' => ['href' => '/quiz/pepquiz'], 'style' => ['display' => 'inline-block', 'padding' => '16px 40px', 'background-color' => '#9A7B4F', 'color' => '#ffffff', 'font-weight' => '700', 'border-radius' => '8px', 'text-decoration' => 'none', 'font-size' => '17px'], 'content' => 'Take the PepQuiz'],
                ]],
            ],
        ];
    }

    private function combineSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-combine'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '16px'], 'content' => 'Can You Combine Them?'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '24px'], 'content' => "Yes — and most experienced users do. But there's a right way and a wrong way. Here are the three main approaches:"],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-methods-table'], 'content' => $this->methodsTableHtml()],
                ]],
            ],
        ];
    }

    private function cyclingSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-cycling'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#ffffff'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '16px'], 'content' => 'How Long to Run It'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '24px'], 'content' => "Peptides aren't meant to be taken forever. Most are run in cycles. Here are the terms you need to know:"],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-cycling-table'], 'content' => $this->cyclingTableHtml()],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-top' => '20px'], 'content' => "Every peptide has its own recommended cycle length. The PepQuiz results include specific dosing and cycling info for your matched peptide."],
                ]],
            ],
        ];
    }

    private function dosingCtaSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-cta2'],
            'style' => ['padding' => '50px 20px', 'background' => 'linear-gradient(135deg, #9A7B4F 0%, #A67B5B 100%)', 'text-align' => 'center'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '600px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h3', 'type' => 'text', 'style' => ['font-size' => '26px', 'font-weight' => '700', 'color' => '#ffffff', 'margin-bottom' => '12px'], 'content' => 'Get Your Personalized Dosing Schedule'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['color' => 'rgba(255,255,255,0.85)', 'margin-bottom' => '24px', 'font-size' => '16px'], 'content' => "Take the quiz and we'll build a custom protocol based on your goals and experience level."],
                    ['tagName' => 'a', 'type' => 'link', 'attributes' => ['href' => '/quiz/pepquiz'], 'style' => ['display' => 'inline-block', 'padding' => '16px 40px', 'background-color' => '#1a1714', 'color' => '#f8f5f0', 'font-weight' => '700', 'border-radius' => '8px', 'text-decoration' => 'none', 'font-size' => '17px'], 'content' => 'Get My Protocol'],
                ]],
            ],
        ];
    }

    private function mythsSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-myths'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '8px', 'text-align' => 'center'], 'content' => 'Myths, Busted'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['color' => '#666', 'text-align' => 'center', 'margin-bottom' => '30px', 'font-size' => '16px'], 'content' => "Let's clear up the biggest misconceptions about peptides."],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-myths-grid'], 'content' => $this->mythsGridHtml()],
                ]],
            ],
        ];
    }

    private function howToGetSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-howtoget'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#ffffff'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '16px'], 'content' => 'How to Get Them'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '16px'], 'content' => "For years, doctors could prescribe most popular peptides. Licensed compounding pharmacies would make them for individual patients. It was legitimate, monitored, and it worked."],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '16px'], 'content' => "Then in 2023, the FDA placed the majority of popular peptides onto a restricted list — prohibiting licensed pharmacies from making them. Here's why:"],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-fda-list'], 'content' => '<ul style="padding-left:20px;margin-bottom:20px;"><li style="font-size:16px;line-height:1.8;color:#444;margin-bottom:10px;"><strong>FDA approval requires large-scale human clinical trials</strong> — a process that takes years and costs hundreds of millions of dollars</li><li style="font-size:16px;line-height:1.8;color:#444;margin-bottom:10px;"><strong>Most peptides are naturally occurring</strong> — making them difficult to patent</li><li style="font-size:16px;line-height:1.8;color:#444;margin-bottom:10px;"><strong>Without a patent</strong>, there\'s limited financial incentive to fund those trials</li><li style="font-size:16px;line-height:1.8;color:#444;margin-bottom:10px;"><strong>Without trial data</strong>, the FDA can\'t approve it — and the cycle continues</li></ul>'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '18px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '16px'], 'content' => 'That one ruling created a gap. And the gray market filled it.'],
                    ['tagName' => 'h3', 'type' => 'text', 'style' => ['font-size' => '28px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-top' => '40px', 'margin-bottom' => '16px'], 'content' => 'Your Two Options Today'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '24px'], 'content' => 'A very short list of FDA-approved peptides you can get through a doctor. And everything else.'],
                ]],
            ],
        ];
    }

    private function comparisonSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-comparison'],
            'style' => ['padding' => '40px 20px 60px', 'background-color' => '#ffffff'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-compare-options'], 'content' => $this->optionsCompareHtml()],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-top' => '24px'], 'components' => [
                        ['tagName' => 'strong', 'style' => ['color' => '#1a1714'], 'content' => 'Most of the research community operates through the gray market.'],
                        ['type' => 'textnode', 'content' => ' The difference between a good and bad experience comes down to two things: '],
                        ['tagName' => 'strong', 'style' => ['color' => '#1a1714'], 'content' => 'independent testing and vendor selection.'],
                    ]],
                ]],
            ],
        ];
    }

    private function grayMarketSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-graymarket'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '16px'], 'content' => 'Gray Market 101'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '16px'], 'components' => [
                        ['type' => 'textnode', 'content' => "The gray market is unregulated. That doesn't mean unsafe — it means "],
                        ['tagName' => 'strong', 'style' => ['color' => '#1a1714'], 'content' => 'you are the quality control.'],
                    ]],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '20px'], 'content' => "Before you buy anything, internalize this:"],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-safety-list'], 'content' => $this->safetyListHtml()],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444', 'margin-top' => '24px'], 'content' => "Which brings us to the most important question in the gray market:"],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '22px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-top' => '8px'], 'content' => 'Who do you actually trust?'],
                ]],
            ],
        ];
    }

    private function vendorCtaSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-cta3'],
            'style' => ['padding' => '50px 20px', 'background' => 'linear-gradient(135deg, #1a1714 0%, #2d2520 100%)', 'text-align' => 'center'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '600px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['color' => '#C9A227', 'font-size' => '15px', 'margin-bottom' => '8px', 'font-weight' => '600'], 'content' => 'Want a trusted vendor?'],
                    ['tagName' => 'h3', 'type' => 'text', 'style' => ['font-size' => '28px', 'font-weight' => '700', 'color' => '#f8f5f0', 'margin-bottom' => '16px'], 'content' => 'Get paired with one of our vetted shops.'],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['color' => '#d4c5b0', 'margin-bottom' => '24px', 'font-size' => '16px'], 'content' => 'The PepQuiz matches you with trusted vendors based on your peptide, budget, and experience level.'],
                    ['tagName' => 'a', 'type' => 'link', 'attributes' => ['href' => '/quiz/pepquiz'], 'style' => ['display' => 'inline-block', 'padding' => '16px 40px', 'background-color' => '#9A7B4F', 'color' => '#ffffff', 'font-weight' => '700', 'border-radius' => '8px', 'text-decoration' => 'none', 'font-size' => '17px'], 'content' => 'Find My Vendor'],
                ]],
            ],
        ];
    }

    private function groceryListSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-grocery'],
            'style' => ['padding' => '60px 20px', 'background-color' => '#ffffff'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['color' => '#666', 'text-align' => 'center', 'margin-bottom' => '8px', 'font-size' => '16px'], 'content' => "Now let's talk about what supplies you need."],
                    ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '32px', 'font-weight' => '700', 'color' => '#1a1714', 'margin-bottom' => '30px', 'text-align' => 'center'], 'content' => 'Peptide Grocery List'],
                    ['tagName' => 'div', 'type' => 'text', 'attributes' => ['class' => 'pfd-grocery-grid'], 'content' => $this->groceryGridHtml()],
                ]],
            ],
        ];
    }

    private function disclaimerSection(): array
    {
        return [
            'tagName' => 'section',
            'attributes' => ['id' => 'pfd-disclaimer'],
            'style' => ['padding' => '40px 20px 60px', 'background-color' => '#f8f5f0'],
            'components' => [
                ['tagName' => 'div', 'style' => ['max-width' => '760px', 'margin' => '0 auto'], 'components' => [
                    ['tagName' => 'hr', 'style' => ['border' => 'none', 'border-top' => '1px solid #d4c5b0', 'margin-bottom' => '24px']],
                    ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '13px', 'line-height' => '1.8', 'color' => '#888'], 'content' => "Disclaimer: This content is for educational and informational purposes only. It is not medical advice. Peptides mentioned here are discussed in the context of published research and community experience. Always consult a licensed healthcare provider before starting any new compound. Professor Peptides does not sell peptides or any controlled substances. We are an educational platform."],
                ]],
            ],
        ];
    }

    // ── HTML fragment helpers ───────────────────────────────────

    private function tierTableHtml(): string
    {
        return <<<'H'
<table class="pfd-tier">
<thead><tr><th>Tier</th><th>Peptides</th><th>Why</th></tr></thead>
<tbody>
<tr class="tier-s"><td><span class="tier-badge tier-s-badge">S</span></td><td>BPC-157, GHK-Cu, Thymosin Beta-4</td><td>Proven track record, broad benefits, well-tolerated</td></tr>
<tr class="tier-a"><td><span class="tier-badge tier-a-badge">A</span></td><td>CJC-1295/Ipamorelin, Semaglutide, PT-141</td><td>Highly effective for specific goals, solid research</td></tr>
<tr class="tier-b"><td><span class="tier-badge tier-b-badge">B</span></td><td>Selank, Semax, KPV, Epithalon</td><td>Strong community feedback, emerging clinical data</td></tr>
<tr class="tier-c"><td><span class="tier-badge tier-c-badge">C</span></td><td>DSIP, Melanotan II, AOD-9604</td><td>Works for some, mixed results or notable side effects</td></tr>
<tr class="tier-d"><td><span class="tier-badge tier-d-badge">D</span></td><td>IGF-1 LR3, Follistatin, GHRP-6</td><td>Higher risk, limited data, or better alternatives exist</td></tr>
</tbody>
</table>
H;
    }

    private function tocHtml(): string
    {
        return <<<'H'
<ol class="pfd-toc">
<li><a href="#pfd-tierlist">TL;DR — Peptide Tier List</a></li>
<li><a href="#pfd-before">Before Taking Peptides</a></li>
<li><a href="#pfd-what">What Are Peptides?</a></li>
<li><a href="#pfd-age">Why Your Body Needs Help</a></li>
<li><a href="#pfd-help">How They Help You</a></li>
<li><a href="#pfd-combine">Can You Combine Them?</a></li>
<li><a href="#pfd-cycling">How Long to Run It</a></li>
<li><a href="#pfd-myths">Myths, Busted</a></li>
<li><a href="#pfd-howtoget">How to Get Them</a></li>
<li><a href="#pfd-graymarket">Gray Market 101</a></li>
<li><a href="#pfd-grocery">Peptide Grocery List</a></li>
</ol>
H;
    }

    private function checklistHtml(): string
    {
        return <<<'H'
<div class="pfd-check-grid">
<div class="pfd-check-item"><div class="pfd-check-icon">&#x1F6CF;</div><div><strong>Sleep</strong><p>7-9 hours of quality sleep. This is when your body does most of its repair work. Peptides that boost GH release are largely useless if you're not sleeping.</p></div></div>
<div class="pfd-check-item"><div class="pfd-check-icon">&#x1F966;</div><div><strong>Nutrition</strong><p>Whole foods, adequate protein (0.8-1g per lb), and enough calories. You can't signal repair in a body that has no building materials.</p></div></div>
<div class="pfd-check-item"><div class="pfd-check-icon">&#x1F3CB;</div><div><strong>Movement</strong><p>Resistance training 3-4x per week minimum. Peptides amplify your training response — no training means nothing to amplify.</p></div></div>
<div class="pfd-check-item"><div class="pfd-check-icon">&#x1F4A7;</div><div><strong>Hydration</strong><p>Half your bodyweight in ounces daily. Dehydration impairs every biological process peptides are trying to enhance.</p></div></div>
</div>
H;
    }

    private function compareTableHtml(): string
    {
        return <<<'H'
<table class="pfd-compare">
<thead><tr><th></th><th>Amino Acid</th><th>Peptide</th><th>Protein</th></tr></thead>
<tbody>
<tr><td><strong>Size</strong></td><td>Single molecule</td><td>2-50 amino acids</td><td>50+ amino acids</td></tr>
<tr><td><strong>Function</strong></td><td>Building block</td><td>Signaling molecule</td><td>Structural / enzymatic</td></tr>
<tr><td><strong>Example</strong></td><td>L-Glutamine</td><td>BPC-157</td><td>Collagen</td></tr>
<tr><td><strong>How it works</strong></td><td>Provides raw material</td><td>Tells body what to do</td><td>Does the heavy lifting</td></tr>
</tbody>
</table>
H;
    }

    private function ageTimelineHtml(): string
    {
        return <<<'H'
<div class="pfd-age-grid">
<div class="pfd-age-card"><div class="pfd-age-decade">20s</div><p>Peak production. GH, collagen, and repair peptides at all-time highs. Recovery is fast. Energy is abundant.</p></div>
<div class="pfd-age-card"><div class="pfd-age-decade">30s</div><p>Production starts declining ~1-2% per year. Recovery slows. Sleep quality drops. First signs of aging appear.</p></div>
<div class="pfd-age-card"><div class="pfd-age-decade">40s</div><p>Significant decline. GH output drops 50%+. Injuries take longer to heal. Fat accumulates easier. Muscle harder to maintain.</p></div>
<div class="pfd-age-card"><div class="pfd-age-decade">55+</div><p>Steep decline across all peptide pathways. Immune function weakens. Skin thins. Bone density drops. This is where targeted peptides make the biggest difference.</p></div>
</div>
H;
    }

    private function categoriesGridHtml(): string
    {
        return <<<'H'
<div class="pfd-cat-grid">
<div class="pfd-cat-card" style="border-left:4px solid #C9A227"><strong>Anti-Aging</strong><p>GHK-Cu, Epithalon, BPC-157</p></div>
<div class="pfd-cat-card" style="border-left:4px solid #10b981"><strong>Injury Repair</strong><p>BPC-157, TB-500, GHK-Cu</p></div>
<div class="pfd-cat-card" style="border-left:4px solid #ef4444"><strong>Fat Loss</strong><p>Semaglutide, AOD-9604, Tesamorelin</p></div>
<div class="pfd-cat-card" style="border-left:4px solid #3b82f6"><strong>Energy & Focus</strong><p>Semax, Selank, Dihexa</p></div>
<div class="pfd-cat-card" style="border-left:4px solid #8b5cf6"><strong>Performance</strong><p>CJC-1295/Ipamorelin, MK-677, Follistatin</p></div>
<div class="pfd-cat-card" style="border-left:4px solid #f59e0b"><strong>Gut Health</strong><p>BPC-157, KPV, Larazotide</p></div>
<div class="pfd-cat-card" style="border-left:4px solid #ec4899"><strong>Better Skin</strong><p>GHK-Cu, Copper Peptides, Collagen Peptides</p></div>
<div class="pfd-cat-card" style="border-left:4px solid #9A7B4F"><strong>Hormones</strong><p>CJC-1295/Ipamorelin, PT-141, Kisspeptin</p></div>
</div>
H;
    }

    private function methodsTableHtml(): string
    {
        return <<<'H'
<table class="pfd-methods">
<thead><tr><th>Method</th><th>What It Is</th><th>Best For</th></tr></thead>
<tbody>
<tr><td><strong>One Peptide</strong></td><td>Single peptide, one goal at a time</td><td>Beginners, first-timers, specific issue</td></tr>
<tr><td><strong>Blend</strong></td><td>Pre-mixed combo in one vial (e.g., CJC/Ipa)</td><td>Convenience, synergistic pairs</td></tr>
<tr><td><strong>Stack</strong></td><td>Multiple separate peptides, custom protocol</td><td>Experienced users, multi-goal protocols</td></tr>
</tbody>
</table>
H;
    }

    private function cyclingTableHtml(): string
    {
        return <<<'H'
<table class="pfd-cycling">
<thead><tr><th>Term</th><th>Definition</th></tr></thead>
<tbody>
<tr><td><strong>On-cycle</strong></td><td>The period you're actively using a peptide. Typically 4-12 weeks depending on the compound.</td></tr>
<tr><td><strong>Off-cycle</strong></td><td>The break between cycles. Lets your body reset receptors and prevents desensitization. Usually 2-4 weeks.</td></tr>
<tr><td><strong>Tapering</strong></td><td>Gradually lowering your dose before stopping instead of quitting cold turkey. Reduces rebound effects.</td></tr>
</tbody>
</table>
H;
    }

    private function mythsGridHtml(): string
    {
        return <<<'H'
<div class="pfd-myths-pairs">
<div class="pfd-myth-row">
  <div class="pfd-myth"><span class="pfd-myth-label">MYTH</span>"They work instantly"</div>
  <div class="pfd-fact"><span class="pfd-fact-label">FACT</span>Expect 4-8 weeks of consistent use</div>
</div>
<div class="pfd-myth-row">
  <div class="pfd-myth"><span class="pfd-myth-label">MYTH</span>"They're all safe"</div>
  <div class="pfd-fact"><span class="pfd-fact-label">FACT</span>Side effects are possible</div>
</div>
<div class="pfd-myth-row">
  <div class="pfd-myth"><span class="pfd-myth-label">MYTH</span>"They all do the same thing"</div>
  <div class="pfd-fact"><span class="pfd-fact-label">FACT</span>Each targets different pathways</div>
</div>
<div class="pfd-myth-row">
  <div class="pfd-myth"><span class="pfd-myth-label">MYTH</span>"Only for bodybuilders"</div>
  <div class="pfd-fact"><span class="pfd-fact-label">FACT</span>Multiple health solutions</div>
</div>
<div class="pfd-myth-row">
  <div class="pfd-myth"><span class="pfd-myth-label">MYTH</span>"Short-term use only"</div>
  <div class="pfd-fact"><span class="pfd-fact-label">FACT</span>Many are safe long-term with cycling</div>
</div>
<div class="pfd-myth-row">
  <div class="pfd-myth"><span class="pfd-myth-label">MYTH</span>"Injections are the only option"</div>
  <div class="pfd-fact"><span class="pfd-fact-label">FACT</span>Also: oral, nasal, and topical forms</div>
</div>
</div>
H;
    }

    private function optionsCompareHtml(): string
    {
        return <<<'H'
<table class="pfd-options">
<thead><tr><th></th><th>&#x1F3E5; Licensed Physician</th><th>&#x1F50D; Gray Market</th></tr></thead>
<tbody>
<tr><td><strong>Cost</strong></td><td>$300 to $600+/mo</td><td>5-20x cheaper</td></tr>
<tr><td><strong>Selection</strong></td><td>Limited by FDA</td><td>Much wider</td></tr>
<tr><td><strong>Quality</strong></td><td>Pharmaceutical-grade</td><td>Varies by vendor</td></tr>
<tr><td><strong>Oversight</strong></td><td>Doctor monitors you</td><td>You monitor yourself</td></tr>
<tr><td><strong>Best for</strong></td><td>Complex conditions, full guidance</td><td>Informed self-researchers</td></tr>
</tbody>
</table>
H;
    }

    private function safetyListHtml(): string
    {
        return <<<'H'
<div class="pfd-safety-items">
<div class="pfd-safety-item"><strong>You are your own regulator.</strong> No FDA, no BBB, and customer service is a Discord DM. If something goes wrong, you're the one who has to figure it out.</div>
<div class="pfd-safety-item"><strong>This isn't Amazon.</strong> Shipping could take 2-8 weeks. Chargebacks hurt everyone.</div>
<div class="pfd-safety-item"><strong>Protect your privacy.</strong> Use a throwaway email. The community is vulnerable to doxxing.</div>
<div class="pfd-safety-item"><strong>Test every order.</strong> HPLC testing is your only way to verify what's in the vial.</div>
</div>
H;
    }

    private function groceryGridHtml(): string
    {
        return <<<'H'
<div class="pfd-grocery-cards">
<div class="pfd-grocery-card"><div class="pfd-grocery-icon">&#x1F9EA;</div><strong>Peptide Vial</strong><p>Small glass bottle containing the peptide.</p></div>
<div class="pfd-grocery-card"><div class="pfd-grocery-icon">&#x1F9F4;</div><strong>Alcohol Swabs</strong><p>Used for sterilizing the vial and injection site.</p></div>
<div class="pfd-grocery-card"><div class="pfd-grocery-icon">&#x1F489;</div><strong>Mixing Syringe</strong><p>Used to draw up BAC water and mix with peptide.</p></div>
<div class="pfd-grocery-card"><div class="pfd-grocery-icon">&#x1F5A5;</div><strong>Clean Workspace</strong><p>A designated area for preparing and injecting peptides.</p></div>
<div class="pfd-grocery-card"><div class="pfd-grocery-icon">&#x1F4A7;</div><strong>BAC or Sterile Water</strong><p>Bacteriostatic water to reconstitute the peptide powder.</p></div>
<div class="pfd-grocery-card"><div class="pfd-grocery-icon">&#x1F489;</div><strong>Insulin Syringes</strong><p>29-31 gauge needles for subcutaneous injection.</p></div>
<div class="pfd-grocery-card"><div class="pfd-grocery-icon">&#x26A0;</div><strong>Sharps Container</strong><p>For safe disposal of used needles and syringes.</p></div>
</div>
H;
    }

    // ── Rendered HTML (frontend) ────────────────────────────────

    private function renderHtml(): string
    {
        return <<<HTML
<!-- Hero -->
<section id="pfd-hero" class="pfd-cta-dark" style="padding:60px 20px 40px;text-align:center;">
<div style="max-width:760px;margin:0 auto;">
<p style="color:#9A7B4F;font-size:14px;text-transform:uppercase;letter-spacing:3px;margin-bottom:16px;font-weight:600;">Professor Peptides Presents</p>
<h1 style="font-size:48px;line-height:1.15;color:#f8f5f0;margin-bottom:16px;font-weight:800;">Peptides For Dummies</h1>
<p style="font-size:20px;color:#C9A227;margin-bottom:24px;font-weight:500;">The 2026 Edition</p>
<div class="pfd-hero-meta" style="color:#a09080;font-size:14px;margin-bottom:30px;">
<span>By Professor Peptides</span><span>Updated Feb 2026</span><span>15 min read</span>
</div>
<p style="font-size:17px;line-height:1.7;color:#d4c5b0;max-width:600px;margin:0 auto;">Everything you need to know about peptides in one place. No PhD required. No sales pitch. Just the straight facts.</p>
</div>
</section>

<!-- Tier List -->
<section id="pfd-tierlist" style="padding:60px 20px;background-color:#f8f5f0;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:8px;text-align:center;">TL;DR — Peptide Tier List</h2>
<p style="color:#666;text-align:center;margin-bottom:30px;font-size:16px;">Ranked by community results, research depth, and real-world reliability.</p>
{$this->tierTableHtml()}
</div>
</section>

<!-- TOC -->
<section id="pfd-toc" style="padding:50px 20px;background-color:#ffffff;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:24px;font-weight:700;color:#1a1714;margin-bottom:20px;">Table of Contents</h2>
{$this->tocHtml()}
</div>
</section>

<!-- Before Taking -->
<section id="pfd-before" style="padding:60px 20px;background-color:#f8f5f0;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:16px;">Before Taking Peptides</h2>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:24px;">Peptides are powerful, but they're not a shortcut. They amplify what you're already doing right. If your foundation is broken, peptides won't fix it — they'll just amplify the mess. Get these right first:</p>
{$this->checklistHtml()}
<p style="font-size:16px;line-height:1.8;color:#444;margin-top:24px;font-style:italic;">You don't have to be perfect. But if you're sleeping 4 hours, living on fast food, and never moving your body — a peptide isn't the answer. Fix the basics first, then layer peptides on top.</p>
</div>
</section>

<!-- What Are Peptides -->
<section id="pfd-what" style="padding:60px 20px;background-color:#ffffff;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:16px;">What Are Peptides?</h2>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:16px;">A peptide is a short chain of amino acids — typically between 2 and 50. They're smaller than proteins and act as signaling molecules, telling your body to do specific things like repair tissue, burn fat, or produce growth hormone.</p>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:24px;">Your body already makes peptides naturally. When you take one, you're amplifying a signal your body already recognizes — that's why they tend to work with your biology instead of against it.</p>
{$this->compareTableHtml()}
</div>
</section>

<!-- Age Decline -->
<section id="pfd-age" style="padding:60px 20px;background-color:#f8f5f0;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:16px;text-align:center;">Why Your Body Needs Help</h2>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:30px;text-align:center;">As you age, your body makes fewer peptides naturally. Here's what that decline looks like:</p>
{$this->ageTimelineHtml()}
</div>
</section>

<!-- How They Help -->
<section id="pfd-help" style="padding:60px 20px;background-color:#ffffff;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:8px;text-align:center;">How They Help You</h2>
<p style="color:#666;text-align:center;margin-bottom:30px;font-size:16px;">Peptides target specific pathways. Here are the 8 most common goals:</p>
{$this->categoriesGridHtml()}
</div>
</section>

<!-- CTA 1 -->
<section id="pfd-cta1" class="pfd-cta-dark" style="padding:50px 20px;text-align:center;">
<div style="max-width:600px;margin:0 auto;">
<h3 style="font-size:28px;font-weight:700;color:#f8f5f0;margin-bottom:12px;">Find Your Peptide</h3>
<p style="color:#d4c5b0;margin-bottom:24px;font-size:16px;">Take the PepQuiz to find the right peptide for your specific goal. 4 minutes. No account needed.</p>
<a href="/quiz/pepquiz" class="pfd-cta-btn" style="padding:16px 40px;background-color:#9A7B4F;color:#ffffff;font-weight:700;text-decoration:none;font-size:17px;">Take the PepQuiz</a>
</div>
</section>

<!-- Combine -->
<section id="pfd-combine" style="padding:60px 20px;background-color:#f8f5f0;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:16px;">Can You Combine Them?</h2>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:24px;">Yes — and most experienced users do. But there's a right way and a wrong way. Here are the three main approaches:</p>
{$this->methodsTableHtml()}
</div>
</section>

<!-- Cycling -->
<section id="pfd-cycling" style="padding:60px 20px;background-color:#ffffff;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:16px;">How Long to Run It</h2>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:24px;">Peptides aren't meant to be taken forever. Most are run in cycles. Here are the terms you need to know:</p>
{$this->cyclingTableHtml()}
<p style="font-size:16px;line-height:1.8;color:#444;margin-top:20px;">Every peptide has its own recommended cycle length. The PepQuiz results include specific dosing and cycling info for your matched peptide.</p>
</div>
</section>

<!-- CTA 2 -->
<section id="pfd-cta2" class="pfd-cta-gold" style="padding:50px 20px;text-align:center;">
<div style="max-width:600px;margin:0 auto;">
<h3 style="font-size:26px;font-weight:700;color:#ffffff;margin-bottom:12px;">Get Your Personalized Dosing Schedule</h3>
<p style="color:rgba(255,255,255,0.85);margin-bottom:24px;font-size:16px;">Take the quiz and we'll build a custom protocol based on your goals and experience level.</p>
<a href="/quiz/pepquiz" class="pfd-cta-btn" style="padding:16px 40px;background-color:#1a1714;color:#f8f5f0;font-weight:700;text-decoration:none;font-size:17px;">Get My Protocol</a>
</div>
</section>

<!-- Myths -->
<section id="pfd-myths" style="padding:60px 20px;background-color:#f8f5f0;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:8px;text-align:center;">Myths, Busted</h2>
<p style="color:#666;text-align:center;margin-bottom:30px;font-size:16px;">Let's clear up the biggest misconceptions about peptides.</p>
{$this->mythsGridHtml()}
</div>
</section>

<!-- How to Get Them -->
<section id="pfd-howtoget" style="padding:60px 20px;background-color:#ffffff;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:16px;">How to Get Them</h2>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:16px;">For years, doctors could prescribe most popular peptides. Licensed compounding pharmacies would make them for individual patients. It was legitimate, monitored, and it worked.</p>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:16px;">Then in 2023, the FDA placed the majority of popular peptides onto a restricted list — prohibiting licensed pharmacies from making them. Here's why:</p>
<ul style="padding-left:20px;margin-bottom:20px;">
<li style="font-size:16px;line-height:1.8;color:#444;margin-bottom:10px;"><strong>FDA approval requires large-scale human clinical trials</strong> — a process that takes years and costs hundreds of millions of dollars</li>
<li style="font-size:16px;line-height:1.8;color:#444;margin-bottom:10px;"><strong>Most peptides are naturally occurring</strong> — making them difficult to patent</li>
<li style="font-size:16px;line-height:1.8;color:#444;margin-bottom:10px;"><strong>Without a patent</strong>, there's limited financial incentive to fund those trials</li>
<li style="font-size:16px;line-height:1.8;color:#444;margin-bottom:10px;"><strong>Without trial data</strong>, the FDA can't approve it — and the cycle continues</li>
</ul>
<p style="font-size:18px;font-weight:700;color:#1a1714;margin-bottom:16px;">That one ruling created a gap. And the gray market filled it.</p>
<h3 style="font-size:28px;font-weight:700;color:#1a1714;margin-top:40px;margin-bottom:16px;">Your Two Options Today</h3>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:24px;">A very short list of FDA-approved peptides you can get through a doctor. And everything else.</p>
</div>
</section>

<!-- Comparison -->
<section id="pfd-comparison" style="padding:40px 20px 60px;background-color:#ffffff;">
<div style="max-width:760px;margin:0 auto;">
{$this->optionsCompareHtml()}
<p style="font-size:16px;line-height:1.8;color:#444;margin-top:24px;"><strong style="color:#1a1714;">Most of the research community operates through the gray market.</strong> The difference between a good and bad experience comes down to two things: <strong style="color:#1a1714;">independent testing and vendor selection.</strong></p>
</div>
</section>

<!-- Gray Market -->
<section id="pfd-graymarket" style="padding:60px 20px;background-color:#f8f5f0;">
<div style="max-width:760px;margin:0 auto;">
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:16px;">Gray Market 101</h2>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:16px;">The gray market is unregulated. That doesn't mean unsafe — it means <strong style="color:#1a1714;">you are the quality control.</strong></p>
<p style="font-size:16px;line-height:1.8;color:#444;margin-bottom:20px;">Before you buy anything, internalize this:</p>
{$this->safetyListHtml()}
<p style="font-size:16px;line-height:1.8;color:#444;margin-top:24px;">Which brings us to the most important question in the gray market:</p>
<p style="font-size:22px;font-weight:700;color:#1a1714;margin-top:8px;">Who do you actually trust?</p>
</div>
</section>

<!-- CTA 3 -->
<section id="pfd-cta3" class="pfd-cta-dark" style="padding:50px 20px;text-align:center;">
<div style="max-width:600px;margin:0 auto;">
<p style="color:#C9A227;font-size:15px;margin-bottom:8px;font-weight:600;">Want a trusted vendor?</p>
<h3 style="font-size:28px;font-weight:700;color:#f8f5f0;margin-bottom:16px;">Get paired with one of our vetted shops.</h3>
<p style="color:#d4c5b0;margin-bottom:24px;font-size:16px;">The PepQuiz matches you with trusted vendors based on your peptide, budget, and experience level.</p>
<a href="/quiz/pepquiz" class="pfd-cta-btn" style="padding:16px 40px;background-color:#9A7B4F;color:#ffffff;font-weight:700;text-decoration:none;font-size:17px;">Find My Vendor</a>
</div>
</section>

<!-- Grocery List -->
<section id="pfd-grocery" style="padding:60px 20px;background-color:#ffffff;">
<div style="max-width:760px;margin:0 auto;">
<p style="color:#666;text-align:center;margin-bottom:8px;font-size:16px;">Now let's talk about what supplies you need.</p>
<h2 style="font-size:32px;font-weight:700;color:#1a1714;margin-bottom:30px;text-align:center;">Peptide Grocery List</h2>
{$this->groceryGridHtml()}
</div>
</section>

<!-- Disclaimer -->
<section id="pfd-disclaimer" style="padding:40px 20px 60px;background-color:#f8f5f0;">
<div style="max-width:760px;margin:0 auto;">
<hr style="border:none;border-top:1px solid #d4c5b0;margin-bottom:24px;">
<p style="font-size:13px;line-height:1.8;color:#888;">Disclaimer: This content is for educational and informational purposes only. It is not medical advice. Peptides mentioned here are discussed in the context of published research and community experience. Always consult a licensed healthcare provider before starting any new compound. Professor Peptides does not sell peptides or any controlled substances. We are an educational platform.</p>
</div>
</section>
HTML;
    }

    // ── CSS ──────────────────────────────────────────────────────

    private function renderCss(): string
    {
        return <<<'CSS'
/* Peptides For Dummies — Page Styles */
.gjs-landing-content { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }

/* Hero + CTA gradient backgrounds (inline gradients stripped by HTMLPurifier) */
.pfd-cta-dark { background: linear-gradient(135deg, #1a1714 0%, #2d2520 100%); }
.pfd-cta-gold { background: linear-gradient(135deg, #9A7B4F 0%, #C9A227 100%); }
.pfd-hero-meta { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
.pfd-cta-btn { display: inline-block; border-radius: 8px; }

/* ─── Shared Table Base ─── */
.pfd-tier, .pfd-compare, .pfd-methods, .pfd-cycling, .pfd-options {
  width: 100%; border-collapse: separate; border-spacing: 0;
  border-radius: 12px; overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06), 0 4px 12px rgba(0,0,0,0.04);
  background: #fff;
}
.pfd-tier thead th, .pfd-compare thead th, .pfd-methods thead th, .pfd-cycling thead th, .pfd-options thead th {
  background: #1a1714; color: #f8f5f0; padding: 14px 18px; text-align: left;
  font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px;
  border-bottom: 2px solid #9A7B4F;
}
.pfd-tier tbody td, .pfd-compare tbody td, .pfd-methods tbody td, .pfd-cycling tbody td, .pfd-options tbody td {
  padding: 14px 18px; font-size: 15px; color: #444; border-bottom: 1px solid #eee;
  line-height: 1.6; vertical-align: top;
}
.pfd-tier tbody tr:last-child td, .pfd-compare tbody tr:last-child td, .pfd-methods tbody tr:last-child td,
.pfd-cycling tbody tr:last-child td, .pfd-options tbody tr:last-child td { border-bottom: none; }
.pfd-tier tbody td strong, .pfd-compare tbody td strong, .pfd-methods tbody td strong,
.pfd-cycling tbody td strong, .pfd-options tbody td strong { color: #1a1714; font-weight: 600; }

/* Alternating row tints */
.pfd-compare tbody tr:nth-child(even), .pfd-methods tbody tr:nth-child(even),
.pfd-cycling tbody tr:nth-child(even), .pfd-options tbody tr:nth-child(even) { background: #faf8f5; }

/* Tier Table — colored rows override alternating */
.tier-s { background: rgba(234,179,8,0.10) !important; }
.tier-a { background: rgba(59,130,246,0.08) !important; }
.tier-b { background: rgba(16,185,129,0.08) !important; }
.tier-c { background: rgba(249,115,22,0.08) !important; }
.tier-d { background: rgba(239,68,68,0.06) !important; }
.tier-badge { display: inline-block; width: 34px; height: 34px; border-radius: 8px; text-align: center; line-height: 34px; font-weight: 800; font-size: 16px; color: #fff; }
.tier-s-badge { background: #eab308; }
.tier-a-badge { background: #3b82f6; }
.tier-b-badge { background: #10b981; }
.tier-c-badge { background: #f97316; }
.tier-d-badge { background: #ef4444; }
.pfd-tier tbody tr { border-bottom: 1px solid #e5e0d8; }

/* ─── Table of Contents ─── */
.pfd-toc { padding-left: 0; list-style: none; counter-reset: toc; display: grid; grid-template-columns: repeat(2, 1fr); gap: 0; }
.pfd-toc li { counter-increment: toc; padding: 14px 18px; font-size: 16px; border-bottom: 1px solid #eee; transition: background 0.15s; }
.pfd-toc li:hover { background: #faf8f5; }
.pfd-toc li::before { content: counter(toc, decimal-leading-zero) " "; color: #9A7B4F; font-weight: 700; font-size: 14px; margin-right: 6px; }
.pfd-toc a { color: #1a1714; text-decoration: none; font-weight: 500; }
.pfd-toc a:hover { color: #9A7B4F; }

/* ─── Checklist ─── */
.pfd-check-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.pfd-check-item { display: flex; gap: 14px; background: #fff; padding: 22px; border-radius: 12px; border: 1px solid #e5e0d8; transition: box-shadow 0.15s; }
.pfd-check-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
.pfd-check-icon { font-size: 28px; flex-shrink: 0; }
.pfd-check-item strong { display: block; color: #1a1714; font-size: 16px; margin-bottom: 4px; }
.pfd-check-item p { color: #666; font-size: 14px; line-height: 1.6; margin: 0; }

/* ─── Age Timeline ─── */
.pfd-age-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.pfd-age-card { background: #fff; padding: 28px 22px; border-radius: 12px; border: 1px solid #e5e0d8; text-align: center; transition: transform 0.15s, box-shadow 0.15s; }
.pfd-age-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.06); }
.pfd-age-decade { font-size: 30px; font-weight: 800; color: #9A7B4F; margin-bottom: 12px; }
.pfd-age-card p { color: #666; font-size: 14px; line-height: 1.6; margin: 0; }

/* ─── Categories Grid ─── */
.pfd-cat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
.pfd-cat-card { background: #f8f5f0; padding: 22px; border-radius: 10px; transition: box-shadow 0.15s; }
.pfd-cat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
.pfd-cat-card strong { display: block; color: #1a1714; font-size: 16px; margin-bottom: 4px; }
.pfd-cat-card p { color: #888; font-size: 14px; margin: 0; }

/* ─── Myths ─── */
.pfd-myths-pairs { display: flex; flex-direction: column; gap: 14px; }
.pfd-myth-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.pfd-myth, .pfd-fact { padding: 20px 22px; border-radius: 10px; font-size: 15px; line-height: 1.6; }
.pfd-myth { background: rgba(239,68,68,0.06); color: #991b1b; border-left: 3px solid #ef4444; }
.pfd-fact { background: rgba(16,185,129,0.06); color: #065f46; border-left: 3px solid #10b981; }
.pfd-myth-label, .pfd-fact-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px; opacity: 0.7; }

/* ─── Safety List ─── */
.pfd-safety-items { display: flex; flex-direction: column; gap: 14px; }
.pfd-safety-item { background: #fff; padding: 20px 22px; border-radius: 10px; border: 1px solid #e5e0d8; font-size: 15px; line-height: 1.7; color: #444; transition: box-shadow 0.15s; }
.pfd-safety-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.pfd-safety-item strong { color: #1a1714; }

/* ─── Grocery Grid ─── */
.pfd-grocery-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.pfd-grocery-card { background: #f8f5f0; padding: 28px 18px; border-radius: 12px; text-align: center; border: 1px solid #e5e0d8; transition: transform 0.15s, box-shadow 0.15s; }
.pfd-grocery-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.06); }
.pfd-grocery-icon { font-size: 36px; margin-bottom: 12px; }
.pfd-grocery-card strong { display: block; color: #1a1714; font-size: 15px; margin-bottom: 6px; }
.pfd-grocery-card p { color: #888; font-size: 13px; line-height: 1.5; margin: 0; }

/* ─── Responsive ─── */
@media (max-width:768px) {
  .pfd-check-grid { grid-template-columns: 1fr; }
  .pfd-age-grid { grid-template-columns: repeat(2, 1fr); }
  .pfd-cat-grid { grid-template-columns: 1fr; }
  .pfd-myth-row { grid-template-columns: 1fr; }
  .pfd-grocery-cards { grid-template-columns: repeat(2, 1fr); }
  .pfd-toc { grid-template-columns: 1fr; }
  #pfd-hero h1 { font-size: 36px !important; }
  .pfd-tier, .pfd-compare, .pfd-methods, .pfd-cycling, .pfd-options { font-size: 14px; }
  .pfd-tier thead th, .pfd-compare thead th, .pfd-methods thead th, .pfd-cycling thead th, .pfd-options thead th { padding: 12px 14px; font-size: 12px; }
  .pfd-tier tbody td, .pfd-compare tbody td, .pfd-methods tbody td, .pfd-cycling tbody td, .pfd-options tbody td { padding: 12px 14px; font-size: 14px; }
}
@media (max-width:480px) {
  .pfd-age-grid { grid-template-columns: 1fr; }
  .pfd-grocery-cards { grid-template-columns: 1fr; }
  #pfd-hero h1 { font-size: 28px !important; }
  .pfd-compare, .pfd-methods, .pfd-cycling, .pfd-options { display: block; }
  .pfd-compare thead, .pfd-methods thead, .pfd-cycling thead, .pfd-options thead { display: none; }
  .pfd-compare tbody, .pfd-methods tbody, .pfd-cycling tbody, .pfd-options tbody,
  .pfd-compare tbody tr, .pfd-methods tbody tr, .pfd-cycling tbody tr, .pfd-options tbody tr { display: block; }
  .pfd-compare tbody td, .pfd-methods tbody td, .pfd-cycling tbody td, .pfd-options tbody td { display: block; padding: 8px 14px; border-bottom: none; }
  .pfd-compare tbody tr, .pfd-methods tbody tr, .pfd-cycling tbody tr, .pfd-options tbody tr { border-bottom: 1px solid #eee; padding: 8px 0; }
}
CSS;
    }
}
