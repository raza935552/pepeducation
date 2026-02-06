<tr class="hover:bg-gray-50 transition-colors"
    x-data="{ editing: false, name: '{{ $category->name }}', color: '{{ $category->color }}' }">
    <!-- Category -->
    <td class="px-6 py-4">
        <template x-if="!editing">
            <div class="flex items-center gap-3">
                <span class="w-4 h-4 rounded-full" style="background-color: {{ $category->color }}"></span>
                <span class="font-medium text-gray-900">{{ $category->name }}</span>
            </div>
        </template>
        <template x-if="editing">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST"
                  class="flex items-center gap-2" @submit="editing = false">
                @csrf
                @method('PUT')
                <input type="color" name="color" x-model="color" class="w-8 h-8 rounded cursor-pointer">
                <input type="text" name="name" x-model="name" class="input flex-1 text-sm py-1" required>
                <button type="submit" class="p-1 text-green-600 hover:text-green-700">
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
                <button type="button" @click="editing = false" class="p-1 text-gray-400 hover:text-gray-600">
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </form>
        </template>
    </td>

    <!-- Peptides Count -->
    <td class="px-6 py-4 text-center">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            {{ $category->peptides_count }}
        </span>
    </td>

    <!-- Actions -->
    <td class="px-6 py-4 text-right">
        <template x-if="!editing">
            <div class="flex items-center justify-end gap-2">
                <button type="button" @click="editing = true"
                        class="p-2 text-gray-500 hover:text-primary-600 transition-colors"
                        title="Edit">
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                @if($category->peptides_count === 0)
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete {{ $category->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="p-2 text-gray-500 hover:text-red-600 transition-colors"
                                title="Delete">
                            <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                @else
                    <span class="p-2 text-gray-300 cursor-not-allowed" title="Has peptides">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </span>
                @endif
            </div>
        </template>
    </td>
</tr>
