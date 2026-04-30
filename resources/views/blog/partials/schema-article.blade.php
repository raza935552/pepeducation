@props(['post'])

@php
$articleImage = null;
if (!empty($post->featured_image)) {
    $articleImage = \Illuminate\Support\Str::startsWith($post->featured_image, ['http://','https://'])
        ? $post->featured_image
        : url($post->featured_image);
} else {
    $logoUrl = \App\Models\Setting::getValue('branding', 'logo_url', null);
    if (!empty($logoUrl)) {
        $articleImage = \Illuminate\Support\Str::startsWith($logoUrl, ['http://','https://'])
            ? $logoUrl
            : url($logoUrl);
    }
}

$articleDescription = trim((string) ($post->meta_description ?? $post->excerpt ?? ''));

$articleSchema = array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $post->title,
    'description' => $articleDescription !== '' ? $articleDescription : null,
    'image' => $articleImage,
    'datePublished' => $post->published_at?->toIso8601String(),
    'dateModified' => $post->updated_at->toIso8601String(),
    'author' => [
        '@type' => 'Person',
        'name' => $post->author?->name ?? 'PepProfesor',
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => config('app.name'),
        'url' => url('/'),
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => route('blog.show', $post->slug),
    ],
    'wordCount' => str_word_count(strip_tags($post->html ?? '')),
    'breadcrumb' => [
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title],
        ],
    ],
], fn ($v) => $v !== null);
@endphp

<script type="application/ld+json">
{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
