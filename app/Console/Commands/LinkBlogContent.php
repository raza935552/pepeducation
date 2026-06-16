<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Services\Blog\CalculatorMatcher;
use Illuminate\Console\Command;

/**
 * Injects ONE natural in-content anchor per relevant target into published blog posts:
 *   - calculators (topically matched via CalculatorMatcher), and
 *   - the "Peptides 101" pillar guide.
 *
 * Safe by design: edits only the first eligible TEXT node (never inside an existing
 * link or heading), skips a target already linked in the post, caps links per post,
 * and snapshots the pre-edit html to blog_post_versions. Use --dry-run to preview.
 */
class LinkBlogContent extends Command
{
    protected $signature = 'blog:link-content {--dry-run : Show what would change without saving} {--max=4 : Max inline links to add per post}';

    protected $description = 'Add in-content internal links (calculators + Peptides 101 pillar) to blog posts';

    /** Clean, full-word anchor phrases per calculator slug (first found wins). */
    private const ANCHOR_PHRASES = [
        'glp-1'          => ['GLP-1'],
        'weight-loss'    => ['weight loss'],
        'glp-1-units'    => ['units to draw', 'how many units'],
        'reconstitution' => ['reconstitution', 'bacteriostatic water'],
        'bmi'            => ['body mass index', 'BMI'],
        'trt'            => ['testosterone replacement therapy', 'TRT'],
        'melanotan'      => ['Melanotan'],
        'fitness'        => ['lean muscle', 'hypertrophy'],
        'tdee'           => ['maintenance calories', 'TDEE'],
        'body-fat'       => ['body fat percentage'],
        'macro'          => ['macronutrient', 'macros'],
        'protocol'       => ['dosing protocol', 'dosing schedule'],
    ];

    private const PILLAR_SLUG = 'peptides-101-beginner-complete-guide';
    private const PILLAR_PHRASES = ['Peptides 101', "beginner's guide to peptides", 'beginner guide to peptides'];

    public function handle(CalculatorMatcher $matcher): int
    {
        $dry = (bool) $this->option('dry-run');
        $max = (int) $this->option('max');
        $pillarUrl = route('blog.show', self::PILLAR_SLUG);

        $totalPosts = 0;
        $totalLinks = 0;

        BlogPost::where('status', 'published')->whereNotNull('html')
            ->with(['peptides:id,name,slug', 'categories:id,slug'])
            ->chunkById(50, function ($posts) use ($matcher, $dry, $max, $pillarUrl, &$totalPosts, &$totalLinks) {
            foreach ($posts as $post) {
                // Build the peptide-database + calculator targets for this post.
                $pepTargets = $post->peptides
                    ->filter(fn ($pep) => filled($pep->name) && filled($pep->slug))
                    ->map(fn ($pep) => [
                        'url'     => route('peptides.show', $pep->slug),
                        'phrases' => [$pep->name],
                        'label'   => 'peptide:' . $pep->slug,
                    ])->values()->all();

                $calcTargets = [];
                foreach ($matcher->inlineTargets($post, 6) as $c) {
                    $phrases = self::ANCHOR_PHRASES[$c['slug']] ?? [];
                    if ($phrases) {
                        $calcTargets[] = ['url' => $c['url'], 'phrases' => $phrases, 'label' => 'calc:' . $c['slug']];
                    }
                }

                $pillarTarget = $post->slug !== self::PILLAR_SLUG
                    ? ['url' => $pillarUrl, 'phrases' => self::PILLAR_PHRASES, 'label' => 'pillar:peptides-101']
                    : null;

                // Interleave for variety: peptide → calculator → pillar → peptide → calculator.
                $targets = array_values(array_filter([
                    $pepTargets[0]  ?? null,
                    $calcTargets[0] ?? null,
                    $pillarTarget,
                    $pepTargets[1]  ?? null,
                    $calcTargets[1] ?? null,
                ]));
                if (!$targets) {
                    continue;
                }

                [$newHtml, $added] = $this->inject($post->html, $targets, $max);
                if (!$added) {
                    continue;
                }

                $totalPosts++;
                $totalLinks += count($added);
                $this->line(($dry ? '[dry] ' : '') . "#{$post->id} {$post->slug}: " . implode(', ', $added));

                if (!$dry) {
                    // Snapshot the pre-edit state into the post's version history so the
                    // change can be restored from the admin Versions panel.
                    try {
                        $lastVersion = $post->versions()->max('version') ?? 0;
                        $post->versions()->create([
                            'version'    => $lastVersion + 1,
                            'title'      => $post->title,
                            'content'    => $post->content,
                            'html'       => $post->html,
                            'created_by' => $post->created_by,
                        ]);
                    } catch (\Throwable $e) {
                        $this->warn("  (could not snapshot #{$post->id}: {$e->getMessage()})");
                    }
                    $post->html = $newHtml;
                    $post->saveQuietly();
                }
            }
        });

        $this->newLine();
        $this->info(($dry ? 'DRY RUN — ' : '') . "Posts touched: {$totalPosts} | inline links: {$totalLinks}");

        return self::SUCCESS;
    }

