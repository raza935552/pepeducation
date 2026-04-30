@php
$cleanDescription = function ($text, int $maxChars) {
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));
    if ($text === '' || mb_strlen($text) <= $maxChars) {
        return $text;
    }
    $cut = mb_substr($text, 0, $maxChars);
    $lastPeriod = mb_strrpos($cut, '.');
    $lastSpace  = mb_strrpos($cut, ' ');
    if ($lastPeriod !== false && $lastPeriod > $maxChars * 0.6) {
        return mb_substr($cut, 0, $lastPeriod + 1);
    }
    if ($lastSpace !== false) {
        return rtrim(mb_substr($cut, 0, $lastSpace));
    }
    return $cut;
};

$pageDescription = $peptide->meta_description ?: $cleanDescription($peptide->overview, 160);

$aboutEntity = array_filter([
    '@type' => 'MedicalEntity',
    'name' => $peptide->name,
    'alternateName' => $peptide->abbreviation && $peptide->abbreviation !== $peptide->name ? $peptide->abbreviation : null,
]);

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $peptide->meta_title ?? $peptide->name . ' - Peptide Guide',
    'description' => $pageDescription,
    'url' => route('peptides.show', $peptide->slug),
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => route('peptides.show', $peptide->slug),
    ],
    'about' => $aboutEntity,
    'datePublished' => optional($peptide->created_at)->toIso8601String(),
    'dateModified' => optional($peptide->updated_at)->toIso8601String(),
    'author' => [
        '@type' => 'Organization',
        'name' => config('app.name'),
        'url' => config('app.url'),
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => config('app.name'),
        'url' => config('app.url'),
    ],
    'breadcrumb' => [
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Peptides', 'item' => route('peptides.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $peptide->name],
        ],
    ],
];
@endphp
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
