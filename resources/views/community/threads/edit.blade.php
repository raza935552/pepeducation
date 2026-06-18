<x-app-layout>
    @push('head')
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
        <title>Edit discussion — Community</title>
    @endpush

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('community.threads.show', $thread) }}" class="hover:text-indigo-600">← Back to discussion</a>
        </nav>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit discussion</h1>

        <form method="POST" action="{{ route('community.threads.update', $thread) }}" class="rounded-xl border border-gray-200 bg-white p-6 space-y-5">
            @csrf @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string)old('category_id', $thread->category_id) === (string)$category->id)>{{ $category->icon }} {{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $thread->title) }}" required minlength="8" maxlength="160"
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('title')<p class="text-sm text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Body</label>
                <textarea name="body" rows="10" required minlength="15" maxlength="20000"
                          class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('body', app(\App\Services\ForumContentSanitizer::class)->toPlainText($thread->body)) }}</textarea>
                @error('body')<p class="text-sm text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('community.threads.show', $thread) }}" class="text-gray-500 hover:text-gray-700">Cancel</a>
                <button class="rounded-full bg-indigo-600 text-white font-semibold px-6 py-2.5 hover:bg-indigo-700 transition">Save changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
