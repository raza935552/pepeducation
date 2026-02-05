<?php

namespace Database\Seeders;

use App\Models\PageTemplate;
use Illuminate\Database\Seeder;

class PageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Advertorial - Product Review',
                'slug' => 'advertorial-product-review',
                'description' => 'News-style article promoting a product with testimonials',
                'category' => 'advertorial',
                'is_system' => true,
                'content' => $this->getAdvertorialTemplate(),
            ],
            [
                'name' => 'Listicle - Top 10',
                'slug' => 'listicle-top-10',
                'description' => 'Numbered list article with product recommendations',
                'category' => 'listicle',
                'is_system' => true,
                'content' => $this->getListicleTemplate(),
            ],
            [
                'name' => 'Landing Page - Product',
                'slug' => 'landing-page-product',
                'description' => 'High-converting product landing page with CTA',
                'category' => 'landing',
                'is_system' => true,
                'content' => $this->getLandingTemplate(),
            ],
            [
                'name' => 'Problem-Solution Advertorial',
                'slug' => 'problem-solution-advertorial',
                'description' => 'Identifies pain point, presents your product as the solution',
                'category' => 'advertorial',
                'is_system' => true,
                'content' => $this->getProblemSolutionTemplate(),
            ],
            [
                'name' => 'Listicle - 5 Reasons Why',
                'slug' => 'listicle-5-reasons',
                'description' => 'Compelling reasons format with special offer CTA',
                'category' => 'listicle',
                'is_system' => true,
                'content' => $this->get5ReasonsTemplate(),
            ],
            [
                'name' => 'Before/After Story',
                'slug' => 'before-after-story',
                'description' => 'Transformation-focused testimonial with visual proof',
                'category' => 'advertorial',
                'is_system' => true,
                'content' => $this->getBeforeAfterTemplate(),
            ],
            [
                'name' => 'Expert Comparison Review',
                'slug' => 'expert-comparison-review',
                'description' => 'Authority-style product comparison with clear winner',
                'category' => 'advertorial',
                'is_system' => true,
                'content' => $this->getComparisonTemplate(),
            ],
            [
                'name' => 'Quick Tips Guide',
                'slug' => 'quick-tips-guide',
                'description' => 'Skimmable tips format with icons and clear CTAs',
                'category' => 'listicle',
                'is_system' => true,
                'content' => $this->getQuickTipsTemplate(),
            ],
        ];

        foreach ($templates as $template) {
            PageTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }

    private function getAdvertorialTemplate(): array
    {
        return [
            'components' => [
                [
                    'type' => 'wrapper',
                    'components' => [
                        // Hero Section
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'p', 'style' => ['color' => '#9A7B4F', 'font-size' => '14px', 'margin-bottom' => '10px'], 'content' => 'HEALTH & WELLNESS'],
                                        ['tagName' => 'h1', 'style' => ['font-size' => '42px', 'line-height' => '1.2', 'color' => '#333', 'margin-bottom' => '20px'], 'content' => 'New Research Reveals Breakthrough Discovery That\'s Changing Everything'],
                                        ['tagName' => 'p', 'style' => ['color' => '#666', 'font-size' => '14px'], 'content' => 'By Dr. Sarah Johnson | Updated: Today | 5 min read'],
                                    ],
                                ],
                            ],
                        ],
                        // Content Section
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '40px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'line-height' => '1.8', 'color' => '#333', 'margin-bottom' => '20px'], 'content' => 'In a groundbreaking development that has researchers excited, a new approach to wellness is showing remarkable results. Thousands of people are already experiencing the benefits...'],
                                        ['tagName' => 'img', 'style' => ['width' => '100%', 'border-radius' => '12px', 'margin' => '30px 0'], 'attributes' => ['src' => 'https://placehold.co/800x400/9A7B4F/ffffff?text=Product+Image']],
                                        ['tagName' => 'h2', 'style' => ['font-size' => '28px', 'color' => '#333', 'margin' => '30px 0 15px'], 'content' => 'The Science Behind the Results'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'line-height' => '1.8', 'color' => '#333', 'margin-bottom' => '20px'], 'content' => 'Research conducted at leading institutions has demonstrated significant improvements in key health markers. The unique formulation targets multiple pathways for comprehensive benefits.'],
                                    ],
                                ],
                            ],
                        ],
                        // Testimonial
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '600px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'p', 'style' => ['font-size' => '48px', 'color' => '#9A7B4F', 'line-height' => '1'], 'content' => '"'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '20px', 'font-style' => 'italic', 'color' => '#333', 'margin' => '20px 0'], 'content' => 'I was skeptical at first, but after just two weeks I noticed a real difference. This has been a game-changer for me.'],
                                        ['tagName' => 'p', 'style' => ['font-weight' => '600', 'color' => '#333'], 'content' => '- Michael R., Verified Buyer'],
                                    ],
                                ],
                            ],
                        ],
                        // CTA Section
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#A67B5B', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '600px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '32px', 'color' => '#fff', 'margin-bottom' => '15px'], 'content' => 'Try It Risk-Free Today'],
                                        ['tagName' => 'p', 'style' => ['color' => 'rgba(255,255,255,0.9)', 'margin-bottom' => '25px'], 'content' => 'Join thousands of satisfied customers. 30-day money-back guarantee.'],
                                        ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'padding' => '15px 40px', 'background-color' => '#fff', 'color' => '#9A7B4F', 'font-weight' => '600', 'border-radius' => '30px', 'text-decoration' => 'none'], 'content' => 'Get Started Now', 'attributes' => ['href' => '#']],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'styles' => [],
        ];
    }

    private function getListicleTemplate(): array
    {
        return [
            'components' => [
                [
                    'type' => 'wrapper',
                    'components' => [
                        // Hero
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h1', 'style' => ['font-size' => '42px', 'line-height' => '1.2', 'color' => '#333', 'margin-bottom' => '20px'], 'content' => 'Top 10 Products That Actually Work in 2024'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => '#666'], 'content' => 'We tested dozens of products to bring you only the best'],
                                    ],
                                ],
                            ],
                        ],
                        // List Item 1
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '40px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '30px', 'align-items' => 'center'],
                                    'components' => [
                                        ['tagName' => 'span', 'style' => ['font-size' => '60px', 'font-weight' => '700', 'color' => '#9A7B4F', 'min-width' => '80px'], 'content' => '1'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h2', 'style' => ['font-size' => '24px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => 'Product Name - Best Overall'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.6'], 'content' => 'This top-rated product stands out for its exceptional quality and proven results. Users consistently report significant improvements within the first few weeks.'],
                                                ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'margin-top' => '15px', 'padding' => '10px 25px', 'background-color' => '#9A7B4F', 'color' => '#fff', 'border-radius' => '25px', 'text-decoration' => 'none'], 'content' => 'Learn More', 'attributes' => ['href' => '#']],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // List Item 2
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '40px 20px', 'background-color' => '#f8f5f0'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '30px', 'align-items' => 'center'],
                                    'components' => [
                                        ['tagName' => 'span', 'style' => ['font-size' => '60px', 'font-weight' => '700', 'color' => '#9A7B4F', 'min-width' => '80px'], 'content' => '2'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h2', 'style' => ['font-size' => '24px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => 'Product Name - Best Value'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.6'], 'content' => 'Offering incredible value for money, this product delivers premium results at an affordable price point. Perfect for those on a budget.'],
                                                ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'margin-top' => '15px', 'padding' => '10px 25px', 'background-color' => '#9A7B4F', 'color' => '#fff', 'border-radius' => '25px', 'text-decoration' => 'none'], 'content' => 'Learn More', 'attributes' => ['href' => '#']],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // List Item 3
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '40px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '30px', 'align-items' => 'center'],
                                    'components' => [
                                        ['tagName' => 'span', 'style' => ['font-size' => '60px', 'font-weight' => '700', 'color' => '#9A7B4F', 'min-width' => '80px'], 'content' => '3'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h2', 'style' => ['font-size' => '24px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => 'Product Name - Premium Choice'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.6'], 'content' => 'For those who want the absolute best, this premium option delivers unmatched quality and results. Worth every penny.'],
                                                ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'margin-top' => '15px', 'padding' => '10px 25px', 'background-color' => '#9A7B4F', 'color' => '#fff', 'border-radius' => '25px', 'text-decoration' => 'none'], 'content' => 'Learn More', 'attributes' => ['href' => '#']],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'styles' => [],
        ];
    }

    private function getLandingTemplate(): array
    {
        return [
            'components' => [
                [
                    'type' => 'wrapper',
                    'components' => [
                        // Hero Section
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '80px 20px', 'background' => 'linear-gradient(135deg, #f8f5f0 0%, #e8e0d5 100%)'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '1200px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '60px', 'align-items' => 'center'],
                                    'components' => [
                                        [
                                            'tagName' => 'div',
                                            'style' => ['flex' => '1'],
                                            'components' => [
                                                ['tagName' => 'h1', 'style' => ['font-size' => '48px', 'line-height' => '1.2', 'color' => '#333', 'margin-bottom' => '20px'], 'content' => 'Transform Your Results With Our Proven Solution'],
                                                ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => '#666', 'margin-bottom' => '30px', 'line-height' => '1.6'], 'content' => 'Join over 50,000 satisfied customers who have discovered the secret to achieving their goals faster than ever before.'],
                                                ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'padding' => '15px 40px', 'background-color' => '#9A7B4F', 'color' => '#fff', 'font-weight' => '600', 'border-radius' => '30px', 'text-decoration' => 'none', 'font-size' => '18px'], 'content' => 'Get Started Today', 'attributes' => ['href' => '#']],
                                            ],
                                        ],
                                        ['tagName' => 'img', 'style' => ['flex' => '1', 'max-width' => '500px', 'border-radius' => '12px'], 'attributes' => ['src' => 'https://placehold.co/500x400/9A7B4F/ffffff?text=Hero+Image']],
                                    ],
                                ],
                            ],
                        ],
                        // Benefits Section
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '80px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '1200px', 'margin' => '0 auto', 'text-align' => 'center'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '36px', 'color' => '#333', 'margin-bottom' => '50px'], 'content' => 'Why Choose Us?'],
                                        [
                                            'tagName' => 'div',
                                            'style' => ['display' => 'grid', 'grid-template-columns' => 'repeat(3, 1fr)', 'gap' => '40px'],
                                            'components' => [
                                                [
                                                    'tagName' => 'div',
                                                    'style' => ['padding' => '30px', 'background-color' => '#f8f5f0', 'border-radius' => '12px'],
                                                    'components' => [
                                                        ['tagName' => 'div', 'style' => ['width' => '60px', 'height' => '60px', 'background-color' => '#9A7B4F', 'border-radius' => '50%', 'margin' => '0 auto 20px', 'display' => 'flex', 'align-items' => 'center', 'justify-content' => 'center', 'color' => '#fff', 'font-size' => '24px'], 'content' => '1'],
                                                        ['tagName' => 'h3', 'style' => ['font-size' => '20px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => 'Fast Results'],
                                                        ['tagName' => 'p', 'style' => ['color' => '#666'], 'content' => 'See noticeable improvements in just 14 days'],
                                                    ],
                                                ],
                                                [
                                                    'tagName' => 'div',
                                                    'style' => ['padding' => '30px', 'background-color' => '#f8f5f0', 'border-radius' => '12px'],
                                                    'components' => [
                                                        ['tagName' => 'div', 'style' => ['width' => '60px', 'height' => '60px', 'background-color' => '#9A7B4F', 'border-radius' => '50%', 'margin' => '0 auto 20px', 'display' => 'flex', 'align-items' => 'center', 'justify-content' => 'center', 'color' => '#fff', 'font-size' => '24px'], 'content' => '2'],
                                                        ['tagName' => 'h3', 'style' => ['font-size' => '20px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => 'Premium Quality'],
                                                        ['tagName' => 'p', 'style' => ['color' => '#666'], 'content' => 'Made with the finest ingredients available'],
                                                    ],
                                                ],
                                                [
                                                    'tagName' => 'div',
                                                    'style' => ['padding' => '30px', 'background-color' => '#f8f5f0', 'border-radius' => '12px'],
                                                    'components' => [
                                                        ['tagName' => 'div', 'style' => ['width' => '60px', 'height' => '60px', 'background-color' => '#9A7B4F', 'border-radius' => '50%', 'margin' => '0 auto 20px', 'display' => 'flex', 'align-items' => 'center', 'justify-content' => 'center', 'color' => '#fff', 'font-size' => '24px'], 'content' => '3'],
                                                        ['tagName' => 'h3', 'style' => ['font-size' => '20px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => 'Money-Back Guarantee'],
                                                        ['tagName' => 'p', 'style' => ['color' => '#666'], 'content' => '30-day full refund if not satisfied'],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // CTA Section
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '80px 20px', 'background-color' => '#A67B5B', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '600px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '36px', 'color' => '#fff', 'margin-bottom' => '20px'], 'content' => 'Ready to Get Started?'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => 'rgba(255,255,255,0.9)', 'margin-bottom' => '30px'], 'content' => 'Join thousands of satisfied customers today. Risk-free with our 30-day guarantee.'],
                                        ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'padding' => '18px 50px', 'background-color' => '#fff', 'color' => '#9A7B4F', 'font-weight' => '600', 'border-radius' => '30px', 'text-decoration' => 'none', 'font-size' => '18px'], 'content' => 'Claim Your Offer Now', 'attributes' => ['href' => '#']],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'styles' => [],
        ];
    }

    private function getProblemSolutionTemplate(): array
    {
        return [
            'components' => [
                [
                    'type' => 'wrapper',
                    'components' => [
                        // Problem Section
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#1a1a2e', 'color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto', 'text-align' => 'center'],
                                    'components' => [
                                        ['tagName' => 'p', 'style' => ['color' => '#e94560', 'font-weight' => '600', 'margin-bottom' => '15px'], 'content' => 'THE PROBLEM'],
                                        ['tagName' => 'h1', 'style' => ['font-size' => '42px', 'line-height' => '1.2', 'margin-bottom' => '20px'], 'content' => 'Are You Still Struggling With [Pain Point]?'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => 'rgba(255,255,255,0.8)', 'line-height' => '1.8'], 'content' => 'You\'ve tried everything. The expensive solutions. The time-consuming methods. Nothing seems to work. Sound familiar?'],
                                    ],
                                ],
                            ],
                        ],
                        // Pain Points List
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '28px', 'color' => '#333', 'margin-bottom' => '30px', 'text-align' => 'center'], 'content' => 'If you\'re experiencing any of these...'],
                                        ['tagName' => 'div', 'style' => ['display' => 'flex', 'align-items' => 'center', 'gap' => '15px', 'padding' => '15px', 'background' => '#fff', 'border-radius' => '8px', 'margin-bottom' => '15px', 'border-left' => '4px solid #e94560'], 'components' => [['tagName' => 'span', 'content' => '❌ Frustration with slow or no results']]],
                                        ['tagName' => 'div', 'style' => ['display' => 'flex', 'align-items' => 'center', 'gap' => '15px', 'padding' => '15px', 'background' => '#fff', 'border-radius' => '8px', 'margin-bottom' => '15px', 'border-left' => '4px solid #e94560'], 'components' => [['tagName' => 'span', 'content' => '❌ Wasting money on solutions that don\'t work']]],
                                        ['tagName' => 'div', 'style' => ['display' => 'flex', 'align-items' => 'center', 'gap' => '15px', 'padding' => '15px', 'background' => '#fff', 'border-radius' => '8px', 'margin-bottom' => '15px', 'border-left' => '4px solid #e94560'], 'components' => [['tagName' => 'span', 'content' => '❌ Feeling overwhelmed by conflicting advice']]],
                                    ],
                                ],
                            ],
                        ],
                        // Solution Reveal
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto', 'text-align' => 'center'],
                                    'components' => [
                                        ['tagName' => 'p', 'style' => ['color' => '#10b981', 'font-weight' => '600', 'margin-bottom' => '15px'], 'content' => 'THE SOLUTION'],
                                        ['tagName' => 'h2', 'style' => ['font-size' => '36px', 'color' => '#333', 'margin-bottom' => '20px'], 'content' => 'Introducing [Product Name]'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => '#666', 'margin-bottom' => '30px'], 'content' => 'A revolutionary approach that\'s helping thousands achieve results in weeks, not months.'],
                                        ['tagName' => 'img', 'style' => ['max-width' => '400px', 'margin' => '0 auto', 'border-radius' => '12px'], 'attributes' => ['src' => 'https://placehold.co/400x400/9A7B4F/ffffff?text=Product']],
                                    ],
                                ],
                            ],
                        ],
                        // Benefits
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#f0fdf4'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'div', 'style' => ['display' => 'flex', 'align-items' => 'center', 'gap' => '15px', 'padding' => '15px', 'background' => '#fff', 'border-radius' => '8px', 'margin-bottom' => '15px', 'border-left' => '4px solid #10b981'], 'components' => [['tagName' => 'span', 'content' => '✓ See visible results in just 14 days']]],
                                        ['tagName' => 'div', 'style' => ['display' => 'flex', 'align-items' => 'center', 'gap' => '15px', 'padding' => '15px', 'background' => '#fff', 'border-radius' => '8px', 'margin-bottom' => '15px', 'border-left' => '4px solid #10b981'], 'components' => [['tagName' => 'span', 'content' => '✓ Backed by clinical research']]],
                                        ['tagName' => 'div', 'style' => ['display' => 'flex', 'align-items' => 'center', 'gap' => '15px', 'padding' => '15px', 'background' => '#fff', 'border-radius' => '8px', 'margin-bottom' => '15px', 'border-left' => '4px solid #10b981'], 'components' => [['tagName' => 'span', 'content' => '✓ 100% money-back guarantee']]],
                                    ],
                                ],
                            ],
                        ],
                        // CTA
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#9A7B4F', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '600px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '32px', 'color' => '#fff', 'margin-bottom' => '15px'], 'content' => 'Ready to Solve Your Problem?'],
                                        ['tagName' => 'p', 'style' => ['color' => 'rgba(255,255,255,0.9)', 'margin-bottom' => '25px'], 'content' => 'Join 10,000+ satisfied customers. Limited time: 40% OFF'],
                                        ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'padding' => '18px 50px', 'background-color' => '#fff', 'color' => '#9A7B4F', 'font-weight' => '700', 'border-radius' => '30px', 'text-decoration' => 'none', 'font-size' => '18px'], 'content' => 'Get 40% Off Now →', 'attributes' => ['href' => '#']],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'styles' => [],
        ];
    }

    private function get5ReasonsTemplate(): array
    {
        return [
            'components' => [
                [
                    'type' => 'wrapper',
                    'components' => [
                        // Hero with Offer
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'span', 'style' => ['display' => 'inline-block', 'padding' => '8px 20px', 'background' => 'rgba(255,255,255,0.2)', 'border-radius' => '20px', 'color' => '#fff', 'font-size' => '14px', 'margin-bottom' => '20px'], 'content' => '🎁 SPECIAL OFFER: $20 OFF YOUR FIRST ORDER'],
                                        ['tagName' => 'h1', 'style' => ['font-size' => '42px', 'line-height' => '1.2', 'color' => '#fff', 'margin-bottom' => '20px'], 'content' => '5 Reasons Why [Product] Is Taking Over in 2024'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => 'rgba(255,255,255,0.9)'], 'content' => 'Discover why millions are making the switch'],
                                    ],
                                ],
                            ],
                        ],
                        // Reason 1
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '50px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '30px', 'align-items' => 'flex-start'],
                                    'components' => [
                                        ['tagName' => 'div', 'style' => ['width' => '60px', 'height' => '60px', 'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'border-radius' => '50%', 'display' => 'flex', 'align-items' => 'center', 'justify-content' => 'center', 'color' => '#fff', 'font-size' => '24px', 'font-weight' => '700', 'flex-shrink' => '0'], 'content' => '1'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h2', 'style' => ['font-size' => '24px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => 'It Actually Works (Backed by Science)'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.8'], 'content' => 'Unlike other products that make empty promises, [Product] is backed by 15+ clinical studies showing a 94% success rate. Real results, not marketing hype.'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Reason 2
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '50px 20px', 'background-color' => '#f8f5f0'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '30px', 'align-items' => 'flex-start'],
                                    'components' => [
                                        ['tagName' => 'div', 'style' => ['width' => '60px', 'height' => '60px', 'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'border-radius' => '50%', 'display' => 'flex', 'align-items' => 'center', 'justify-content' => 'center', 'color' => '#fff', 'font-size' => '24px', 'font-weight' => '700', 'flex-shrink' => '0'], 'content' => '2'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h2', 'style' => ['font-size' => '24px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => 'Incredibly Easy to Use'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.8'], 'content' => 'No complicated routines or confusing instructions. Just 2 minutes a day is all it takes. Perfect for busy lifestyles.'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Reason 3
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '50px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '30px', 'align-items' => 'flex-start'],
                                    'components' => [
                                        ['tagName' => 'div', 'style' => ['width' => '60px', 'height' => '60px', 'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'border-radius' => '50%', 'display' => 'flex', 'align-items' => 'center', 'justify-content' => 'center', 'color' => '#fff', 'font-size' => '24px', 'font-weight' => '700', 'flex-shrink' => '0'], 'content' => '3'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h2', 'style' => ['font-size' => '24px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => 'Premium Ingredients, Fair Price'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.8'], 'content' => 'We source only the highest quality ingredients. And because we sell direct, you get premium quality at half the price of competitors.'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Reason 4
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '50px 20px', 'background-color' => '#f8f5f0'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '30px', 'align-items' => 'flex-start'],
                                    'components' => [
                                        ['tagName' => 'div', 'style' => ['width' => '60px', 'height' => '60px', 'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'border-radius' => '50%', 'display' => 'flex', 'align-items' => 'center', 'justify-content' => 'center', 'color' => '#fff', 'font-size' => '24px', 'font-weight' => '700', 'flex-shrink' => '0'], 'content' => '4'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h2', 'style' => ['font-size' => '24px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => '50,000+ 5-Star Reviews'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.8'], 'content' => 'Don\'t just take our word for it. Join the community of raving fans who\'ve transformed their lives with [Product].'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Reason 5
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '50px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '30px', 'align-items' => 'flex-start'],
                                    'components' => [
                                        ['tagName' => 'div', 'style' => ['width' => '60px', 'height' => '60px', 'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'border-radius' => '50%', 'display' => 'flex', 'align-items' => 'center', 'justify-content' => 'center', 'color' => '#fff', 'font-size' => '24px', 'font-weight' => '700', 'flex-shrink' => '0'], 'content' => '5'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h2', 'style' => ['font-size' => '24px', 'color' => '#333', 'margin-bottom' => '10px'], 'content' => 'Risk-Free Guarantee'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.8'], 'content' => 'Try it for 60 days. If you\'re not completely satisfied, we\'ll refund every penny. No questions asked. No hassle.'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // CTA
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '600px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '32px', 'color' => '#fff', 'margin-bottom' => '15px'], 'content' => 'Ready to Experience the Difference?'],
                                        ['tagName' => 'p', 'style' => ['color' => 'rgba(255,255,255,0.9)', 'margin-bottom' => '25px', 'font-size' => '18px'], 'content' => 'Use code SAVE20 for $20 off your first order'],
                                        ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'padding' => '18px 50px', 'background-color' => '#fff', 'color' => '#764ba2', 'font-weight' => '700', 'border-radius' => '30px', 'text-decoration' => 'none', 'font-size' => '18px'], 'content' => 'Claim Your $20 Off →', 'attributes' => ['href' => '#']],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'styles' => [],
        ];
    }

    private function getBeforeAfterTemplate(): array
    {
        return [
            'components' => [
                [
                    'type' => 'wrapper',
                    'components' => [
                        // Hero
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'p', 'style' => ['color' => '#9A7B4F', 'font-weight' => '600', 'margin-bottom' => '15px'], 'content' => 'REAL CUSTOMER STORY'],
                                        ['tagName' => 'h1', 'style' => ['font-size' => '42px', 'line-height' => '1.2', 'color' => '#333', 'margin-bottom' => '20px'], 'content' => '"How I Finally [Achieved Goal] After Years of Trying"'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => '#666'], 'content' => 'Sarah\'s incredible transformation story'],
                                    ],
                                ],
                            ],
                        ],
                        // Before/After Images
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '900px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '30px', 'justify-content' => 'center'],
                                    'components' => [
                                        [
                                            'tagName' => 'div',
                                            'style' => ['text-align' => 'center'],
                                            'components' => [
                                                ['tagName' => 'img', 'style' => ['width' => '300px', 'height' => '300px', 'object-fit' => 'cover', 'border-radius' => '12px', 'border' => '4px solid #e5e5e5'], 'attributes' => ['src' => 'https://placehold.co/300x300/e5e5e5/666666?text=BEFORE']],
                                                ['tagName' => 'p', 'style' => ['margin-top' => '15px', 'font-weight' => '600', 'color' => '#666'], 'content' => 'BEFORE'],
                                            ],
                                        ],
                                        [
                                            'tagName' => 'div',
                                            'style' => ['text-align' => 'center'],
                                            'components' => [
                                                ['tagName' => 'img', 'style' => ['width' => '300px', 'height' => '300px', 'object-fit' => 'cover', 'border-radius' => '12px', 'border' => '4px solid #10b981'], 'attributes' => ['src' => 'https://placehold.co/300x300/10b981/ffffff?text=AFTER']],
                                                ['tagName' => 'p', 'style' => ['margin-top' => '15px', 'font-weight' => '600', 'color' => '#10b981'], 'content' => 'AFTER - 8 WEEKS'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Story
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#f8f5f0'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '700px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '28px', 'color' => '#333', 'margin-bottom' => '20px'], 'content' => 'My Journey'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '20px'], 'content' => '"For years, I struggled with [problem]. I tried everything - expensive treatments, countless products, different routines. Nothing worked. I was ready to give up."'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'line-height' => '1.8', 'color' => '#444', 'margin-bottom' => '20px'], 'content' => '"Then a friend recommended [Product]. I was skeptical at first - I\'d been burned before. But I decided to give it one more shot."'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'line-height' => '1.8', 'color' => '#444', 'font-weight' => '600'], 'content' => '"Within 2 weeks, I noticed a difference. By week 8, I couldn\'t believe the transformation. This product changed my life."'],
                                    ],
                                ],
                            ],
                        ],
                        // Results Stats
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '900px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '40px', 'justify-content' => 'center', 'text-align' => 'center'],
                                    'components' => [
                                        ['tagName' => 'div', 'components' => [['tagName' => 'p', 'style' => ['font-size' => '48px', 'font-weight' => '700', 'color' => '#9A7B4F'], 'content' => '94%'], ['tagName' => 'p', 'style' => ['color' => '#666'], 'content' => 'Saw Results']]],
                                        ['tagName' => 'div', 'components' => [['tagName' => 'p', 'style' => ['font-size' => '48px', 'font-weight' => '700', 'color' => '#9A7B4F'], 'content' => '8'], ['tagName' => 'p', 'style' => ['color' => '#666'], 'content' => 'Week Average']]],
                                        ['tagName' => 'div', 'components' => [['tagName' => 'p', 'style' => ['font-size' => '48px', 'font-weight' => '700', 'color' => '#9A7B4F'], 'content' => '50K+'], ['tagName' => 'p', 'style' => ['color' => '#666'], 'content' => 'Happy Customers']]],
                                    ],
                                ],
                            ],
                        ],
                        // CTA
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#A67B5B', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '600px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '32px', 'color' => '#fff', 'margin-bottom' => '15px'], 'content' => 'Start Your Transformation Today'],
                                        ['tagName' => 'p', 'style' => ['color' => 'rgba(255,255,255,0.9)', 'margin-bottom' => '25px'], 'content' => 'Join Sarah and thousands of others. 60-day money-back guarantee.'],
                                        ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'padding' => '18px 50px', 'background-color' => '#fff', 'color' => '#9A7B4F', 'font-weight' => '700', 'border-radius' => '30px', 'text-decoration' => 'none', 'font-size' => '18px'], 'content' => 'Begin My Transformation →', 'attributes' => ['href' => '#']],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'styles' => [],
        ];
    }

    private function getComparisonTemplate(): array
    {
        return [
            'components' => [
                [
                    'type' => 'wrapper',
                    'components' => [
                        // Hero
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#1a1a2e', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'p', 'style' => ['color' => '#fbbf24', 'font-weight' => '600', 'margin-bottom' => '15px'], 'content' => 'EXPERT REVIEW 2024'],
                                        ['tagName' => 'h1', 'style' => ['font-size' => '42px', 'line-height' => '1.2', 'color' => '#fff', 'margin-bottom' => '20px'], 'content' => 'We Tested 12 Popular [Products] - Here\'s The Clear Winner'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => 'rgba(255,255,255,0.8)'], 'content' => 'Our team spent 6 months testing every major brand. The results may surprise you.'],
                                    ],
                                ],
                            ],
                        ],
                        // Comparison Table
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '900px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '28px', 'color' => '#333', 'margin-bottom' => '30px', 'text-align' => 'center'], 'content' => 'Head-to-Head Comparison'],
                                        [
                                            'tagName' => 'div',
                                            'style' => ['overflow-x' => 'auto'],
                                            'components' => [
                                                [
                                                    'tagName' => 'table',
                                                    'style' => ['width' => '100%', 'border-collapse' => 'collapse'],
                                                    'components' => [
                                                        ['tagName' => 'tr', 'style' => ['background-color' => '#f8f5f0'], 'components' => [['tagName' => 'th', 'style' => ['padding' => '15px', 'text-align' => 'left', 'border-bottom' => '2px solid #ddd'], 'content' => 'Feature'], ['tagName' => 'th', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '2px solid #ddd', 'background-color' => '#10b981', 'color' => '#fff'], 'content' => 'Our Pick ⭐'], ['tagName' => 'th', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '2px solid #ddd'], 'content' => 'Brand B'], ['tagName' => 'th', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '2px solid #ddd'], 'content' => 'Brand C']]],
                                                        ['tagName' => 'tr', 'components' => [['tagName' => 'td', 'style' => ['padding' => '15px', 'border-bottom' => '1px solid #eee'], 'content' => 'Effectiveness'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee', 'color' => '#10b981', 'font-weight' => '600'], 'content' => '⭐⭐⭐⭐⭐'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee'], 'content' => '⭐⭐⭐'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee'], 'content' => '⭐⭐⭐⭐']]],
                                                        ['tagName' => 'tr', 'components' => [['tagName' => 'td', 'style' => ['padding' => '15px', 'border-bottom' => '1px solid #eee'], 'content' => 'Value for Money'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee', 'color' => '#10b981', 'font-weight' => '600'], 'content' => '⭐⭐⭐⭐⭐'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee'], 'content' => '⭐⭐'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee'], 'content' => '⭐⭐⭐']]],
                                                        ['tagName' => 'tr', 'components' => [['tagName' => 'td', 'style' => ['padding' => '15px', 'border-bottom' => '1px solid #eee'], 'content' => 'Customer Support'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee', 'color' => '#10b981', 'font-weight' => '600'], 'content' => '⭐⭐⭐⭐⭐'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee'], 'content' => '⭐⭐⭐'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee'], 'content' => '⭐⭐⭐']]],
                                                        ['tagName' => 'tr', 'components' => [['tagName' => 'td', 'style' => ['padding' => '15px', 'border-bottom' => '1px solid #eee'], 'content' => 'Money-Back Guarantee'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee', 'color' => '#10b981', 'font-weight' => '600'], 'content' => '60 Days'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee'], 'content' => '14 Days'], ['tagName' => 'td', 'style' => ['padding' => '15px', 'text-align' => 'center', 'border-bottom' => '1px solid #eee'], 'content' => '30 Days']]],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Winner Callout
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#f0fdf4'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '700px', 'margin' => '0 auto', 'text-align' => 'center', 'padding' => '40px', 'background' => '#fff', 'border-radius' => '16px', 'border' => '3px solid #10b981'],
                                    'components' => [
                                        ['tagName' => 'p', 'style' => ['color' => '#10b981', 'font-weight' => '700', 'font-size' => '14px', 'margin-bottom' => '10px'], 'content' => '🏆 EDITOR\'S CHOICE'],
                                        ['tagName' => 'h2', 'style' => ['font-size' => '32px', 'color' => '#333', 'margin-bottom' => '15px'], 'content' => '[Product Name] Takes The Crown'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => '#666', 'line-height' => '1.6'], 'content' => 'After extensive testing, [Product] emerged as the clear winner. Superior effectiveness, best value, and unmatched customer support make it our #1 recommendation.'],
                                    ],
                                ],
                            ],
                        ],
                        // CTA
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background-color' => '#1a1a2e', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '600px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '32px', 'color' => '#fff', 'margin-bottom' => '15px'], 'content' => 'Try The #1 Rated [Product]'],
                                        ['tagName' => 'p', 'style' => ['color' => 'rgba(255,255,255,0.8)', 'margin-bottom' => '25px'], 'content' => 'Exclusive reader discount: 35% off + free shipping'],
                                        ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'padding' => '18px 50px', 'background-color' => '#fbbf24', 'color' => '#1a1a2e', 'font-weight' => '700', 'border-radius' => '30px', 'text-decoration' => 'none', 'font-size' => '18px'], 'content' => 'Get 35% Off Now →', 'attributes' => ['href' => '#']],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'styles' => [],
        ];
    }

    private function getQuickTipsTemplate(): array
    {
        return [
            'components' => [
                [
                    'type' => 'wrapper',
                    'components' => [
                        // Hero
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '800px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h1', 'style' => ['font-size' => '42px', 'line-height' => '1.2', 'color' => '#fff', 'margin-bottom' => '20px'], 'content' => '7 Quick Tips That Will [Transform Your Results]'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => 'rgba(255,255,255,0.9)'], 'content' => 'Simple changes you can make today for better results tomorrow'],
                                    ],
                                ],
                            ],
                        ],
                        // Tip 1
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '40px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '700px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '20px', 'align-items' => 'flex-start'],
                                    'components' => [
                                        ['tagName' => 'span', 'style' => ['font-size' => '40px'], 'content' => '💡'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h3', 'style' => ['font-size' => '20px', 'color' => '#333', 'margin-bottom' => '8px'], 'content' => 'Tip #1: Start Small'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.6'], 'content' => 'Don\'t try to change everything at once. Pick one area to focus on and master it before moving to the next.'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Tip 2
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '40px 20px', 'background-color' => '#fdf2f8'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '700px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '20px', 'align-items' => 'flex-start'],
                                    'components' => [
                                        ['tagName' => 'span', 'style' => ['font-size' => '40px'], 'content' => '⏰'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h3', 'style' => ['font-size' => '20px', 'color' => '#333', 'margin-bottom' => '8px'], 'content' => 'Tip #2: Consistency Over Intensity'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.6'], 'content' => '10 minutes daily beats 2 hours once a week. Build habits that stick.'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Tip 3
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '40px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '700px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '20px', 'align-items' => 'flex-start'],
                                    'components' => [
                                        ['tagName' => 'span', 'style' => ['font-size' => '40px'], 'content' => '📊'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h3', 'style' => ['font-size' => '20px', 'color' => '#333', 'margin-bottom' => '8px'], 'content' => 'Tip #3: Track Your Progress'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.6'], 'content' => 'What gets measured gets improved. Keep a simple log to stay motivated.'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Tip 4
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '40px 20px', 'background-color' => '#fdf2f8'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '700px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '20px', 'align-items' => 'flex-start'],
                                    'components' => [
                                        ['tagName' => 'span', 'style' => ['font-size' => '40px'], 'content' => '🎯'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h3', 'style' => ['font-size' => '20px', 'color' => '#333', 'margin-bottom' => '8px'], 'content' => 'Tip #4: Set Clear Goals'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.6'], 'content' => 'Vague goals get vague results. Be specific about what you want to achieve.'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Tip 5
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '40px 20px', 'background-color' => '#fff'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '700px', 'margin' => '0 auto', 'display' => 'flex', 'gap' => '20px', 'align-items' => 'flex-start'],
                                    'components' => [
                                        ['tagName' => 'span', 'style' => ['font-size' => '40px'], 'content' => '🛠️'],
                                        [
                                            'tagName' => 'div',
                                            'components' => [
                                                ['tagName' => 'h3', 'style' => ['font-size' => '20px', 'color' => '#333', 'margin-bottom' => '8px'], 'content' => 'Tip #5: Use The Right Tools'],
                                                ['tagName' => 'p', 'style' => ['color' => '#666', 'line-height' => '1.6'], 'content' => 'Having the right tools makes everything easier. Invest in quality.'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Product Callout
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '40px 20px', 'background-color' => '#fff3cd', 'border' => '2px dashed #fbbf24'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '700px', 'margin' => '0 auto', 'text-align' => 'center'],
                                    'components' => [
                                        ['tagName' => 'p', 'style' => ['font-weight' => '600', 'color' => '#92400e', 'margin-bottom' => '10px'], 'content' => '💎 PRO TIP'],
                                        ['tagName' => 'p', 'style' => ['font-size' => '18px', 'color' => '#333'], 'content' => 'Want to accelerate your results? [Product Name] is the tool thousands are using to get results 3x faster.'],
                                        ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'margin-top' => '15px', 'padding' => '12px 30px', 'background-color' => '#f5576c', 'color' => '#fff', 'font-weight' => '600', 'border-radius' => '25px', 'text-decoration' => 'none'], 'content' => 'Learn More →', 'attributes' => ['href' => '#']],
                                    ],
                                ],
                            ],
                        ],
                        // CTA
                        [
                            'tagName' => 'section',
                            'style' => ['padding' => '60px 20px', 'background' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', 'text-align' => 'center'],
                            'components' => [
                                [
                                    'tagName' => 'div',
                                    'style' => ['max-width' => '600px', 'margin' => '0 auto'],
                                    'components' => [
                                        ['tagName' => 'h2', 'style' => ['font-size' => '32px', 'color' => '#fff', 'margin-bottom' => '15px'], 'content' => 'Ready to Put These Tips Into Action?'],
                                        ['tagName' => 'p', 'style' => ['color' => 'rgba(255,255,255,0.9)', 'margin-bottom' => '25px'], 'content' => 'Get our free guide with 20 more tips + exclusive discount'],
                                        ['tagName' => 'a', 'style' => ['display' => 'inline-block', 'padding' => '18px 50px', 'background-color' => '#fff', 'color' => '#f5576c', 'font-weight' => '700', 'border-radius' => '30px', 'text-decoration' => 'none', 'font-size' => '18px'], 'content' => 'Download Free Guide →', 'attributes' => ['href' => '#']],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'styles' => [],
        ];
    }
}
