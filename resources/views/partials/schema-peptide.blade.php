<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "MedicalWebPage",
    "name": "{{ $peptide->name }}",
    "url": "{{ route('peptides.show', $peptide->slug) }}",
    "description": "{{ Str::limit(strip_tags($peptide->overview), 160) }}",
    "mainEntity": {
        "@@type": "Drug",
        "name": "{{ $peptide->name }}",
        @if($peptide->abbreviation)
        "alternateName": "{{ $peptide->abbreviation }}",
        @endif
        "description": "{{ Str::limit(strip_tags($peptide->overview), 300) }}"
    },
    "breadcrumb": {
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ route('home') }}"
            },
            {
                "@@type": "ListItem",
                "position": 2,
                "name": "Peptides",
                "item": "{{ route('peptides.index') }}"
            },
            {
                "@@type": "ListItem",
                "position": 3,
                "name": "{{ $peptide->name }}"
            }
        ]
    }
}
</script>
