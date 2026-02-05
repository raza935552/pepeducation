<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
    <td class="px-6 py-4">
        <div class="flex items-center gap-3">
            @if($supporter->logo)
                <img src="{{ Storage::url($supporter->logo) }}" alt="{{ $supporter->name }}"
                     class="w-10 h-10 rounded-lg object-contain bg-gray-100 dark:bg-gray-700 p-1">
            @else
                <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            @endif
            <div>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ $supporter->name }}
                    @if($supporter->is_featured)
                        <span class="ml-1 text-gold-500" title="Featured">
                            <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </span>
                    @endif
                </p>
                @if($supporter->website_url)
                    <a href="{{ $supporter->website_url }}" target="_blank" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gold-500">
                        {{ parse_url($supporter->website_url, PHP_URL_HOST) }}
                    </a>
                @endif
            </div>
        </div>
    </td>
    <td class="px-6 py-4">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $supporter->tier_color }}">
            {{ ucfirst($supporter->tier) }}
        </span>
    </td>
    <td class="px-6 py-4">
        @if($supporter->status === 'active')
            <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400 text-sm">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                Active
            </span>
        @else
            <span class="inline-flex items-center gap-1 text-gray-500 dark:text-gray-400 text-sm">
                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                Inactive
            </span>
        @endif
    </td>
    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
        {{ $supporter->display_order }}
    </td>
    <td class="px-6 py-4 text-right">
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.supporters.edit', $supporter) }}"
               class="p-2 text-gray-500 hover:text-gold-600 dark:text-gray-400 dark:hover:text-gold-400 transition-colors"
               title="Edit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <form action="{{ route('admin.supporters.destroy', $supporter) }}" method="POST" class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this supporter?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors"
                        title="Delete">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
