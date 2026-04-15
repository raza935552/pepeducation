<?php

namespace App\Console\Commands;

use App\Models\Peptide;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSeoMetaCommand extends Command
{
    protected $signature = 'seo:generate-meta
                            {--force : Overwrite existing meta titles/descriptions}
                            {--dry-run : Preview changes without saving}';

    protected $description = 'Generate unique SEO meta titles and descriptions for peptide pages';

    private const MAX_TITLE = 58;
    private const MAX_DESC = 155;

    public function handle(): int
    {
        $query = Peptide::published()->with('categories');

        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->where('meta_title', 'like', '% - Research, Dosing & Protocols')
                  ->orWhereNull('meta_title')
                  ->orWhere('meta_title', '');
            });
        }

        $peptides = $query->get();
        $this->info("Processing {$peptides->count()} peptides...");

        $updated = 0;

        foreach ($peptides as $peptide) {
            $title = $this->generateTitle($peptide);
            $description = $this->generateDescription($peptide);

            if ($this->option('dry-run')) {
                $this->line('');
                $this->comment($peptide->name);
                $this->info("  Title ({$this->charCount($title)}): {$title}");
                $this->info("  Desc ({$this->charCount($description)}): {$description}");
                continue;
            }

            $peptide->update([
                'meta_title' => $title,
                'meta_description' => $description,
            ]);
            $updated++;
            $this->line("  Updated: {$peptide->name}");
        }

        if (!$this->option('dry-run')) {
            $this->info("Done. Updated {$updated} peptides.");
        }

        return self::SUCCESS;
    }

    private function generateTitle(Peptide $peptide): string
    {
        $name = $peptide->name;
        $nameLen = strlen($name);
        $primaryCat = $peptide->categories->pluck('name')->first();

        // For long names (>25 chars), use shorter suffixes
        if ($nameLen > 25) {
            $variants = [
                "{$name}: Guide & Protocols",
                "{$name} - Benefits & Dosing",
                "{$name}: Dosing & Safety",
            ];
        } elseif ($primaryCat && strlen("{$name}: {$primaryCat} Guide") <= self::MAX_TITLE) {
            $variants = [
                "{$name}: {$primaryCat} Guide - Dosing & Benefits",
                "{$name} Peptide - {$primaryCat} Protocols & Research",
                "{$name} Guide: {$primaryCat} Benefits & Dosing",
                "{$name} - Benefits, Protocols & Safety Guide",
                "{$name} Peptide Guide - Research & Dosing",
            ];
        } else {
            $variants = [
                "{$name} - Benefits, Protocols & Safety Guide",
                "{$name} Peptide Guide - Research & Dosing",
                "{$name}: Complete Peptide Guide & Protocols",
                "{$name} - Dosing, Safety & Research Guide",
                "{$name}: Benefits & Dosing Protocols",
            ];
        }

        // Pick deterministically, then find one that fits
        $preferred = $variants[$peptide->id % count($variants)];
        if (strlen($preferred) <= self::MAX_TITLE) {
            return $preferred;
        }

        // Try all variants for one that fits
        foreach ($variants as $v) {
            if (strlen($v) <= self::MAX_TITLE) {
                return $v;
            }
        }

        // Last resort: just name + short suffix, truncated cleanly
        $suffix = ' - Peptide Guide';
        $available = self::MAX_TITLE - strlen($suffix);
        if ($nameLen > $available) {
            return substr($name, 0, $available) . $suffix;
        }
        return $name . $suffix;
    }

    private function generateDescription(Peptide $peptide): string
    {
        $name = $peptide->name;
        $overview = strip_tags($peptide->overview ?? '');
        $tail = "Learn about {$name} dosing, protocols, and safety.";
        $tailLen = strlen($tail) + 1; // +1 for the space

        if (!empty($overview)) {
            // Extract first sentence
            $firstSentence = Str::before($overview, '. ');
            if ($firstSentence !== $overview) {
                $firstSentence .= '.';
            }

            // Check if first sentence + tail fits
            $combined = $firstSentence . ' ' . $tail;
            if (strlen($combined) <= self::MAX_DESC) {
                return $combined;
            }

            // Truncate first sentence to fit with tail
            $maxIntro = self::MAX_DESC - $tailLen;
            $intro = $this->truncateAtWord($firstSentence, $maxIntro);
            return $intro . ' ' . $tail;
        }

        // No overview - build from categories/benefits
        $benefit = $this->extractPrimaryBenefit($peptide);
        if ($benefit) {
            $desc = "{$name} is used for {$benefit}. Explore research-backed protocols, dosing, benefits, and safety.";
            if (strlen($desc) <= self::MAX_DESC) {
                return $desc;
            }
        }

        return "Complete {$name} guide - research-backed dosing protocols, benefits, safety warnings, and usage information.";
    }

    private function extractPrimaryBenefit(Peptide $peptide): ?string
    {
        $benefits = $peptide->key_benefits;
        if (empty($benefits) || !is_array($benefits)) {
            return null;
        }

        $raw = $benefits[0];
        $parts = preg_split('/[,.]/', $raw);
        $first = trim(strtolower($parts[0] ?? ''));

        return strlen($first) > 50 ? $this->truncateAtWord($first, 47) : $first;
    }

    /** Truncate at last word boundary without adding ellipsis */
    private function truncateAtWord(string $text, int $max): string
    {
        $text = trim($text);
        if (strlen($text) <= $max) {
            return $text;
        }

        $cut = substr($text, 0, $max);

        // Try to cut at a sentence end
        $lastPeriod = strrpos($cut, '.');
        if ($lastPeriod !== false && $lastPeriod > $max * 0.5) {
            return substr($text, 0, $lastPeriod + 1);
        }

        // Cut at last word boundary
        $lastSpace = strrpos($cut, ' ');
        if ($lastSpace !== false && $lastSpace > $max * 0.5) {
            $trimmed = substr($text, 0, $lastSpace);
            // Remove trailing punctuation fragments
            return rtrim($trimmed, ' ,;:--');
        }

        return $cut;
    }

    private function charCount(string $text): int
    {
        return strlen($text);
    }
}
