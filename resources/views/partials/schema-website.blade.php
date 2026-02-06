<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "name": "{{ config('app.name') }}",
    "url": "{{ config('app.url') }}",
    "description": "Educational resource for peptide research",
    "potentialAction": {
        "@@type": "SearchAction",
        "target": "{{ route('peptides.index') }}?search={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
