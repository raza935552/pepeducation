@php
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'MedicalWebPage',
    'name' => $peptide->meta_title ?? $peptide->name . ' - Peptide Guide',
    'url' => route('peptides.show', $peptide->slug),
    'description' => $peptide->meta_description ?? Str::limit(strip_tags($peptide->overview), 160),
    'mainEntity' => array_filter([
        '@type' => 'Drug',
        'name' => $peptide->name,
        'alternateName' => $peptide->abbreviation !== $peptide->name ? $peptide->abbreviation : null,
        'description' => Str::limit(strip_tags($peptide->overview), 300),
        'url' => route('peptides.show', $peptide->slug),
    ]),
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
