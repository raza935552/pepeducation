@if($relatedPosts->isNotEmpty())
    <section class="mt-16 pt-12 border-t border-gray-200">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedPosts as $relatedPost)
                @include('blog.partials.card', ['post' => $relatedPost])
            @endforeach
        </div>
    </section>
@endif
