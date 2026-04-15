@php
$siteSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => config('app.name'),
    'url' => config('app.url'),
    'description' => 'Free peptide encyclopedia with research-backed guides on 68+ peptides. Dosing protocols, benefits, side effects, and safety profiles.',
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => route('peptides.index') . '?search={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
];

$orgSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'Professor Peptides',
    'url' => config('app.url'),
    'logo' => asset('images/og-default.jpg'),
    'description' => 'Free peptide education platform with research-backed guides on protocols, benefits, safety, and reconstitution.',
];
@endphp
<script type="application/ld+json">
{!! json_encode($siteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
