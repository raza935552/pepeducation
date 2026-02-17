<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Quizzes</span>
            <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                <svg aria-hidden="true" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Quiz
            </a>
        </div>
    </x-slot>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Questions</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Completions</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($quizzes as $quiz)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $quiz->name }}</div>
                                <div class="text-sm text-gray-500">/quiz/{{ $quiz->slug }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $quiz->type === 'segmentation' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $quiz->type === 'product' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $quiz->type === 'custom' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($quiz->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $quiz->questions_count }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-gray-900">{{ number_format($quiz->completions_count) }}</div>
                                <div class="text-xs text-gray-500">{{ $quiz->getCompletionRate() }}% rate</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($quiz->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="text-brand-gold hover:underline">Edit</a>
                                <form action="{{ route('admin.quizzes.duplicate', $quiz) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:underline">Duplicate</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">No quizzes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quizzes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $quizzes->links() }}</div>
        @endif
    </div>
</x-admin-layout>
