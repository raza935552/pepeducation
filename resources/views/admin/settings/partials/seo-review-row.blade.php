<div x-data="seoRow({{ json_encode($itemId) }}, '{{ $itemType }}', {{ json_encode($metaTitle ?? '') }}, {{ json_encode($metaDescription ?? '') }})"
     x-show="visible"
     class="card p-3 hover:shadow-md transition">

    {{-- Header: name + status + actions --}}
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2 min-w-0">
            {{-- Status dot --}}
            <span class="w-2 h-2 rounded-full shrink-0"
                :class="isGood ? 'bg-green-500' : (isMissing ? 'bg-red-500' : 'bg-yellow-500')"></span>
            <span class="font-medium text-sm text-gray-900 truncate">{{ $itemName }}</span>
            <a href="{{ $itemSlug }}" target="_blank" class="text-xs text-gray-400 hover:text-blue-500 shrink-0">{{ $itemSlug }}</a>
        </div>
        <div class="flex items-center gap-1 shrink-0">
            {{-- Message --}}
            <span x-show="message" x-cloak class="text-[10px] mr-1" :class="messageSuccess ? 'text-green-600' : 'text-red-600'" x-text="message"></span>

            <template x-if="!editing">
                <div class="flex gap-0.5">
                    {{-- Edit --}}
                    <button @click="editing = true" class="p-1.5 text-gray-400 hover:text-blue-600 rounded hover:bg-blue-50" title="Edit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    {{-- Regenerate (peptides only) --}}
                    @if($canRegenerate ?? false)
                    <button @click="regenerate()" :disabled="regenerating" class="p-1.5 text-gray-400 hover:text-purple-600 rounded hover:bg-purple-50" title="Regenerate with AI">
                        <svg x-show="!regenerating" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <svg x-show="regenerating" class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </button>
                    @endif
                    {{-- Open editor --}}
                    <a href="{{ $editUrl }}" class="p-1.5 text-gray-400 hover:text-gray-600 rounded hover:bg-gray-50" title="Open editor">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </template>
            <template x-if="editing">
                <div class="flex gap-0.5">
                    <button @click="save()" :disabled="saving" class="p-1.5 text-green-600 hover:text-green-800 rounded hover:bg-green-50" title="Save">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <button @click="cancel()" class="p-1.5 text-red-400 hover:text-red-600 rounded hover:bg-red-50" title="Cancel">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>
    </div>

    {{-- Title --}}
    <div class="mb-1.5">
        <div class="flex items-center gap-2 mb-0.5">
            <span class="text-[10px] font-medium text-gray-400 uppercase">Title</span>
            <span class="text-[10px]"
                :class="!hasTitle ? 'text-red-400 font-medium' : (title.length > 60 ? 'text-red-500' : (title.length > 50 ? 'text-yellow-500' : 'text-gray-400'))"
                x-text="hasTitle ? title.length + '/60' : 'MISSING'"
                :data-title-issue="hasTitle && title.length > 60 ? true : undefined"></span>
        </div>
        <template x-if="!editing">
            <div class="text-xs text-gray-700 truncate" x-text="title || '—'"></div>
        </template>
        <template x-if="editing">
            <input type="text" x-model="title" class="w-full text-xs rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-1.5 px-2">
        </template>
    </div>

    {{-- Description --}}
    <div>
        <div class="flex items-center gap-2 mb-0.5">
            <span class="text-[10px] font-medium text-gray-400 uppercase">Description</span>
            <span class="text-[10px]"
                :class="!hasDesc ? 'text-red-400 font-medium' : (desc.length > 155 ? 'text-red-500' : (desc.length > 140 ? 'text-yellow-500' : 'text-gray-400'))"
                x-text="hasDesc ? desc.length + '/155' : 'MISSING'"
                :data-desc-issue="hasDesc && desc.length > 155 ? true : undefined"></span>
        </div>
        <template x-if="!editing">
            <div class="text-xs text-gray-600 line-clamp-2" x-text="desc || '—'"></div>
        </template>
        <template x-if="editing">
            <textarea x-model="desc" rows="2" class="w-full text-xs rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-1.5 px-2"></textarea>
        </template>
    </div>
</div>
