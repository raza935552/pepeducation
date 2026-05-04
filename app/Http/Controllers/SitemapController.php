<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Peptide;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember('sitemap_xml', 3600, function () {
            return $this->buildSitemap();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    private function buildSitemap(): string
    {
        // Force APP_URL so routes generate correct domain regardless of request context
        \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
        \Illuminate\Support\Facades\URL::forceScheme('https');

        $urls = collect();

        // Homepage — highest priority
        $urls->push([
            'loc' => route('home'),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ]);

        // Peptide index
        $urls->push([
            'loc' => route('peptides.index'),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ]);

        // Individual peptide pages
        Peptide::published()
            ->select('slug', 'updated_at')
            ->orderBy('name')
            ->chunk(200, function ($peptides) use (&$urls) {
                foreach ($peptides as $peptide) {
                    $urls->push([
                        'loc' => route('peptides.show', $peptide->slug),
                        'lastmod' => $peptide->updated_at->toW3cString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ]);
                }
            });

        // Blog index
        $urls->push([
            'loc' => route('blog.index'),
            'changefreq' => 'daily',
            'priority' => '0.8',
        ]);

        // Blog posts (with image entries when a featured image exists)
        BlogPost::published()
            ->select('id', 'title', 'slug', 'featured_image', 'published_at', 'updated_at')
            ->latest('published_at')
            ->chunk(200, function ($posts) use (&$urls) {
                foreach ($posts as $post) {
                    $entry = [
                        'loc' => route('blog.show', $post->slug),
                        'lastmod' => $post->updated_at->toW3cString(),
                        'changefreq' => 'monthly',
                        'priority' => '0.7',
                    ];
                    if (!empty($post->featured_image)) {
                        $img = \Illuminate\Support\Str::startsWith($post->featured_image, ['http://','https://'])
                            ? $post->featured_image
                            : url($post->featured_image);
                        $entry['image'] = [
                            'loc' => $img,
                            'title' => $post->title,
                        ];
                    }
                    $urls->push($entry);
                }
            });

        // Blog categories
        BlogCategory::orderBy('name')
            ->each(function ($cat) use (&$urls) {
                $urls->push([
                    'loc' => route('blog.category', $cat->slug),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ]);
            });

        // Static pages
        foreach (['about', 'faq', 'disclaimer', 'privacy', 'terms'] as $page) {
            if (\Illuminate\Support\Facades\Route::has($page)) {
                $urls->push([
                    'loc' => route($page),
                    'changefreq' => 'monthly',
                    'priority' => '0.4',
                ]);
            }
        }

        // Dynamic CMS pages
        Page::where('status', 'published')
            ->whereNull('variant_of')
            ->select('slug', 'updated_at')
            ->each(function ($page) use (&$urls) {
                $urls->push([
                    'loc' => route('page.show', $page->slug),
                    'lastmod' => $page->updated_at->toW3cString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.5',
                ]);
            });

        // Calculator
        $urls->push([
            'loc' => route('calculator'),
            'changefreq' => 'monthly',
            'priority' => '0.6',
        ]);

        // Author pages
        \App\Models\User::where('is_public_author', true)
            ->whereNotNull('slug')
            ->select('slug', 'updated_at')
            ->each(function ($author) use (&$urls) {
                $urls->push([
                    'loc' => route('author.show', $author->slug),
                    'lastmod' => $author->updated_at?->toW3cString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ]);
            });

        // Build XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . '</loc>' . "\n";
            if (!empty($url['lastmod'])) {
                $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            }
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            if (!empty($url['image']['loc'])) {
                $xml .= '    <image:image>' . "\n";
                $xml .= '      <image:loc>' . htmlspecialchars($url['image']['loc'], ENT_XML1) . '</image:loc>' . "\n";
                if (!empty($url['image']['title'])) {
                    $xml .= '      <image:title>' . htmlspecialchars($url['image']['title'], ENT_XML1) . '</image:title>' . "\n";
                }
                $xml .= '    </image:image>' . "\n";
            }
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
