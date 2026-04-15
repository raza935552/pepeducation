@php
$listSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'Peptide Database',
    'description' => 'Browse ' . $peptides->total() . '+ peptides with research-backed protocols, dosing, benefits, and safety information.',
    'url' => route('peptides.index'),
    'mainEntity' => [
        '@type' => 'ItemList',
        'numberOfItems' => $peptides->total(),
        'itemListElement' => $peptides->values()->map(fn($p, $i) => [
            '@type' => 'ListItem',
            'position' => ($peptides->currentPage() - 1) * $peptides->perPage() + $i + 1,
            'name' => $p->name,
            'url' => route('peptides.show', $p->slug),
        ])->all(),
    ],
];
@endphp
<script type="application/ld+json">
{!! json_encode($listSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
