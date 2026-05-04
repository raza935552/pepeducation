<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Peptide;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ValidateSchemasCommand extends Command
{
    protected $signature = 'schema:validate {--limit=999 : Max URLs to validate per type} {--type= : Restrict to one type (peptide|blog|author|homepage|compare|stack)}';

    protected $description = 'Fetches public pages, extracts JSON-LD blocks, and validates them against common rules.';

    private int $totalSchemas = 0;
    private int $totalIssues = 0;
    private array $issuesByType = [];

    public function handle(): int
    {
        $type = $this->option('type');
        $limit = (int) $this->option('limit');

        $urls = $this->buildUrlList($type, $limit);

        $this->info('Validating '.count($urls).' URLs...');
        $this->newLine();

        foreach ($urls as $label => $url) {
            $this->validateUrl($label, $url);
        }

        $this->newLine();
        $this->info('=== SUMMARY ===');
        $this->line('Schemas examined: '.$this->totalSchemas);
        $this->line('Total issues:     '.$this->totalIssues);

        if (!empty($this->issuesByType)) {
            $this->newLine();
            $this->info('Issues by schema type:');
            foreach ($this->issuesByType as $schemaType => $count) {
                $this->line('  '.$schemaType.': '.$count);
            }
        }

        return self::SUCCESS;
    }

    private function buildUrlList(?string $type, int $limit): array
    {
        $urls = [];

        if (!$type || $type === 'homepage') {
            $urls['Homepage'] = url('/');
            $urls['Peptides Index'] = route('peptides.index');
            $urls['Blog Index'] = route('blog.index');
        }

        if (!$type || $type === 'peptide') {
            Peptide::published()->take($limit)->each(function ($p) use (&$urls) {
                $urls['Peptide: '.$p->name] = route('peptides.show', $p->slug);
            });
        }

        if (!$type || $type === 'blog') {
            BlogPost::published()->take($limit)->each(function ($p) use (&$urls) {
                $urls['Blog: '.\Illuminate\Support\Str::limit($p->title, 40)] = route('blog.show', $p->slug);
            });
        }

        if (!$type || $type === 'author') {
            User::where('is_public_author', true)->take($limit)->each(function ($u) use (&$urls) {
                if ($u->slug) {
                    $urls['Author: '.$u->name] = route('author.show', $u->slug);
                }
            });
        }

        if (!$type || $type === 'compare') {
            $urls['Compare: tirzepatide vs semaglutide'] = route('peptides.compare.pair', ['slugA' => 'tirzepatide', 'slugB' => 'semaglutide']);
            $urls['Compare: bpc-157 vs tb-500'] = route('peptides.compare.pair', ['slugA' => 'bpc-157', 'slugB' => 'tb-500']);
        }

        if (!$type || $type === 'stack') {
            $urls['Stack Builder'] = route('stack-builder');
        }

        return $urls;
    }

    private function validateUrl(string $label, string $url): void
    {
        try {
            $response = Http::timeout(10)->get($url);
            if (!$response->successful()) {
                $this->error('HTTP '.$response->status().' on '.$label.' ('.$url.')');
                return;
            }
            $html = $response->body();
        } catch (\Throwable $e) {
            $this->error('Fetch failed: '.$label.' - '.$e->getMessage());
            return;
        }

        // Extract all JSON-LD blocks
        if (!preg_match_all('/<script type="application\/ld\+json">\s*(.+?)\s*<\/script>/s', $html, $matches)) {
            $this->warn('  No JSON-LD found in '.$label);
            return;
        }

        $issues = [];
        foreach ($matches[1] as $i => $jsonString) {
            $this->totalSchemas++;
            $jsonString = trim($jsonString);

            $data = json_decode($jsonString, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $issues[] = '[block #'.($i+1).'] Invalid JSON: '.json_last_error_msg();
                $this->totalIssues++;
                continue;
            }

            $schemaType = $data['@type'] ?? '(unknown)';
            $blockIssues = $this->validateSchema($data, $url);

            foreach ($blockIssues as $issue) {
                $issues[] = '['.$schemaType.'] '.$issue;
                $this->totalIssues++;
                $this->issuesByType[$schemaType] = ($this->issuesByType[$schemaType] ?? 0) + 1;
            }
        }

        if (empty($issues)) {
            $this->line('  ✓ '.$label);
        } else {
            $this->error('  ✗ '.$label);
            foreach ($issues as $issue) {
                $this->line('     - '.$issue);
            }
        }
    }

    private function validateSchema(array $data, string $url): array
    {
        $issues = [];
        $type = $data['@type'] ?? null;

        // Universal checks
        if (!isset($data['@context'])) {
            $issues[] = 'Missing @context';
        } elseif (!is_string($data['@context']) || !str_contains($data['@context'], 'schema.org')) {
            $issues[] = 'Invalid @context: '.json_encode($data['@context']);
        }

        if (!$type) {
            $issues[] = 'Missing @type';
            return $issues;
        }

        // Recursively scan for common issues (null values, "..." truncations, broken URLs)
        $this->scanRecursive($data, $issues, '');

        // Type-specific required fields
        $requiredByType = [
            'Article'           => ['headline', 'author', 'datePublished'],
            'LearningResource'  => ['name', 'description'],
            'HowTo'             => ['name', 'step'],
            'FAQPage'           => ['mainEntity'],
            'WebSite'           => ['name', 'url'],
            'Organization'      => ['name', 'url'],
            'Person'            => ['name'],
            'BreadcrumbList'    => ['itemListElement'],
            'WebPage'           => ['name', 'url'],
            'CollectionPage'    => ['name', 'url'],
            'MedicalWebPage'    => ['name', 'url'],
        ];

        if (isset($requiredByType[$type])) {
            foreach ($requiredByType[$type] as $field) {
                if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '' || $data[$field] === []) {
                    $issues[] = 'Missing required field: '.$field;
                }
            }
        }

        // HowTo specific - need at least 2 steps
        if ($type === 'HowTo' && isset($data['step']) && is_array($data['step'])) {
            if (count($data['step']) < 2) {
                $issues[] = 'HowTo has fewer than 2 steps (has '.count($data['step']).')';
            }
        }

        // BreadcrumbList - check positions are sequential
        if ($type === 'BreadcrumbList' && isset($data['itemListElement']) && is_array($data['itemListElement'])) {
            foreach ($data['itemListElement'] as $i => $crumb) {
                $expectedPos = $i + 1;
                if (($crumb['position'] ?? null) !== $expectedPos) {
                    $issues[] = 'BreadcrumbList position out of sequence at index '.$i;
                }
            }
        }

        // Date format checks
        foreach (['datePublished', 'dateModified'] as $dateField) {
            if (!empty($data[$dateField]) && is_string($data[$dateField])) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $data[$dateField])) {
                    $issues[] = $dateField.' not ISO 8601: '.$data[$dateField];
                }
            }
        }

        return $issues;
    }

    private function scanRecursive($data, array &$issues, string $path): void
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $childPath = $path === '' ? (string) $k : $path.'.'.$k;
                if ($v === null) {
                    $issues[] = 'Null value at '.$childPath;
                } elseif (is_string($v)) {
                    if (preg_match('/[a-z]\.\.\.$/i', $v) || str_ends_with($v, ' ...')) {
                        $issues[] = 'Truncated text (ends with ...) at '.$childPath.': '.\Illuminate\Support\Str::limit($v, 80);
                    }
                    if (mb_strlen($v) > 5000) {
                        $issues[] = 'Field very long (>5000 chars) at '.$childPath;
                    }
                } elseif (is_array($v)) {
                    $this->scanRecursive($v, $issues, $childPath);
                }
            }
        }
    }
}
