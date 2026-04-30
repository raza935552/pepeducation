@php
$brandLogoRaw = \App\Models\Setting::getValue('branding', 'logo_url', null);
$brandLogo = null;
if (!empty($brandLogoRaw)) {
    $brandLogo = \Illuminate\Support\Str::startsWith($brandLogoRaw, ['http://','https://'])
        ? $brandLogoRaw
        : url($brandLogoRaw);
}

$siteSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => config('app.name'),
    'url' => config('app.url'),
    'description' => 'Free peptide encyclopedia with research-backed guides on 68+ peptides. Dosing protocols, benefits, side effects, and safety profiles.',
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => [
            '@type' => 'EntryPoint',
            'urlTemplate' => route('peptides.index') . '?search={search_term_string}',
        ],
        'query-input' => 'required name=search_term_string',
    ],
];

$orgSchema = array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'Professor Peptides',
    'url' => config('app.url'),
    'logo' => $brandLogo,
    'description' => 'Free peptide education platform with research-backed guides on protocols, benefits, safety, and reconstitution.',
], fn ($v) => $v !== null);
@endphp
<script type="application/ld+json">
{!! json_encode($siteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
