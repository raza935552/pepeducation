@php
    $hasSteps = is_array($peptide->reconstitution_steps ?? null) && count($peptide->reconstitution_steps) >= 3;
@endphp

@if($hasSteps)
@php
    $howToSteps = [];
    foreach ($peptide->reconstitution_steps as $i => $stepText) {
        $clean = trim((string) $stepText);
        if ($clean === '') continue;
        $howToSteps[] = [
            '@type' => 'HowToStep',
            'position' => count($howToSteps) + 1,
            'name' => 'Step '.(count($howToSteps) + 1),
            'text' => $clean,
        ];
    }

    $supplies = [
        ['@type' => 'HowToSupply', 'name' => 'Lyophilized '.$peptide->name.' vial'],
        ['@type' => 'HowToSupply', 'name' => 'Bacteriostatic water (0.9% benzyl alcohol)'],
        ['@type' => 'HowToSupply', 'name' => 'Sterile insulin syringe (29-31 gauge)'],
        ['@type' => 'HowToSupply', 'name' => 'Alcohol pads'],
        ['@type' => 'HowToSupply', 'name' => 'Sharps disposal container'],
    ];

    $howToSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'HowTo',
        'name' => 'How to Reconstitute '.$peptide->name,
        'description' => 'Step-by-step instructions for reconstituting '.$peptide->name.' from lyophilized powder using bacteriostatic water for research preparation.',
        'totalTime' => 'PT5M',
        'supply' => $supplies,
        'step' => $howToSteps,
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($howToSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
