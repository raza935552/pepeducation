<?php

namespace App\Console\Commands;

use App\Models\Lander;
use Illuminate\Console\Command;

/**
 * One-time importer: parses the 5 legacy static "Operator Brief" lander blades and
 * mirrors their content into editable CMS Lander rows (template=operator-brief).
 * Idempotent — re-running overwrites the row's content from the blade. Rows are
 * created INACTIVE so the live static pages keep serving until verified, then flipped.
 */
class ImportLegacyLanders extends Command
{
    protected $signature = 'landers:import-legacy {--activate : Mark imported rows active}';
    protected $description = 'Import the 5 legacy Operator Brief landers into the CMS';

    private array $slugs = ['10-years', 'lying', 'coas-worthless', 'suppliers-identical', 'vetted-47'];

    public function handle(): int
    {
        foreach ($this->slugs as $slug) {
            $path = resource_path("views/landers/{$slug}.blade.php");
            if (!is_file($path)) {
                $this->warn("skip {$slug}: blade not found");
                continue;
            }
            $content = $this->parse(file_get_contents($path));

            $lander = Lander::firstOrNew(['slug' => $slug]);
            $lander->name = $lander->name ?: ($content['meta']['title'] ?? $slug);
            $lander->template = 'operator-brief';
            $lander->outbound_slug = $lander->outbound_slug ?: "lp-{$slug}";
            $lander->noindex = $content['meta']['noindex'] ?? false;
            $lander->content = $content;
            if ($this->option('activate')) {
                $lander->is_active = true;
            } elseif (!$lander->exists) {
                $lander->is_active = false;
            }
            $lander->save();

            $this->info(sprintf(
                '%-22s flags=%d checklist=%d dropdowns=%d active=%s',
                $slug,
                count($content['flags'] ?? []),
                count($content['closing']['checklist'] ?? []),
                count($content['refs']['dropdowns'] ?? []),
                $lander->is_active ? 'yes' : 'no'
            ));
        }

        return self::SUCCESS;
    }

