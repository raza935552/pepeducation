<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Seeds all CMS pages from the exported JSON data file.
 * JSON file: database/seeders/data/pages-export.json
 *
 * Usage: php artisan db:seed --class=PagesDataSeeder
 */
class PagesDataSeeder extends Seeder
{
    public function run(): void
    {
        $path = __DIR__ . '/data/pages-export.json';

        if (! file_exists($path)) {
            $this->command->error('Missing data file: database/seeders/data/pages-export.json');
            $this->command->info('Run: php storage/app/export-pages.php to generate it.');
            return;
        }

        $pages = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Invalid JSON: ' . json_last_error_msg());
            return;
        }

        $count = 0;

        foreach ($pages as $page) {
            // Skip test/draft pages
            if ($page['status'] === 'draft') {
                $this->command->info("Skipping draft: {$page['title']}");
                continue;
            }

            Page::updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title'            => $page['title'],
                    'content'          => $page['content'],
                    'html'             => $page['html'],
                    'css'              => $page['css'],
                    'meta_title'       => $page['meta_title'],
                    'meta_description' => $page['meta_description'],
                    'featured_image'   => $page['featured_image'],
                    'status'           => $page['status'],
                    'template'         => $page['template'],
                    'variant_of'       => $page['variant_of'],
                    'variant_weight'   => $page['variant_weight'] ?? 50,
                    'published_at'     => $page['published_at'],
                ]
            );

            $count++;
            $this->command->info("Seeded: {$page['title']}");
        }

        $this->command->info("Done — {$count} pages seeded.");
    }
}
