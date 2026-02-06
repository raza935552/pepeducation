<?php

namespace App\Services;

class HtmlSanitizer
{
    /** Tags that are always stripped (with their content) */
    private const DANGEROUS_TAGS = [
        'script', 'iframe', 'object', 'embed', 'applet',
        'meta', 'link', 'base', 'noscript',
    ];

    /** Event handler attributes (on*) are stripped via regex */
    private const DANGEROUS_ATTR_PATTERN = '/\s+on\w+\s*=\s*["\'][^"\']*["\']/i';

    public function sanitize(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $html = $this->stripDangerousTags($html);
        $html = $this->stripEventHandlers($html);
        $html = $this->stripJavascriptUris($html);

        return $html;
    }

    private function stripDangerousTags(string $html): string
    {
        $tags = implode('|', self::DANGEROUS_TAGS);

        // Strip tags and their content
        $html = preg_replace(
            '#<\s*(' . $tags . ')[\s>].*?</\s*\1\s*>#si',
            '',
            $html
        );

        // Strip self-closing / unclosed dangerous tags
        $html = preg_replace(
            '#<\s*/?\s*(' . $tags . ')(?:\s[^>]*)?\s*/?\s*>#si',
            '',
            $html
        );

        return $html;
    }

    private function stripEventHandlers(string $html): string
    {
        return preg_replace(self::DANGEROUS_ATTR_PATTERN, '', $html);
    }

    private function stripJavascriptUris(string $html): string
    {
        // Strip javascript: in href/src/action attributes
        return preg_replace(
            '/(href|src|action)\s*=\s*["\']?\s*javascript\s*:/i',
            '$1="',
            $html
        );
    }
}
