<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Strict sanitizer for user-generated community content.
 *
 * Far tighter than the page-builder HtmlSanitizer: only basic formatting is
 * allowed, all links are forced to rel="nofollow ugc noopener" and open in a
 * new tab, and no styles/images/scripts/iframes are permitted.
 */
class ForumContentSanitizer
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();

        $config->set('HTML.Allowed', implode(',', [
            'p', 'br',
            'strong', 'em', 'b', 'i', 'u', 's',
            'ul', 'ol', 'li',
            'blockquote', 'code', 'pre',
            'h3', 'h4',
            'a[href|title|rel|target]',
        ]));

        // Links: only http/https/mailto, force new tab + untrusted rel.
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);
        $config->set('HTML.TargetBlank', true);
        $config->set('HTML.Nofollow', true);

        // Drop empty + auto-paragraph loose text.
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('AutoFormat.AutoParagraph', true);

        $cachePath = storage_path('app/htmlpurifier');
        if (! is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cachePath);

        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * Accepts plain text (from a textarea) or light HTML and returns safe HTML.
     */
    public function sanitize(string $input): string
    {
        $input = trim($input);
        if ($input === '') {
            return '';
        }

        // If the user typed plain text (no tags), preserve their line breaks by
        // converting blank lines into paragraph breaks before purifying.
        if (! preg_match('/<[a-z][\s\S]*>/i', $input)) {
            $input = str_replace(["\r\n", "\r"], "\n", $input);
            $blocks = preg_split('/\n{2,}/', $input);
            $input = implode('', array_map(
                fn ($b) => '<p>' . nl2br(e(trim($b))) . '</p>',
                array_filter($blocks, fn ($b) => trim($b) !== '')
            ));
        }

        $prev = set_error_handler(function (int $severity, string $message, string $file) use (&$prev) {
            if ($severity === E_USER_WARNING && str_contains($file, 'HTMLPurifier') && str_contains($message, 'not supported')) {
                return true;
            }
            return $prev ? $prev($severity, $message, $file, '') : false;
        });

        try {
            return $this->purifier->purify($input);
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Plain-text excerpt for listings/notifications.
     */
    public function excerpt(string $html, int $length = 160): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));

        return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '…' : $text;
    }

    /**
     * Convert stored HTML back to editable plain text (for edit textareas):
     * paragraph + <br> boundaries become newlines, other tags are stripped.
     */
    public function toPlainText(string $html): string
    {
        $text = preg_replace('#</p>\s*<p[^>]*>#i', "\n\n", $html);
        $text = preg_replace('#<br\s*/?>#i', "\n", $text);
        $text = strip_tags($text);

        return trim(html_entity_decode($text, ENT_QUOTES | ENT_HTML5));
    }
}
