<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Results Bank</span>
            <a href="{{ route('admin.results-bank.create') }}" class="btn btn-primary">+ New Entry</a>
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="card p-4 mb-4">
        <form method="GET" action="{{ route('admin.results-bank.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Health Goal</label>
                <select name="health_goal" class="rounded-lg border-gray-300 text-sm focus:border-brand-gold focus:ring-brand-gold">
                    <option value="">All Goals</option>
                    @foreach($healthGoals as $key => $label)
                        <option value="{{ $key }}" {{ request('health_goal') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Experience</label>
                <select name="experience_level" class="rounded-lg border-gray-300 text-sm focus:border-brand-gold focus:ring-brand-gold">
                    <option value="">All Levels</option>
                    <option value="beginner" {{ request('experience_level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="advanced" {{ request('experience_level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="rounded-lg border-gray-300 text-sm focus:border-brand-gold focus:ring-brand-gold">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="btn btn-primary text-sm px-4 py-2">Filter</button>
                @if(request()->hasAny(['health_goal', 'experience_level', 'status']))
                    <a href="{{ route('admin.results-bank.index') }}" class="text-xs text-gray-500 hover:text-gray-700">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Health Goal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Experience</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Peptide</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($results as $result)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $result->goal_label }}</span>
                            <span class="block text-[11px] font-mono text-gray-400 mt-0.5">{{ $result->health_goal }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $result->experience_level === 'beginner' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $result->experience_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $result->peptide_name }}</span>
                            @if($result->peptide_slug)
                                <span class="block text-[11px] font-mono text-gray-400 mt-0.5">{{ $result->peptide_slug }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                <span class="text-yellow-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($result->star_rating))
                                            <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @else
                                            <svg class="w-4 h-4 inline text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endif
                                    @endfor
                                </span>
                                <span class="text-sm text-gray-600 ml-1">{{ $result->star_rating }}</span>
                            </div>
                            <span class="text-xs text-gray-500">{{ $result->rating_label }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $result->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $result->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.results-bank.edit', $result) }}" class="text-brand-gold hover:underline text-sm">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            @if(request()->hasAny(['health_goal', 'experience_level', 'status']))
                                No entries match your filters.
                                <a href="{{ route('admin.results-bank.index') }}" class="text-brand-gold hover:underline">Clear filters</a>
                            @else
                                No results bank entries yet.
                                <a href="{{ route('admin.results-bank.create') }}" class="text-brand-gold hover:underline">Create one</a>
                                or run the seeder: <code class="text-xs bg-gray-100 px-2 py-1 rounded">php artisan db:seed --class=ResultsBankSeeder</code>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($results->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $results->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