    private function parse(string $blade): array
    {
        // Strip Blade-only bits so DOMDocument sees plain HTML.
        $html = preg_replace('/@verbatim<style>.*?<\/style>@endverbatim/s', '', $blade);
        $html = preg_replace('/<x-meta-pixel\s*\/?>/', '', $html);

        // Encode non-ASCII to numeric entities so libxml (which assumes Latin-1)
        // never mangles UTF-8; we decode back to clean UTF-8 when extracting.
        $html = mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, 0x10FFFF], 'UTF-8');

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        libxml_clear_errors();
        $xp = new \DOMXPath($doc);

        $text = fn($q, $ctx = null) => trim(optional($xp->query($q, $ctx)->item(0))->textContent ?? '');
        $inner = function ($q, $ctx = null) use ($xp, $doc) {
            $node = $xp->query($q, $ctx)->item(0);
            return $node ? $this->innerHtml($node) : '';
        };

        // ── meta / chrome ──
        $robots = '';
        if ($m = $xp->query("//meta[@name='robots']")->item(0)) {
            $robots = $m->getAttribute('content');
        }
        $desc = '';
        if ($m = $xp->query("//meta[@name='description']")->item(0)) {
            $desc = $m->getAttribute('content');
        }

        $data = [
            'meta' => [
                'title' => $text('//title'),
                'description' => $desc,
                'noindex' => stripos($robots, 'noindex') !== false,
            ],
            'chrome' => [
                'notice' => $text("//div[contains(@class,'notice')]"),
                'pub' => $inner("//div[contains(@class,'masthead')]//div[contains(@class,'pub')]"),
                'masthead_tag' => $text("//div[contains(@class,'masthead')]//div[contains(@class,'tag')]"),
            ],
            'age_gate' => [
                'title' => $inner("//div[contains(@class,'age')]//div[contains(@class,'b')]"),
                'body' => $text("//div[contains(@class,'age')]//p"),
            ],
        ];

        $article = $xp->query("//article")->item(0);

        // ── hero ──
        $heroImg = $xp->query(".//img[contains(@class,'article-img')]", $article)->item(0);
        $data['hero'] = [
            'eyebrow' => $text(".//div[contains(@class,'eyebrow')]", $article),
            'h1' => $text(".//h1", $article),
            'dek' => $inner(".//p[contains(@class,'dek')]", $article),
            'image_url' => $heroImg ? $heroImg->getAttribute('src') : '',
            'image_alt' => $heroImg ? $heroImg->getAttribute('alt') : '',
            'skip_text' => $text(".//p[contains(@class,'skip')]//a", $article),
        ];

        // ── byline ──
        $byline = $xp->query(".//div[contains(@class,'byline')]", $article)->item(0);
        $author = $byline ? $text(".//b", $byline) : '';
        $nonDot = [];
        if ($byline) {
            foreach ($xp->query(".//span", $byline) as $s) {
                if (strpos($s->getAttribute('class'), 'dot') === false) {
                    $nonDot[] = trim($s->textContent);
                }
            }
        }
        $role = '';
        if (!empty($nonDot[0])) {
            $role = trim(preg_replace('/^By\s+' . preg_quote($author, '/') . '\s*·\s*/u', '', $nonDot[0]));
        }
        $data['byline'] = [
            'author' => $author,
            'role' => $role,
            'cred' => $nonDot[1] ?? '',
            'read_time' => $nonDot[2] ?? '',
        ];

        // ── intro: <p> direct children of article between p.lead and first .flag-block ──
        $data['intro'] = [
            'lead' => $inner(".//p[contains(@class,'lead')]", $article),
            'body' => '',
            'image_url' => '',
            'image_alt' => '',
        ];
        $introBody = '';
        $sawLead = false;
        foreach ($article->childNodes as $node) {
            if (!($node instanceof \DOMElement)) continue;
            $cls = $node->getAttribute('class');
            if ($node->tagName === 'p' && strpos($cls, 'lead') !== false) { $sawLead = true; continue; }
            if (!$sawLead) continue;
            if ($node->tagName === 'div' && strpos($cls, 'flag-block') !== false) break;
            if ($node->tagName === 'p' && strpos($cls, 'skip') === false) {
                $introBody .= $doc->saveHTML($node) . "\n";
            }
            if ($node->tagName === 'img' && strpos($cls, 'article-img') !== false) {
                $data['intro']['image_url'] = $node->getAttribute('src');
                $data['intro']['image_alt'] = $node->getAttribute('alt');
            }
        }
        $data['intro']['body'] = $this->decode(trim($introBody));

        // ── flag blocks ──
        $data['flags'] = [];
        foreach ($xp->query(".//div[contains(@class,'flag-block')]", $article) as $fb) {
            $body = '';
            $img = ['url' => '', 'alt' => ''];
            foreach ($fb->childNodes as $node) {
                if (!($node instanceof \DOMElement)) continue;
                $cls = $node->getAttribute('class');
                if ($node->tagName === 'p' && strpos($cls, 'skip') === false) {
                    $body .= $doc->saveHTML($node) . "\n";
                }
                if ($node->tagName === 'img') {
                    $img = ['url' => $node->getAttribute('src'), 'alt' => $node->getAttribute('alt')];
                }
            }
            $data['flags'][] = [
                'label' => $text(".//div[contains(@class,'flagk')]", $fb),
                'heading' => $text(".//h2", $fb),
                'body' => $this->decode(trim($body)),
                'image_url' => $img['url'],
                'image_alt' => $img['alt'],
            ];
        }

        // ── closing: the h2 direct child of article (not in flag-block/gate) ──
        $closingH2 = null;
        foreach ($article->childNodes as $node) {
            if ($node instanceof \DOMElement && $node->tagName === 'h2') { $closingH2 = $node; break; }
        }
        $closingBodyParts = [];
        $checklistIntro = '';
        if ($closingH2) {
            $n = $closingH2->nextSibling;
            while ($n) {
                if ($n instanceof \DOMElement) {
                    $cls = $n->getAttribute('class');
                    if ($n->tagName === 'div' && strpos($cls, 'check') !== false) break;
                    if ($n->tagName === 'p') $closingBodyParts[] = $doc->saveHTML($n);
                }
                $n = $n->nextSibling;
            }
        }
        // last <p> before the checklist is the intro line
        if ($closingBodyParts) {
            $last = array_pop($closingBodyParts);
            $checklistIntro = trim(strip_tags($this->decode($last)));
        }
        $checklist = [];
        foreach ($xp->query(".//div[contains(@class,'check')]//div[contains(@class,'crow')]", $article) as $crow) {
            // second span (not the icon)
            $spans = $xp->query(".//span", $crow);
            $val = $spans->length > 1 ? $this->innerHtml($spans->item(1)) : '';
            $checklist[] = $val;
        }
        $data['closing'] = [
            'heading' => $closingH2 ? trim($closingH2->textContent) : '',
            'body' => $this->decode(trim(implode("\n", $closingBodyParts))),
            'checklist_intro' => $checklistIntro,
            'checklist' => $checklist,
        ];

        // ── gate ──
        $gate = $xp->query(".//div[contains(@class,'gate')]", $article)->item(0);
        $data['gate'] = [
            'heading' => $gate ? $this->innerHtml($xp->query(".//h2", $gate)->item(0)) : '',
            'body' => $gate ? $text(".//p", $gate) : '',
            'consent' => $gate ? $text(".//label[contains(@class,'chk')]//span", $gate) : '',
            'cta' => $gate ? trim($xp->query(".//a[@id='go']", $gate)->item(0)?->textContent ?? '') : '',
            'sub' => $gate ? $text(".//div[contains(@class,'cta-sub')]", $gate) : '',
        ];

        // ── refs / dropdowns ──
        $dropdowns = [];
        foreach ($xp->query(".//div[contains(@class,'refs')]//details[contains(@class,'drop')]", $article) as $d) {
            $items = [];
            foreach ($xp->query(".//ol/li", $d) as $li) {
                $items[] = trim($this->innerHtml($li));
            }
            $dropdowns[] = [
                'summary' => $text(".//summary", $d),
                'intro' => $text(".//div[contains(@class,'db')]/p", $d),
                'items' => $items,
                'src' => $text(".//div[contains(@class,'src')]", $d),
            ];
        }
        $data['refs'] = [
            'title' => $text(".//div[contains(@class,'refs-h')]", $article),
            'dropdowns' => $dropdowns,
        ];

        // ── footer ──
        $copyright = '';
        if ($fl = $xp->query("//footer//div[contains(@class,'flinks')]")->item(0)) {
            // text node before the first <a>
            foreach ($fl->childNodes as $node) {
                if ($node instanceof \DOMText && trim($node->textContent) !== '') {
                    // Unicode-safe: strip surrounding whitespace + a trailing "·"
                    // separator (byte-trimming "·" would corrupt the leading "©").
                    $copyright = preg_replace('/\s*·\s*$/u', '', trim($node->textContent));
                    break;
                }
            }
        }
        $data['footer'] = [
            'disclaimer' => $inner("//footer//p[contains(@class,'disc')]"),
            'copyright' => $copyright,
        ];

        return $data;
    }

    private function innerHtml(?\DOMNode $node): string
    {
        if (!$node) return '';
        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }
        return $this->decode(trim($html));
    }

    /** Resolve numeric/named entities back to real UTF-8, keeping real tags intact. */
    private function decode(string $s): string
    {
        return html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
