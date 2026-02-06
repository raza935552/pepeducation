<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.quizzes.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg aria-hidden="true" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <span>Edit: {{ $quiz->name }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">{{ $quiz->completions_count }} completions</span>
                @if($quiz->is_active)
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                @endif
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Settings -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Quiz Settings -->
            <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" class="card p-6">
                @csrf @method('PUT')
                <h3 class="text-lg font-semibold mb-4">Quiz Settings</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" value="{{ $quiz->name }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" value="{{ $quiz->slug }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <option value="segmentation" {{ $quiz->type === 'segmentation' ? 'selected' : '' }}>Segmentation</option>
                            <option value="product" {{ $quiz->type === 'product' ? 'selected' : '' }}>Product</option>
                            <option value="custom" {{ $quiz->type === 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Klaviyo List ID</label>
                        <input type="text" name="klaviyo_list_id" value="{{ $quiz->klaviyo_list_id }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ $quiz->is_active ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                        <span class="text-sm">Active</span>
                    </label>
                    <button type="submit" class="btn btn-primary ml-auto">Save Settings</button>
                </div>
            </form>

            <!-- Questions -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Questions ({{ $quiz->questions->count() }})</h3>
                    <button type="button" onclick="showAddQuestion()" class="btn btn-secondary text-sm">+ Add Question</button>
                </div>

                <div id="questions-list" class="space-y-4">
                    @foreach($quiz->questions as $question)
                        @include('admin.quizzes.partials.question-row', ['question' => $question])
                    @endforeach
                </div>
            </div>

            <!-- Outcomes -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Outcomes ({{ $quiz->outcomes->count() }})</h3>
                    <button type="button" onclick="showAddOutcome()" class="btn btn-secondary text-sm">+ Add Outcome</button>
                </div>

                <div id="outcomes-list" class="space-y-4">
                    @foreach($quiz->outcomes as $outcome)
                        @include('admin.quizzes.partials.outcome-row', ['outcome' => $outcome])
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Quiz URL</h3>
                <div class="bg-gray-50 rounded-lg p-3 text-sm font-mono break-all">
                    {{ url('/quiz/' . $quiz->slug) }}
                </div>
                <button type="button" onclick="copyToClipboard('{{ url('/quiz/' . $quiz->slug) }}')"
                    class="mt-2 text-sm text-brand-gold hover:underline">Copy URL</button>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Stats</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Started</dt>
                        <dd class="font-medium">{{ number_format($quiz->starts_count) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Completed</dt>
                        <dd class="font-medium">{{ number_format($quiz->completions_count) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Completion Rate</dt>
                        <dd class="font-medium">{{ $quiz->getCompletionRate() }}%</dd>
                    </div>
                </dl>
            </div>

            <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" class="card p-6"
                onsubmit="return confirm('Delete this quiz and all its questions?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full btn bg-red-500 text-white hover:bg-red-600">Delete Quiz</button>
            </form>
        </div>
    </div>

    @include('admin.quizzes.partials.question-modal')
    @include('admin.quizzes.partials.outcome-modal')

    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert('Copied!');
    }
    </script>
</x-admin-layout>
