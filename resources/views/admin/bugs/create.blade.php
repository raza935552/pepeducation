<x-admin-layout>
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.bugs.index') }}" class="text-sm text-gray-500 hover:text-gold-500 flex items-center gap-1">
                <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Bug Reports
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Report a Bug</h1>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <form action="{{ route('admin.bugs.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           placeholder="Short summary of the bug"
                           class="w-full rounded-lg border-gray-300 focus:ring-gold-500 focus:border-gold-500">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" rows="6" required
                              placeholder="Describe the bug in detail: what happened, what you expected, steps to reproduce..."
                              class="w-full rounded-lg border-gray-300 focus:ring-gold-500 focus:border-gold-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="page_url" class="block text-sm font-medium text-gray-700 mb-2">Page URL</label>
                    <input type="text" name="page_url" id="page_url" value="{{ old('page_url') }}"
                           placeholder="e.g. /admin/quizzes/1/edit"
                           class="w-full rounded-lg border-gray-300 focus:ring-gold-500 focus:border-gold-500">
                    @error('page_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority" id="priority"
                            class="w-full rounded-lg border-gray-300 focus:ring-gold-500 focus:border-gold-500">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-6 py-2.5 bg-gold-500 text-white rounded-lg hover:bg-gold-600 font-medium">
                        Submit Bug Report
                    </button>
                    <a href="{{ route('admin.bugs.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
