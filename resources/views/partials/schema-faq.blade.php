@php
$faqs = [];

// Benefits FAQ
if (!empty($peptide->key_benefits)) {
    $benefits = is_array($peptide->key_benefits) ? implode(' ', $peptide->key_benefits) : $peptide->key_benefits;
    $faqs[] = [
        'q' => 'What are the benefits of ' . $peptide->name . '?',
        'a' => Str::limit(strip_tags($benefits), 300),
    ];
}

// Safety FAQ
if (!empty($peptide->safety_warnings) && count($peptide->safety_warnings) > 0) {
    $warnings = collect($peptide->safety_warnings)->take(5)->map(fn($w) => strip_tags($w))->implode(' ');
    $faqs[] = [
        'q' => 'What are the side effects and safety warnings for ' . $peptide->name . '?',
        'a' => Str::limit($warnings, 300),
    ];
}

// Dosing / Protocol FAQ
if (!empty($peptide->protocols) && count($peptide->protocols) > 0) {
    $proto = $peptide->protocols[0];
    $doseAnswer = $peptide->name . ' is commonly dosed at ' . ($proto['dose'] ?? 'varies') . ', ' . strtolower($proto['frequency'] ?? '') . ' via ' . strtolower($proto['route'] ?? 'injection') . '.';
    if (!empty($proto['goal'])) {
        $doseAnswer .= ' This protocol targets: ' . $proto['goal'] . '.';
    }
    $faqs[] = [
        'q' => 'What is the recommended dosage for ' . $peptide->name . '?',
        'a' => $doseAnswer,
    ];
}

// Mechanism FAQ
if (!empty($peptide->mechanism_of_action)) {
    $faqs[] = [
        'q' => 'How does ' . $peptide->name . ' work?',
        'a' => Str::limit(strip_tags($peptide->mechanism_of_action), 300),
    ];
}

$faqSchema = null;
if (count($faqs) >= 2) {
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($faqs)->map(fn($faq) => [
            '@type' => 'Question',
            'name' => $faq['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['a'],
            ],
        ])->all(),
    ];
}
@endphp
@if($faqSchema)
<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
