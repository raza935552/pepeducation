<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Edit: {{ $blogPost->title }}</span>
            <div class="flex items-center gap-3">
                <a href="{{ route('blog.show', $blogPost->slug) }}" target="_blank" class="text-sm text-blue-500 hover:text-blue-700">View Post</a>
                <a href="{{ route('admin.blog-posts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Posts
                </a>
            </div>
        </div>
    </x-slot>

    @include('admin.blog-posts.partials.form', ['blogPost' => $blogPost])
</x-admin-layout>
