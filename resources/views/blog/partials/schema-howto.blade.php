@props(['post'])

@php
    // Auto-detect HowTo eligibility:
    // 1. Title starts with "How to..." (case-insensitive)
    // 2. Body contains at least 3 h2/h3 headings, treated as steps
    $isHowTo = preg_match('/^how to /i', $post->title);
    $steps = [];

    if ($isHowTo && $post->html) {
        $html = $post->html;
        if (preg_match_all('/<h[23][^>]*>(.*?)<\/h[23]>/is', $html, $matches)) {
            foreach ($matches[1] as $i => $rawHeading) {
                $name = trim(strip_tags($rawHeading));
                if ($name === '' || mb_strlen($name) > 110) continue;

                // Skip generic non-step headings
                if (preg_match('/^(introduction|conclusion|references?|summary|key takeaways?|faq|frequently asked questions)$/i', $name)) continue;

                // Pull body text after this heading until next h2/h3
                $afterPos = strpos($html, $matches[0][$i]);
                $next = isset($matches[0][$i + 1]) ? strpos($html, $matches[0][$i + 1], $afterPos + 1) : null;
                $segment = $next ? substr($html, $afterPos, $next - $afterPos) : substr($html, $afterPos, 800);
                $segmentText = trim(preg_replace('/\s+/', ' ', strip_tags($segment)));
                $segmentText = mb_substr($segmentText, mb_strlen($name));
                $segmentText = trim($segmentText, " :-.\t\n\r");

                if (mb_strlen($segmentText) < 30) continue;

                $steps[] = [
                    '@type' => 'HowToStep',
                    'position' => count($steps) + 1,
                    'name' => $name,
                    'text' => mb_strlen($segmentText) > 500 ? mb_substr($segmentText, 0, 497).'...' : $segmentText,
                    'url' => route('blog.show', $post->slug).'#'.\Illuminate\Support\Str::slug($name),
                ];

                if (count($steps) >= 8) break;
            }
        }
    }

    $emit = $isHowTo && count($steps) >= 3;

    if ($emit) {
        $howtoSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $post->title,
            'description' => $post->meta_description ?: $post->excerpt,
            'image' => $post->featured_image ? (\Illuminate\Support\Str::startsWith($post->featured_image, ['http://','https://']) ? $post->featured_image : url($post->featured_image)) : null,
            'totalTime' => $post->reading_time ? 'PT'.$post->reading_time.'M' : null,
            'step' => $steps,
        ], fn ($v) => $v !== null);
    }
@endphp

@if($emit)
<script type="application/ld+json">
{!! json_encode($howtoSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
