@php
$bcSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => collect($breadcrumbs)->map(fn($crumb, $i) => array_filter([
        '@type' => 'ListItem',
        'position' => $i + 1,
        'name' => $crumb['name'],
        'item' => $crumb['url'] ?? null,
    ]))->values()->all(),
];
@endphp
<script type="application/ld+json">
{!! json_encode($bcSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
