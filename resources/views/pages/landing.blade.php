<x-public-layout :title="$page->meta_title ?? $page->title" :description="$page->meta_description" :image="$page->featured_image ? url($page->featured_image) : null">
    @if($page->css)
        <style>{!! $page->sanitizedCss() !!}</style>
    @endif
    <div class="gjs-landing-content">
        {!! $page->sanitizedHtml() !!}
    </div>
</x-public-layout>
