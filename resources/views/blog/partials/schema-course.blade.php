@props(['post'])

@php
    // Auto-detect course-eligible content. Markers:
    //  - title contains "guide", "complete guide", "primer", or "101"
    //  - body has 5+ h2 sections
    //  - reading_time >= 7 minutes
    $titleLower = mb_strtolower($post->title);
    $isPillar = (
        str_contains($titleLower, 'complete guide') ||
        str_contains($titleLower, 'beginner') ||
        str_contains($titleLower, 'primer') ||
        str_contains($titleLower, '101') ||
        preg_match('/\bguide\b/', $titleLower)
    );

    $h2Count = 0;
    if (!empty($post->html)) {
        $h2Count = preg_match_all('/<h2[^>]*>/i', $post->html);
    }

    $emit = $isPillar && $h2Count >= 5 && ($post->reading_time ?? 0) >= 7;

    if ($emit) {
        $courseSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'LearningResource',
            'name' => $post->title,
            'description' => $post->meta_description ?: $post->excerpt,
            'url' => route('blog.show', $post->slug),
            'image' => $post->featured_image ? (\Illuminate\Support\Str::startsWith($post->featured_image, ['http://','https://']) ? $post->featured_image : url($post->featured_image)) : null,
            'inLanguage' => 'en',
            'learningResourceType' => 'Article',
            'educationalLevel' => 'Beginner to Intermediate',
            'about' => [
                '@type' => 'Thing',
                'name' => 'Peptide Research',
            ],
            'author' => $post->author ? [
                '@type' => 'Person',
                'name' => $post->author->name,
            ] : null,
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => config('app.url'),
            ],
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'timeRequired' => $post->reading_time ? 'PT'.$post->reading_time.'M' : null,
        ], fn ($v) => $v !== null);
    }
@endphp

@if($emit)
<script type="application/ld+json">
{!! json_encode($courseSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
