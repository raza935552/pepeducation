<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlSanitizer
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();

        // Allow safe HTML tags for page builder content
        $config->set('HTML.Allowed', implode(',', [
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'p', 'br', 'hr',
            'strong', 'em', 'b', 'i', 'u', 's', 'sub', 'sup', 'mark',
            'a[href|target|rel|title|class]',
            'img[src|alt|title|width|height|class|loading]',
            'ul', 'ol', 'li',
            'blockquote', 'pre', 'code',
            'table', 'thead', 'tbody', 'tfoot', 'tr', 'th[colspan|rowspan]', 'td[colspan|rowspan]',
            'div[class|id|style]', 'span[class|style]', 'section[class|id|style]',
            'figure', 'figcaption', 'picture', 'source[srcset|media|type]',
            'video[src|controls|autoplay|muted|loop|poster|width|height|class]',
            'audio[src|controls|class]',
        ]));

        // Allow safe CSS properties for page builder styling
        $config->set('CSS.AllowedProperties', implode(',', [
            'color', 'background-color', 'background',
            'font-size', 'font-weight', 'font-style', 'font-family',
            'text-align', 'text-decoration', 'text-transform', 'line-height', 'letter-spacing',
            'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
            'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
            'border', 'border-radius', 'border-color', 'border-width', 'border-style',
            'width', 'max-width', 'min-width', 'height', 'max-height', 'min-height',
            'display', 'opacity',
        ]));

        // Allow data URIs for inline images (base64)
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'data' => true]);

        // Allow target="_blank" on links
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        // Set serializer path for caching
        $cachePath = storage_path('app/htmlpurifier');
        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cachePath);

        $this->purifier = new HTMLPurifier($config);
    }

    public function sanitize(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        return $this->purifier->purify($html);
    }
}