    /**
     * Inject anchors for each target into the first eligible text node.
     *
     * @return array{0:string,1:array<int,string>}  [newHtml, labelsAdded]
     */
    private function inject(string $html, array $targets, int $max): array
    {
        $added = [];

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        // Force UTF-8; wrap in a known root so we can extract just our content back out
        // (loadHTML adds <!DOCTYPE>/<html>/<body>, which we ignore via the root lookup).
        $dom->loadHTML('<?xml encoding="utf-8"?><div id="__root">' . $html . '</div>');
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        foreach ($targets as $t) {
            if (count($added) >= $max) {
                break;
            }
            // Idempotent: skip if this URL (or its path) is already linked anywhere.
            $path = parse_url($t['url'], PHP_URL_PATH) ?: $t['url'];
            $exists = $xpath->query('//a[contains(@href, ' . $this->xpq($path) . ')]');
            if ($exists->length > 0) {
                continue;
            }

            $done = false;
            foreach ($t['phrases'] as $phrase) {
                if ($done) {
                    break;
                }
                // All text nodes NOT inside an anchor or heading.
                $textNodes = $xpath->query('//text()[not(ancestor::a) and not(ancestor::h1) and not(ancestor::h2) and not(ancestor::h3) and not(ancestor::h4) and not(ancestor::h5) and not(ancestor::h6)]');
                foreach ($textNodes as $node) {
                    $text = $node->nodeValue;
                    $pos = mb_stripos($text, $phrase);
                    if ($pos === false) {
                        continue;
                    }
                    $len     = mb_strlen($phrase);
                    $before  = mb_substr($text, 0, $pos);
                    $matched = mb_substr($text, $pos, $len);     // preserve original casing
                    $after   = mb_substr($text, $pos + $len);

                    $frag = $dom->createDocumentFragment();
                    if ($before !== '') {
                        $frag->appendChild($dom->createTextNode($before));
                    }
                    $a = $dom->createElement('a');
                    $a->setAttribute('href', $t['url']);
                    $a->appendChild($dom->createTextNode($matched));
                    $frag->appendChild($a);
                    if ($after !== '') {
                        $frag->appendChild($dom->createTextNode($after));
                    }
                    $node->parentNode->replaceChild($frag, $node);

                    $added[] = $t['label'] . " (\"{$matched}\")";
                    $done = true;
                    break;
                }
            }
        }

        if (!$added) {
            return [$html, []];
        }

        // Extract inner HTML of our wrapper div (XPath — getElementById is unreliable
        // after loadHTML without a DTD).
        $root = $xpath->query('//*[@id="__root"]')->item(0);
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }

        return [$out, $added];
    }

    /** Quote a string for use inside an XPath expression. */
    private function xpq(string $s): string
    {
        if (!str_contains($s, "'")) {
            return "'$s'";
        }
        return "concat('" . str_replace("'", "',\"'\",'", $s) . "')";
    }
}
