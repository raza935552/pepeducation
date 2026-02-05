<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
    <!-- Peptide Info -->
    <td class="px-6 py-4">
        <div class="flex items-center">
            <div>
                <div class="font-semibold text-gray-900 dark:text-white">{{ $peptide->name }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $peptide->full_name }}</div>
            </div>
        </div>
    </td>

    <!-- Type -->
    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
        {{ $peptide->type ?? '-' }}
    </td>

    <!-- Categories -->
    <td class="px-6 py-4">
        <div class="flex flex-wrap gap-1">
            @foreach($peptide->categories->take(3) as $category)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                      style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                    {{ $category->name }}
                </span>
            @endforeach
            @if($peptide->categories->count() > 3)
                <span class="text-xs text-gray-500">+{{ $peptide->categories->count() - 3 }}</span>
            @endif
        </div>
    </td>

    <!-- Research Status -->
    <td class="px-6 py-4">
        @php $badge = $peptide->research_status_badge; @endphp
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
            {{ $badge['color'] === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
            {{ $badge['color'] === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
            {{ $badge['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
            {{ $badge['color'] === 'gray' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}">
            {{ $badge['label'] }}
        </span>
    </td>

    <!-- Published Status -->
    <td class="px-6 py-4">
        <form action="{{ route('admin.peptides.toggle-publish', $peptide) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="inline-flex items-center gap-1.5 text-sm font-medium transition-colors
                {{ $peptide->is_published
                    ? 'text-green-600 hover:text-green-700 dark:text-green-400'
                    : 'text-gray-400 hover:text-gray-600 dark:text-gray-500' }}">
                <span class="w-2 h-2 rounded-full {{ $peptide->is_published ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                {{ $peptide->is_published ? 'Published' : 'Draft' }}
            </button>
        </form>
    </td>

    <!-- Actions -->
    <td class="px-6 py-4 text-right">
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.peptides.edit', $peptide) }}"
               class="p-2 text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors"
               title="Edit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <form action="{{ route('admin.peptides.destroy', $peptide) }}" method="POST" class="inline"
                  onsubmit="return confirm('Delete {{ $peptide->name }}?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors"
                        title="Delete">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
