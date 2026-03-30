{{-- Shared Accordion Sections — renders nothing if no items configured --}}
@php
    $accordionItems = $this->currentSlide['settings']['accordion_items'] ?? [];
@endphp

@if(!empty($accordionItems))
<div class="mt-6" x-data="{ openItem: null }">
    <div class="space-y-2">
        @foreach($accordionItems as $index => $item)
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <button type="button"
                    @click="openItem = openItem === {{ $index }} ? null : {{ $index }}"
                    class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-gray-50 transition-colors">
                    <span class="font-medium text-gray-800 text-sm">{{ $item['title'] }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                        :class="openItem === {{ $index }} && 'rotate-180'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openItem === {{ $index }}" x-collapse x-cloak>
                    <div class="px-4 pb-4 text-sm text-gray-600 leading-relaxed">
                        {!! nl2br(e($item['content'])) !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
