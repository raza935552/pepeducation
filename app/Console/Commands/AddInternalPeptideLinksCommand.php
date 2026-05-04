<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Peptide;
use Illuminate\Console\Command;

class AddInternalPeptideLinksCommand extends Command
{
    protected $signature = 'blog:add-peptide-links {--limit=999 : Max posts to process}';

    protected $description = 'Inject inline links from blog post body text to peptide pages for the first mention of each linked peptide.';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        // Build a name -> peptide URL lookup, including common name variants.
        $peptides = Peptide::published()->select('id', 'name', 'slug', 'abbreviation')->get();
        $variants = [];

        foreach ($peptides as $p) {
            $url = route('peptides.show', $p->slug);
            $names = [$p->name];
            if (!empty($p->abbreviation) && $p->abbreviation !== $p->name) {
                $names[] = $p->abbreviation;
            }
            // Add common synonyms
            $synonyms = $this->commonSynonyms($p->slug);
            foreach ($synonyms as $syn) {
                $names[] = $syn;
            }

            foreach ($names as $name) {
                $variants[] = ['name' => $name, 'url' => $url, 'peptide_id' => $p->id];
            }
        }

        // Sort longest names first so "BPC-157" matches before "BPC"
        usort($variants, fn ($a, $b) => mb_strlen($b['name']) - mb_strlen($a['name']));

        $processed = 0;
        $linksAdded = 0;
        $posts = BlogPost::with('peptides')->where('status', 'published')->limit($limit)->get();

        foreach ($posts as $post) {
            $html = $post->html;
            if (empty($html)) {
                continue;
            }

            $allowedPeptideIds = $post->peptides->pluck('id')->all();
            $linkedThisPost = [];
            $newHtml = $html;

            foreach ($variants as $variant) {
                // Only link to peptides this post is associated with (via pivot) OR fallback to any peptide if pivot is empty
                if (!empty($allowedPeptideIds) && !in_array($variant['peptide_id'], $allowedPeptideIds, true)) {
                    continue;
                }

                // One link per peptide per post
                if (in_array($variant['peptide_id'], $linkedThisPost, true)) {
                    continue;
                }

                $name = $variant['name'];
                $url = $variant['url'];

                // Match the name as a whole word/phrase, not inside an existing <a>, not inside a heading
                // Pattern: not preceded by alpha char, not followed by alpha char, not inside <a or </a, not inside <h
                $pattern = '/(?<![\w\-])'.preg_quote($name, '/').'(?![\w\-])/u';

                $matched = false;
                $newHtml = preg_replace_callback($pattern, function ($m) use ($url, &$matched) {
                    if ($matched) {
                        return $m[0];
                    }
                    $matched = true;

                    return '<a href="'.htmlspecialchars($url, ENT_QUOTES).'" class="text-primary-600 underline hover:text-primary-700">'.$m[0].'</a>';
                }, $newHtml, 1);

                // Strip out matches that fell inside an existing anchor or heading by reverting
                // (Cheap heuristic: check if the inserted link is inside <h1>...</h1> or inside another <a>)
                if ($matched) {
                    if ($this->insideExcludedContext($newHtml, $url)) {
                        // Revert by removing the link we just added
                        $newHtml = preg_replace('/<a href="'.preg_quote(htmlspecialchars($url, ENT_QUOTES), '/').'"[^>]*>([^<]+)<\/a>/u', '$1', $newHtml, 1);
                        $matched = false;
                    } else {
                        $linksAdded++;
                        $linkedThisPost[] = $variant['peptide_id'];
                    }
                }
            }

            if ($newHtml !== $html) {
                $post->html = $newHtml;
                $post->saveQuietly();
                $processed++;
                $this->line("  + ".$post->slug." (".count($linkedThisPost)." links)");
            }
        }

        $this->newLine();
        $this->info("Processed {$processed} posts; added {$linksAdded} inline peptide links total");

        return self::SUCCESS;
    }

    private function commonSynonyms(string $slug): array
    {
        $map = [
            'semaglutide'  => ['Ozempic', 'Wegovy'],
            'tirzepatide'  => ['Mounjaro', 'Zepbound'],
            'mk-677'       => ['Ibutamoren', 'MK-677'],
            'pt-141'       => ['Bremelanotide', 'PT-141'],
            'cjc-1295'     => ['CJC-1295'],
            'cjc-1295-dac' => ['CJC-1295 DAC'],
            'omberacetam'  => ['Noopept', 'Omberacetam'],
            'ss-31'        => ['SS-31', 'Elamipretide'],
            'thymosin-alpha-1' => ['Thymosin Alpha 1'],
            'thymosin-beta-4'  => ['Thymosin Beta-4', 'Thymosin Beta 4'],
            'aod-9604'     => ['AOD-9604', 'AOD9604'],
            'tb-500'       => ['TB-500', 'TB500'],
            'bpc-157'      => ['BPC-157', 'BPC157'],
            'foxo4-dri'    => ['FOXO4-DRI'],
            'igf-1-lr3'    => ['IGF-1 LR3'],
            'na-semax-amidate' => ['NA-Semax-Amidate'],
            'pe-22-28'     => ['PE-22-28'],
            'll-37'        => ['LL-37'],
            'ghk-cu'       => ['GHK-Cu', 'Copper Tripeptide'],
            'ahk-cu'       => ['AHK-Cu'],
            'mots-c'       => ['MOTS-c'],
            'nad-plus'     => ['NAD+', 'NAD plus'],
            'snap-8'       => ['SNAP-8'],
            'glp-1-glow'   => ['GLOW protocol'],
            'klow'         => ['KLOW'],
        ];

        return $map[$slug] ?? [];
    }

    private function insideExcludedContext(string $html, string $url): bool
    {
        // Look for the link inside <a>...<a> nesting (not allowed) or inside h1/h2/h3
        $escUrl = preg_quote(htmlspecialchars($url, ENT_QUOTES), '/');
        if (preg_match('/<a[^>]*>[^<]*<a href="'.$escUrl.'"/', $html)) {
            return true;
        }
        if (preg_match('/<h[1-6][^>]*>[^<]*<a href="'.$escUrl.'"[^<]*<\/a>[^<]*<\/h[1-6]>/', $html)) {
            return true;
        }

        return false;
    }
}
