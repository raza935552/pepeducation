<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
    <td class="px-6 py-4">
        <div>
            <a href="{{ route('admin.pages.edit', $page) }}" class="font-medium text-gray-900 dark:text-white hover:text-gold-500">
                {{ $page->title }}
            </a>
            <p class="text-sm text-gray-500 dark:text-gray-400">/{{ $page->slug }}</p>
        </div>
    </td>
    <td class="px-6 py-4 text-center">
        @if($page->isPublished())
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                Published
            </span>
        @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                Draft
            </span>
        @endif
    </td>
    <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400">
        {{ $page->author?->name ?? 'Unknown' }}
    </td>
    <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400">
        {{ $page->updated_at->diffForHumans() }}
    </td>
    <td class="px-6 py-4 text-right">
        <div class="flex items-center justify-end gap-2">
            @if($page->isPublished())
                <a href="{{ route('page.show', $page->slug) }}" target="_blank"
                   class="p-2 text-gray-400 hover:text-blue-500 transition-colors" title="View">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </a>
            @endif
            <a href="{{ route('admin.pages.edit', $page) }}"
               class="p-2 text-gray-400 hover:text-gold-500 transition-colors" title="Edit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <form action="{{ route('admin.pages.duplicate', $page) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="p-2 text-gray-400 hover:text-purple-500 transition-colors" title="Duplicate">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </form>
            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this page?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
