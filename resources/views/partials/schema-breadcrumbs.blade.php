<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        @foreach($breadcrumbs as $i => $crumb)
        {
            "@@type": "ListItem",
            "position": {{ $i + 1 }},
            "name": "{{ $crumb['name'] }}"
            @if(isset($crumb['url']))
            ,"item": "{{ $crumb['url'] }}"
            @endif
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
