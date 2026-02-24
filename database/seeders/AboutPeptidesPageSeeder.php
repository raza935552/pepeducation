<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class AboutPeptidesPageSeeder extends Seeder
{
    public function run(): void
    {
        $content = $this->getGrapesJsContent();
        $html = $this->renderHtml();

        Page::updateOrCreate(
            ['slug' => 'about-peptides'],
            [
                'title' => 'Your Next Step After Supplements',
                'slug' => 'about-peptides',
                'content' => $content,
                'html' => $html,
                'meta_title' => 'About Peptides — Your Next Step After Supplements | Professor Peptides',
                'meta_description' => 'Learn what peptides are, how they differ from supplements, and how Professor Peptides helps you find the right peptide for your health goals.',
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
                        $this->welcomeSection(),
                        $this->whoAreWeSection(),
                        $this->whatArePeptidesSection(),
                        $this->whyPeptidesSection(),
                        $this->howToGetPeptidesSection(),
                        $this->whatWeStandForSection(),
                        $this->readyToStartSection(),
                        $this->disclaimerSection(),
                    ],
                ],
            ],
            'styles' => [],
        ];
    }

    // ── Section builders ──────────────────────────────────────────

    private function heroSection(): array
    {
        return [
            'tagName' => 'section',
            'style' => ['padding' => '60px 20px 40px', 'background-color' => '#ffffff', 'text-align' => 'center'],
            'components' => [
                [
                    'tagName' => 'div',
                    'style' => ['max-width' => '700px', 'margin' => '0 auto'],
                    'components' => [
                        // Icon placeholder
                        ['tagName' => 'div', 'type' => 'text', 'style' => ['font-size' => '64px', 'margin-bottom' => '20px'], 'content' => "\xF0\x9F\xA7\xAA"],
                        // Headline
                        ['tagName' => 'h1', 'type' => 'text', 'style' => ['font-size' => '42px', 'line-height' => '1.2', 'color' => '#333333', 'margin-bottom' => '16px', 'font-weight' => '700'], 'content' => 'Your Next Step After Supplements.'],
                        // Subtext
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '17px', 'line-height' => '1.6', 'color' => '#666666', 'margin-bottom' => '28px'], 'content' => 'Helping supplement-takers find the best peptide based on their health goals.'],
                        // CTA buttons
                        [
                            'tagName' => 'div',
                            'style' => ['display' => 'flex', 'gap' => '12px', 'justify-content' => 'center', 'flex-wrap' => 'wrap', 'margin-bottom' => '10px'],
                            'components' => [
                                ['tagName' => 'a', 'type' => 'link', 'style' => ['display' => 'inline-block', 'padding' => '14px 32px', 'background-color' => '#00bcd4', 'color' => '#ffffff', 'font-weight' => '600', 'border-radius' => '30px', 'text-decoration' => 'none', 'font-size' => '16px'], 'content' => 'Take the PepQuiz', 'attributes' => ['href' => '/quiz/pepquiz']],
                                ['tagName' => 'a', 'type' => 'link', 'style' => ['display' => 'inline-block', 'padding' => '14px 32px', 'background-color' => 'transparent', 'color' => '#333333', 'font-weight' => '600', 'border-radius' => '30px', 'text-decoration' => 'none', 'font-size' => '16px', 'border' => '2px solid #333333'], 'content' => 'Learn More', 'attributes' => ['href' => '#welcome']],
                            ],
                        ],
                        // Tagline
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-style' => 'italic', 'color' => '#999999', 'font-size' => '15px', 'margin-top' => '20px'], 'content' => 'No PhD required. Just a wifi connection and a fridge.'],
                    ],
                ],
            ],
        ];
    }

    private function welcomeSection(): array
    {
        return [
            'tagName' => 'section',
            'style' => ['padding' => '50px 20px', 'background-color' => '#ffffff'],
            'attributes' => ['id' => 'welcome'],
            'components' => [
                [
                    'tagName' => 'div',
                    'style' => ['max-width' => '700px', 'margin' => '0 auto'],
                    'components' => [
                        ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '22px', 'font-weight' => '700', 'color' => '#333333', 'margin-bottom' => '16px', 'text-transform' => 'uppercase', 'letter-spacing' => '1px'], 'content' => 'Welcome to Professor Peptides'],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444'], 'content' => 'We help supplement users figure out which peptide to try, where to get it, and what to do once it shows up.'],
                    ],
                ],
            ],
        ];
    }

    private function whoAreWeSection(): array
    {
        return [
            'tagName' => 'section',
            'style' => ['padding' => '50px 20px', 'background-color' => '#f8f9fa'],
            'components' => [
                [
                    'tagName' => 'div',
                    'style' => ['max-width' => '700px', 'margin' => '0 auto'],
                    'components' => [
                        ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '22px', 'font-weight' => '700', 'color' => '#333333', 'margin-bottom' => '16px', 'text-transform' => 'uppercase', 'letter-spacing' => '1px'], 'content' => 'Who Are We?'],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'content' => "We're your supplement-taker's tour guide into the world of peptides."],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'content' => "Whether you're here because you're tired of dumping hundreds on supplements that barely move the needle, you keep seeing \"peptides\" all over your feeds but have no idea where to start, or you fell down a Reddit rabbit hole at 2am and came out more confused than when you went in, we've got you covered."],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'components' => [
                            ['tagName' => 'strong', 'style' => ['color' => '#333333'], 'content' => 'We are not a peptide vendor.'],
                            ['type' => 'textnode', 'content' => " You can't buy anything from us. We don't sell peptides, supplements, or anything else. We're also not social media influencers pushing the latest trend for a brand deal."],
                        ]],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'content' => "So what are we? We are people with real experience in the peptide space who got tired of watching beginners get bad info, so we decided to organize everything we've learned into one place. Think of us like a peptide-specific Wikipedia, Reddit, and Expedia that had a baby. One platform for learning what works, hearing from real people, and finding exactly where to go next."],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444'], 'content' => "Everything on this platform is free. No subscriptions. No upsells. No \"unlock premium\" nonsense."],
                    ],
                ],
            ],
        ];
    }

    private function whatArePeptidesSection(): array
    {
        return [
            'tagName' => 'section',
            'style' => ['padding' => '50px 20px', 'background-color' => '#ffffff'],
            'components' => [
                [
                    'tagName' => 'div',
                    'style' => ['max-width' => '700px', 'margin' => '0 auto'],
                    'components' => [
                        ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '22px', 'font-weight' => '700', 'color' => '#333333', 'margin-bottom' => '16px', 'text-transform' => 'uppercase', 'letter-spacing' => '1px'], 'content' => 'What Are Peptides?'],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'components' => [
                            ['tagName' => 'strong', 'style' => ['color' => '#333333'], 'content' => 'If you already know what peptides are, skip ahead.'],
                            ['type' => 'textnode', 'content' => " This part is for the people who keep hearing the word but never get a straight answer."],
                        ]],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'content' => "Peptides are tiny chains of amino acids that tell your body to do specific things. That's it. Your body already makes them naturally. When you take a peptide, you're basically giving your body more of a signal it already recognizes. That's why they work differently than supplements, which just dump nutrients into your system and hope for the best."],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'components' => [
                            ['type' => 'textnode', 'content' => "Different peptides send different signals. Some tell your body to burn fat. Some tell it to heal faster. Some help with sleep, gut health, or muscle recovery. The key is matching the right peptide to your specific goal, which is exactly what the "],
                            ['tagName' => 'a', 'type' => 'link', 'style' => ['color' => '#00bcd4', 'text-decoration' => 'underline', 'font-weight' => '600'], 'content' => 'PepQuiz', 'attributes' => ['href' => '/quiz/pepquiz']],
                            ['type' => 'textnode', 'content' => ' does.'],
                        ]],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444'], 'content' => "If you've seen videos like \"WTF Are Peptides\" or \"Every Peptide Explained in 10 Minutes\" and still felt lost, you're not alone. That's literally why we built this."],
                    ],
                ],
            ],
        ];
    }

    private function whyPeptidesSection(): array
    {
        return [
            'tagName' => 'section',
            'style' => ['padding' => '50px 20px', 'background-color' => '#f8f9fa'],
            'components' => [
                [
                    'tagName' => 'div',
                    'style' => ['max-width' => '700px', 'margin' => '0 auto'],
                    'components' => [
                        ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '22px', 'font-weight' => '700', 'color' => '#333333', 'margin-bottom' => '16px', 'text-transform' => 'uppercase', 'letter-spacing' => '1px'], 'content' => 'Why Peptides Over Supplements?'],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'components' => [
                            ['tagName' => 'strong', 'style' => ['color' => '#333333'], 'content' => 'There are a lot of ways to tackle a health problem, but some are better than others (way better).'],
                        ]],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'content' => "Supplements are the easiest route, but they take forever and hit a ceiling pretty fast. You can only stack so many vitamins and powders before you're just making expensive pee. Replacement therapy might be the most effective option out there, but it can create serious problems that aren't worth it to begin with. And once you're on that train, getting off isn't always simple."],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'content' => "Peptides sit in the sweet spot. The perfect next step after your supplements stop pulling their weight before you start messing with your body's natural systems. They work with what you've got, not against it."],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444'], 'content' => "The hard part isn't deciding if peptides are worth trying. It's figuring out which one, where to get it, and what to do once it shows up at your door. There are dozens of peptides out there. Not all of them are worth your time. And the ones that are worth it depend entirely on what you're actually trying to fix."],
                    ],
                ],
            ],
        ];
    }

    private function howToGetPeptidesSection(): array
    {
        return [
            'tagName' => 'section',
            'style' => ['padding' => '50px 20px', 'background-color' => '#ffffff'],
            'components' => [
                [
                    'tagName' => 'div',
                    'style' => ['max-width' => '700px', 'margin' => '0 auto'],
                    'components' => [
                        ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '22px', 'font-weight' => '700', 'color' => '#333333', 'margin-bottom' => '16px', 'text-transform' => 'uppercase', 'letter-spacing' => '1px'], 'content' => 'How Do People Get Peptides?'],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'content' => 'There are 2 main ways to get peptides, and neither one is wrong.'],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'components' => [
                            ['tagName' => 'strong', 'style' => ['color' => '#333333'], 'content' => 'Telehealth'],
                            ['type' => 'textnode', 'content' => " means getting peptides through a board-certified physician, usually online. This is for folks who prefer having a doctor in their corner and don't mind paying more for that peace of mind."],
                        ]],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'components' => [
                            ['tagName' => 'strong', 'style' => ['color' => '#333333'], 'content' => 'Research'],
                            ['type' => 'textnode', 'content' => " means sourcing peptides independently for research purposes only. This is for folks who prefer the DIY route and would rather save some money in exchange for doing their own homework."],
                        ]],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'content' => "Both ways work. Both have tradeoffs. Your choice comes down to what matters more to you: convenience or cost. We cover both and the PepQuiz will match you with trusted options based on whichever path fits."],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444'], 'content' => "We don't push one path over the other. We don't get paid more if you pick telehealth or vice versa. We just lay out the facts and let you decide."],
                    ],
                ],
            ],
        ];
    }

    private function whatWeStandForSection(): array
    {
        return [
            'tagName' => 'section',
            'style' => ['padding' => '50px 20px', 'background-color' => '#f8f9fa'],
            'components' => [
                [
                    'tagName' => 'div',
                    'style' => ['max-width' => '700px', 'margin' => '0 auto'],
                    'components' => [
                        ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '22px', 'font-weight' => '700', 'color' => '#333333', 'margin-bottom' => '16px', 'text-transform' => 'uppercase', 'letter-spacing' => '1px'], 'content' => 'What Do We Stand For?'],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'content' => "We believe peptides are the future of effective and affordable health, and that future shouldn't be locked behind paywalls, jargon, or a PhD."],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '16px'], 'content' => "Everyone deserves easy-to-understand education and real access to peptide knowledge. Not gatekept forums. Not influencer hype. Not vendor marketing dressed up as science."],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '20px'], 'content' => "Here's what we're actually here to do:"],
                        // Numbered list
                        [
                            'tagName' => 'div',
                            'style' => ['padding-left' => '10px'],
                            'components' => [
                                ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '14px'], 'components' => [
                                    ['tagName' => 'strong', 'style' => ['color' => '#333333'], 'content' => '1. Match you with the right peptide.'],
                                    ['type' => 'textnode', 'content' => " Not the trendy one. Not the one some influencer is getting paid to promote. The one that fits your health goal based on what the community actually reports working."],
                                ]],
                                ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '14px'], 'components' => [
                                    ['tagName' => 'strong', 'style' => ['color' => '#333333'], 'content' => '2. Connect you with trusted vendors.'],
                                    ['type' => 'textnode', 'content' => " Whether that's a telehealth provider or a research supplier, we've vetted the options so you don't have to sort through the noise. No mystery vendors. No \"trust me bro\" recommendations."],
                                ]],
                                ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444'], 'components' => [
                                    ['tagName' => 'strong', 'style' => ['color' => '#333333'], 'content' => '3. Give you a real guide.'],
                                    ['type' => 'textnode', 'content' => " Step by step. Beginner to whatever peptide you're working with. Dosage, storage, what to expect. So when it shows up, you're not staring at a vial wondering what to do next."],
                                ]],
                            ],
                        ],
                        // Transition
                        ['tagName' => 'h3', 'type' => 'text', 'style' => ['font-size' => '20px', 'font-weight' => '700', 'color' => '#333333', 'margin-top' => '30px', 'margin-bottom' => '8px', 'text-align' => 'center'], 'content' => 'We put all of that into one free tool.'],
                    ],
                ],
            ],
        ];
    }

    private function readyToStartSection(): array
    {
        return [
            'tagName' => 'section',
            'style' => ['padding' => '50px 20px', 'background-color' => '#ffffff', 'text-align' => 'center'],
            'components' => [
                [
                    'tagName' => 'div',
                    'style' => ['max-width' => '700px', 'margin' => '0 auto'],
                    'components' => [
                        ['tagName' => 'h2', 'type' => 'text', 'style' => ['font-size' => '28px', 'font-weight' => '700', 'color' => '#333333', 'margin-bottom' => '16px'], 'content' => 'Ready to Get Started?'],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '16px', 'line-height' => '1.8', 'color' => '#444444', 'margin-bottom' => '24px'], 'content' => "Take the PepQuiz to find out which peptide fits your goals, where to get it, and exactly how to use it. Takes about 4 minutes and no account needed."],
                        // CTA button
                        ['tagName' => 'a', 'type' => 'link', 'style' => ['display' => 'inline-block', 'padding' => '16px 40px', 'background-color' => '#00bcd4', 'color' => '#ffffff', 'font-weight' => '600', 'border-radius' => '30px', 'text-decoration' => 'none', 'font-size' => '18px'], 'content' => 'Take the PepQuiz', 'attributes' => ['href' => '/quiz/pepquiz']],
                        // Tagline
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-style' => 'italic', 'color' => '#999999', 'font-size' => '15px', 'margin-top' => '20px'], 'content' => 'No PhD required. Just a wifi connection and a fridge.'],
                    ],
                ],
            ],
        ];
    }

    private function disclaimerSection(): array
    {
        return [
            'tagName' => 'section',
            'style' => ['padding' => '40px 20px 60px', 'background-color' => '#f8f9fa'],
            'components' => [
                [
                    'tagName' => 'div',
                    'style' => ['max-width' => '700px', 'margin' => '0 auto'],
                    'components' => [
                        ['tagName' => 'hr', 'style' => ['border' => 'none', 'border-top' => '1px solid #dddddd', 'margin-bottom' => '24px']],
                        ['tagName' => 'p', 'type' => 'text', 'style' => ['font-size' => '14px', 'line-height' => '1.8', 'color' => '#888888'], 'content' => "PS: We're not doctors and anything construed as medical advice here should be discussed with your own primary healthcare provider. We're passionate about building a community dedicated to sharing knowledge and ensuring continued access to peptides while promoting harm reduction strategies."],
                    ],
                ],
            ],
        ];
    }

    // ── Rendered HTML ──────────────────────────────────────────────

    private function renderHtml(): string
    {
        return <<<'HTML'
<section style="padding: 60px 20px 40px; background-color: #ffffff; text-align: center;">
  <div style="max-width: 700px; margin: 0 auto;">
    <div style="font-size: 64px; margin-bottom: 20px;">&#x1F9EA;</div>
    <h1 style="font-size: 42px; line-height: 1.2; color: #333333; margin-bottom: 16px; font-weight: 700;">Your Next Step After Supplements.</h1>
    <p style="font-size: 17px; line-height: 1.6; color: #666666; margin-bottom: 28px;">Helping supplement-takers find the best peptide based on their health goals.</p>
    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 10px;">
      <a href="/quiz/pepquiz" style="display: inline-block; padding: 14px 32px; background-color: #00bcd4; color: #ffffff; font-weight: 600; border-radius: 30px; text-decoration: none; font-size: 16px;">Take the PepQuiz</a>
      <a href="#welcome" style="display: inline-block; padding: 14px 32px; background-color: transparent; color: #333333; font-weight: 600; border-radius: 30px; text-decoration: none; font-size: 16px; border: 2px solid #333333;">Learn More</a>
    </div>
    <p style="font-style: italic; color: #999999; font-size: 15px; margin-top: 20px;">No PhD required. Just a wifi connection and a fridge.</p>
  </div>
</section>

<section id="welcome" style="padding: 50px 20px; background-color: #ffffff;">
  <div style="max-width: 700px; margin: 0 auto;">
    <h2 style="font-size: 22px; font-weight: 700; color: #333333; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 1px;">Welcome to Professor Peptides</h2>
    <p style="font-size: 16px; line-height: 1.8; color: #444444;">We help supplement users figure out which peptide to try, where to get it, and what to do once it shows up.</p>
  </div>
</section>

<section style="padding: 50px 20px; background-color: #f8f9fa;">
  <div style="max-width: 700px; margin: 0 auto;">
    <h2 style="font-size: 22px; font-weight: 700; color: #333333; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 1px;">Who Are We?</h2>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">We're your supplement-taker's tour guide into the world of peptides.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">Whether you're here because you're tired of dumping hundreds on supplements that barely move the needle, you keep seeing "peptides" all over your feeds but have no idea where to start, or you fell down a Reddit rabbit hole at 2am and came out more confused than when you went in, we've got you covered.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;"><strong style="color: #333333;">We are not a peptide vendor.</strong> You can't buy anything from us. We don't sell peptides, supplements, or anything else. We're also not social media influencers pushing the latest trend for a brand deal.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">So what are we? We are people with real experience in the peptide space who got tired of watching beginners get bad info, so we decided to organize everything we've learned into one place. Think of us like a peptide-specific Wikipedia, Reddit, and Expedia that had a baby. One platform for learning what works, hearing from real people, and finding exactly where to go next.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444;">Everything on this platform is free. No subscriptions. No upsells. No "unlock premium" nonsense.</p>
  </div>
</section>

<section style="padding: 50px 20px; background-color: #ffffff;">
  <div style="max-width: 700px; margin: 0 auto;">
    <h2 style="font-size: 22px; font-weight: 700; color: #333333; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 1px;">What Are Peptides?</h2>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;"><strong style="color: #333333;">If you already know what peptides are, skip ahead.</strong> This part is for the people who keep hearing the word but never get a straight answer.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">Peptides are tiny chains of amino acids that tell your body to do specific things. That's it. Your body already makes them naturally. When you take a peptide, you're basically giving your body more of a signal it already recognizes. That's why they work differently than supplements, which just dump nutrients into your system and hope for the best.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">Different peptides send different signals. Some tell your body to burn fat. Some tell it to heal faster. Some help with sleep, gut health, or muscle recovery. The key is matching the right peptide to your specific goal, which is exactly what the <a href="/quiz/pepquiz" style="color: #00bcd4; text-decoration: underline; font-weight: 600;">PepQuiz</a> does.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444;">If you've seen videos like "WTF Are Peptides" or "Every Peptide Explained in 10 Minutes" and still felt lost, you're not alone. That's literally why we built this.</p>
  </div>
</section>

<section style="padding: 50px 20px; background-color: #f8f9fa;">
  <div style="max-width: 700px; margin: 0 auto;">
    <h2 style="font-size: 22px; font-weight: 700; color: #333333; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 1px;">Why Peptides Over Supplements?</h2>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;"><strong style="color: #333333;">There are a lot of ways to tackle a health problem, but some are better than others (way better).</strong></p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">Supplements are the easiest route, but they take forever and hit a ceiling pretty fast. You can only stack so many vitamins and powders before you're just making expensive pee. Replacement therapy might be the most effective option out there, but it can create serious problems that aren't worth it to begin with. And once you're on that train, getting off isn't always simple.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">Peptides sit in the sweet spot. The perfect next step after your supplements stop pulling their weight before you start messing with your body's natural systems. They work with what you've got, not against it.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444;">The hard part isn't deciding if peptides are worth trying. It's figuring out which one, where to get it, and what to do once it shows up at your door. There are dozens of peptides out there. Not all of them are worth your time. And the ones that are worth it depend entirely on what you're actually trying to fix.</p>
  </div>
</section>

<section style="padding: 50px 20px; background-color: #ffffff;">
  <div style="max-width: 700px; margin: 0 auto;">
    <h2 style="font-size: 22px; font-weight: 700; color: #333333; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 1px;">How Do People Get Peptides?</h2>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">There are 2 main ways to get peptides, and neither one is wrong.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;"><strong style="color: #333333;">Telehealth</strong> means getting peptides through a board-certified physician, usually online. This is for folks who prefer having a doctor in their corner and don't mind paying more for that peace of mind.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;"><strong style="color: #333333;">Research</strong> means sourcing peptides independently for research purposes only. This is for folks who prefer the DIY route and would rather save some money in exchange for doing their own homework.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">Both ways work. Both have tradeoffs. Your choice comes down to what matters more to you: convenience or cost. We cover both and the PepQuiz will match you with trusted options based on whichever path fits.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444;">We don't push one path over the other. We don't get paid more if you pick telehealth or vice versa. We just lay out the facts and let you decide.</p>
  </div>
</section>

<section style="padding: 50px 20px; background-color: #f8f9fa;">
  <div style="max-width: 700px; margin: 0 auto;">
    <h2 style="font-size: 22px; font-weight: 700; color: #333333; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 1px;">What Do We Stand For?</h2>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">We believe peptides are the future of effective and affordable health, and that future shouldn't be locked behind paywalls, jargon, or a PhD.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 16px;">Everyone deserves easy-to-understand education and real access to peptide knowledge. Not gatekept forums. Not influencer hype. Not vendor marketing dressed up as science.</p>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 20px;">Here's what we're actually here to do:</p>
    <div style="padding-left: 10px;">
      <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 14px;"><strong style="color: #333333;">1. Match you with the right peptide.</strong> Not the trendy one. Not the one some influencer is getting paid to promote. The one that fits your health goal based on what the community actually reports working.</p>
      <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 14px;"><strong style="color: #333333;">2. Connect you with trusted vendors.</strong> Whether that's a telehealth provider or a research supplier, we've vetted the options so you don't have to sort through the noise. No mystery vendors. No "trust me bro" recommendations.</p>
      <p style="font-size: 16px; line-height: 1.8; color: #444444;"><strong style="color: #333333;">3. Give you a real guide.</strong> Step by step. Beginner to whatever peptide you're working with. Dosage, storage, what to expect. So when it shows up, you're not staring at a vial wondering what to do next.</p>
    </div>
    <h3 style="font-size: 20px; font-weight: 700; color: #333333; margin-top: 30px; margin-bottom: 8px; text-align: center;">We put all of that into one free tool.</h3>
  </div>
</section>

<section style="padding: 50px 20px; background-color: #ffffff; text-align: center;">
  <div style="max-width: 700px; margin: 0 auto;">
    <h2 style="font-size: 28px; font-weight: 700; color: #333333; margin-bottom: 16px;">Ready to Get Started?</h2>
    <p style="font-size: 16px; line-height: 1.8; color: #444444; margin-bottom: 24px;">Take the PepQuiz to find out which peptide fits your goals, where to get it, and exactly how to use it. Takes about 4 minutes and no account needed.</p>
    <a href="/quiz/pepquiz" style="display: inline-block; padding: 16px 40px; background-color: #00bcd4; color: #ffffff; font-weight: 600; border-radius: 30px; text-decoration: none; font-size: 18px;">Take the PepQuiz</a>
    <p style="font-style: italic; color: #999999; font-size: 15px; margin-top: 20px;">No PhD required. Just a wifi connection and a fridge.</p>
  </div>
</section>

<section style="padding: 40px 20px 60px; background-color: #f8f9fa;">
  <div style="max-width: 700px; margin: 0 auto;">
    <hr style="border: none; border-top: 1px solid #dddddd; margin-bottom: 24px;">
    <p style="font-size: 14px; line-height: 1.8; color: #888888;">PS: We're not doctors and anything construed as medical advice here should be discussed with your own primary healthcare provider. We're passionate about building a community dedicated to sharing knowledge and ensuring continued access to peptides while promoting harm reduction strategies.</p>
  </div>
</section>
HTML;
    }
}
