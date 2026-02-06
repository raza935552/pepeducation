<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.quizzes.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg aria-hidden="true" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span>Create Quiz</span>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form action="{{ route('admin.quizzes.store') }}" method="POST" class="card p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Type</label>
                <select name="type" required class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    <option value="segmentation">Segmentation Quiz (TOF/MOF/BOF)</option>
                    <option value="product">Product Finder Quiz</option>
                    <option value="custom">Custom Quiz</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Segmentation quiz determines funnel position. Product quiz recommends peptides.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Klaviyo List ID (optional)</label>
                <input type="text" name="klaviyo_list_id" value="{{ old('klaviyo_list_id') }}"
                    class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold"
                    placeholder="Enter Klaviyo list ID to add completers">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                <label for="is_active" class="text-sm text-gray-700">Activate quiz after creation</label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create & Add Questions</button>
            </div>
        </form>
    </div>
</x-admin-layout>
