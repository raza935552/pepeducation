<x-public-layout
    title="Peptide Stack Builder"
    description="Build your personalized peptide stack based on your health goals. Expert-curated combinations for fat loss, muscle growth, recovery, and more."
    :canonical="isset($goalSlug) && $goalSlug ? route('stack-builder.goal', $goalSlug) : route('stack-builder')"
>
    @push('head')
    @php
        $stackGoals = collect();
        try {
            $stackGoals = \App\Models\StackGoal::where('is_active', true)
                ->orderBy('display_order')
                ->select('slug', 'name', 'description')
                ->get();
        } catch (\Throwable $e) {
            // Schema unavailable, continue without
        }

        $stackSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'Peptide Stack Builder',
            'description' => 'Curated peptide stack combinations for specific health goals.',
            'url' => isset($goalSlug) && $goalSlug ? route('stack-builder.goal', $goalSlug) : route('stack-builder'),
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => array_filter([
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Stack Builder', 'item' => route('stack-builder')],
                ]),
            ],
        ];

        if ($stackGoals->isNotEmpty()) {
            $stackSchema['mainEntity'] = [
                '@type' => 'ItemList',
                'numberOfItems' => $stackGoals->count(),
                'itemListElement' => $stackGoals->values()->map(fn ($g, $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $g->name,
                    'url' => route('stack-builder.goal', $g->slug),
                ])->all(),
            ];
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode($stackSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    @endpush

    <div class="py-14 md:py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @livewire('stack-builder', ['goalSlug' => $goalSlug ?? null])
        </div>
    </div>
</x-public-layout>
