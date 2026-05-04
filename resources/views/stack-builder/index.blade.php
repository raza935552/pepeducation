@php
    $currentGoal = null;
    if (isset($goalSlug) && $goalSlug) {
        try {
            $currentGoal = \App\Models\StackGoal::where('slug', $goalSlug)->where('is_active', true)->first();
        } catch (\Throwable $e) {
            // Continue without goal
        }
    }

    $pageTitle = $currentGoal
        ? $currentGoal->name.' Peptide Stack: Curated Combinations'
        : 'Peptide Stack Builder';
    $pageDescription = $currentGoal
        ? ($currentGoal->description ?: 'Curated peptide stacks for '.$currentGoal->name.'. Expert-reviewed combinations with dosing, mechanism, and safety guidance.')
        : 'Build your personalized peptide stack based on your health goals. Expert-curated combinations for fat loss, muscle growth, recovery, and more.';
@endphp

<x-public-layout
    :title="$pageTitle"
    :description="$pageDescription"
    :canonical="$currentGoal ? route('stack-builder.goal', $currentGoal->slug) : route('stack-builder')"
>
    @push('head')
    @php
        $stackGoals = collect();
        $goalBundles = collect();
        try {
            $stackGoals = \App\Models\StackGoal::where('is_active', true)
                ->orderBy('display_order')
                ->select('id', 'slug', 'name', 'description')
                ->get();
            if ($currentGoal) {
                $goalBundles = \App\Models\StackBundle::where('stack_goal_id', $currentGoal->id)
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->select('name', 'slug', 'description')
                    ->get();
            }
        } catch (\Throwable $e) {
            // Schema unavailable, continue without
        }

        // Breadcrumbs
        $breadcrumbs = [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Stack Builder', 'item' => route('stack-builder')],
        ];
        if ($currentGoal) {
            $breadcrumbs[] = ['@type' => 'ListItem', 'position' => 3, 'name' => $currentGoal->name];
        }

        $stackSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $pageTitle,
            'description' => $pageDescription,
            'url' => $currentGoal ? route('stack-builder.goal', $currentGoal->slug) : route('stack-builder'),
            'breadcrumb' => [
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbs,
            ],
        ];

        if ($currentGoal && $goalBundles->isNotEmpty()) {
            $stackSchema['mainEntity'] = [
                '@type' => 'ItemList',
                'name' => $currentGoal->name.' peptide stacks',
                'numberOfItems' => $goalBundles->count(),
                'itemListElement' => $goalBundles->values()->map(fn ($b, $i) => array_filter([
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $b->name,
                    'description' => $b->description ?? null,
                ], fn ($v) => $v !== null))->all(),
            ];
        } elseif (!$currentGoal && $stackGoals->isNotEmpty()) {
            $stackSchema['mainEntity'] = [
                '@type' => 'ItemList',
                'name' => 'Peptide stack categories',
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
