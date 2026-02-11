<x-page-builder-layout title="Edit: {{ $blogPost->title }}">
    @include('admin.blog-posts.partials.builder', ['blogPost' => $blogPost])
</x-page-builder-layout>
