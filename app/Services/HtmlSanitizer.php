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
        // Note: style attribute is needed on most tags for GrapesJS inline styles
        $config->set('HTML.Allowed', implode(',', [
            'h1[id|style]', 'h2[id|style]', 'h3[id|style]', 'h4[id|style]', 'h5[id|style]', 'h6[id|style]',
            'p[id|style]', 'br', 'hr[id|style]',
            'strong[id|style]', 'em[id|style]', 'b[id]', 'i[id]', 'u[id]', 's[id]', 'sub', 'sup', 'mark',
            'a[id|href|target|rel|title|class|style]',
            'img[id|src|alt|title|width|height|class|loading|style]',
            'ul[id|style]', 'ol[id|style]', 'li[id|style]',
            'blockquote[id|style]', 'pre[id|style]', 'code[id]',
            'table[id|style]', 'thead[id]', 'tbody[id]', 'tfoot[id]', 'tr[id|style]', 'th[id|colspan|rowspan|style]', 'td[id|colspan|rowspan|style]',
            'div[class|id|style]', 'span[id|class|style]', 'section[class|id|style]',
            'figure[id|style]', 'figcaption[id|style]', 'picture[id]', 'source[id|srcset|media|type]',
            'video[id|src|controls|autoplay|muted|loop|poster|width|height|class|style]',
            'audio[id|src|controls|class|style]',
        ]));

        // Allow safe CSS properties for page builder styling
        $config->set('CSS.AllowedProperties', implode(',', [
            'color', 'background-color', 'background', 'background-image', 'background-size', 'background-position', 'background-repeat',
            'font-size', 'font-weight', 'font-style', 'font-family',
            'text-align', 'text-decoration', 'text-transform', 'line-height', 'letter-spacing',
            'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
            'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
            'border', 'border-top', 'border-right', 'border-bottom', 'border-left',
            'border-radius', 'border-color', 'border-width', 'border-style',
            'width', 'max-width', 'min-width', 'height', 'max-height', 'min-height',
            'display', 'opacity',
            // Flexbox (used by all GrapesJS blocks)
            'flex', 'flex-direction', 'flex-wrap', 'flex-grow', 'flex-shrink', 'flex-basis',
            'align-items', 'align-self', 'justify-content', 'gap', 'order',
            // Grid
            'grid-template-columns', 'grid-template-rows', 'grid-column', 'grid-row', 'grid-gap',
            // Positioning & overflow
            'position', 'top', 'right', 'bottom', 'left', 'z-index', 'overflow',
            // Visual
            'box-shadow', 'object-fit', 'cursor', 'list-style', 'list-style-type',
            'vertical-align', 'white-space', 'word-break', 'overflow-wrap',
            // Transitions
            'transition', 'transform',
        ]));

        // Allow data URIs for inline images (base64)
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'data' => true]);

        // Allow target="_blank" on links
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        // Allow id attributes (required for GrapesJS CSS selectors)
        $config->set('Attr.EnableID', true);

        // Set serializer path for caching
        $cachePath = storage_path('app/htmlpurifier');
        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }
        $config->set('Cache.SerializerPath', $cachePath);

        // Register HTML5 elements not in HTMLPurifier's default definition
        $config->set('HTML.DefinitionID', 'pepprofesor-html5');
        $config->set('HTML.DefinitionRev', 4);
        if ($def = $config->maybeGetRawHTMLDefinition()) {
            // HTML5 inline elements
            $def->addElement('mark', 'Inline', 'Inline', 'Common');

            // HTML5 structural elements
            $def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
            $def->addElement('figcaption', 'Inline', 'Flow', 'Common');
            $def->addElement('section', 'Block', 'Flow', 'Common');

            // HTML5 media elements
            $def->addElement('picture', 'Block', 'Optional: (source, Flow) | Flow', 'Common');
            $def->addElement('source', 'Block', 'Empty', 'Common', [
                'srcset' => 'Text',
                'media' => 'Text',
                'type' => 'Text',
            ]);
            $def->addElement('video', 'Block', 'Optional: (source, Flow) | Flow', 'Common', [
                'src' => 'URI',
                'controls' => 'Bool',
                'autoplay' => 'Bool',
                'muted' => 'Bool',
                'loop' => 'Bool',
                'poster' => 'URI',
                'width' => 'Length',
                'height' => 'Length',
            ]);
            $def->addElement('audio', 'Block', 'Optional: (source, Flow) | Flow', 'Common', [
                'src' => 'URI',
                'controls' => 'Bool',
            ]);

            // HTML5 attributes on existing elements
            $def->addAttribute('img', 'loading', 'Enum#lazy,eager');
        }

        $this->purifier = new HTMLPurifier($config);
    }

    public function sanitize(string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Suppress HTMLPurifier E_USER_WARNING about unsupported CSS/HTML5
        // features — Laravel's error handler converts these to ErrorException.
        // Unsupported properties are silently stripped instead of crashing.
        $prev = set_error_handler(function (int $severity, string $message, string $file) use (&$prev) {
            if ($severity === E_USER_WARNING && str_contains($file, 'HTMLPurifier') && str_contains($message, 'not supported')) {
                return true;
            }
            return $prev ? $prev($severity, $message, $file, '') : false;
        });

        try {
            return $this->purifier->purify($html);
        } finally {
            restore_error_handler();
        }
    }
}
