<div
    x-data="{
        open: @entangle('isOpen'),
        init() {
            this.$watch('open', value => {
                if (value) {
                    document.body.classList.add('overflow-hidden');
                    this.$nextTick(() => this.$refs.searchInput?.focus());
                } else {
                    document.body.classList.remove('overflow-hidden');
                }
            });
        }
    }"
    x-show="open"
    x-cloak
    @keydown.cmd.k.window.prevent="open = true"
    @keydown.ctrl.k.window.prevent="open = true"
    @open-search.window="open = true"
    @keydown.escape.window="if (open) { open = false; $wire.close(); }"
    @keydown.arrow-down.prevent="$wire.selectNext()"
    @keydown.arrow-up.prevent="$wire.selectPrevious()"
    @keydown.enter.prevent="$wire.goToSelected()"
    class="fixed inset-0 z-[100] overflow-y-auto"
    role="dialog"
    aria-modal="true"
    aria-labelledby="search-modal-title"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false; $wire.close()"
        class="fixed inset-0 bg-brown-950/60 backdrop-blur-sm"
        aria-hidden="true"
    ></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 flex items-start justify-center px-4 pt-[10vh] sm:pt-[15vh]">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            x-trap.inert.noscroll="open"
            @click.outside="open = false; $wire.close()"
            class="relative w-full max-w-xl overflow-hidden rounded-2xl bg-brown-800 border border-brown-700/50 shadow-2xl shadow-black/40 ring-1 ring-white/5"
        >
            <!-- Search Input Section -->
            <div class="relative border-b border-brown-700/50">
                <!-- Search Icon -->
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg aria-hidden="true" class="h-5 w-5 text-cream-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>

                <!-- Input Field -->
                <input
                    x-ref="searchInput"
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    id="search-modal-title"
                    class="block w-full bg-transparent py-4 pl-12 pr-12 text-base text-cream-100 placeholder-cream-500 border-0 focus:ring-0 focus:outline-none"
                    placeholder="Search peptides, categories..."
                    autocomplete="off"
                    autocorrect="off"
                    autocapitalize="off"
                    spellcheck="false"
                >

                <!-- Loading Spinner -->
                <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 flex items-center pr-4">
                    <svg aria-hidden="true" class="h-5 w-5 animate-spin text-gold-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <!-- Clear Button -->
                @if(strlen($search) > 0)
                    <button
                        wire:click="$set('search', '')"
                        wire:loading.remove
                        wire:target="search"
                        type="button"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-cream-500 hover:text-cream-300 transition-colors"
                    >
                        <svg aria-hidden="true" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Results Section -->
            <div class="max-h-[60vh] overflow-y-auto overscroll-contain">
                @if(strlen($search) >= 2)
                    @if(count($this->results) > 0)
                        <!-- Results List -->
                        <ul class="py-2" role="listbox">
                            @foreach($this->results as $index => $result)
                                <li
                                    wire:key="result-{{ $index }}"
                                    wire:click="$set('selectedIndex', {{ $index }}); $wire.goToSelected()"
                                    role="option"
                                    aria-selected="{{ $selectedIndex === $index ? 'true' : 'false' }}"
                                    class="group relative mx-2 flex cursor-pointer items-center gap-3 rounded-xl px-3 py-3 transition-all duration-150 {{ $selectedIndex === $index ? 'bg-gold-600/20 text-cream-100' : 'text-cream-300 hover:bg-brown-700/50' }}"
                                >
                                    <!-- Result Icon -->
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg {{ $selectedIndex === $index ? 'bg-gold-600/30 text-gold-400' : 'bg-brown-700/50 text-cream-500 group-hover:bg-brown-700 group-hover:text-cream-400' }} transition-colors">
                                        @if($result['type'] === 'peptide')
                                            <!-- Peptide Icon (molecule) -->
                                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                                            </svg>
                                        @elseif($result['type'] === 'category')
                                            <!-- Category Icon (folder) -->
                                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                            </svg>
                                        @else
                                            <!-- Generic Icon -->
                                            <svg aria-hidden="true" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        @endif
                                    </div>

                                    <!-- Result Content -->
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium {{ $selectedIndex === $index ? 'text-cream-100' : 'text-cream-200' }}">
                                            {{ $result['name'] }}
                                        </p>
                                        @if(!empty($result['subtitle']))
                                            <p class="truncate text-xs {{ $selectedIndex === $index ? 'text-gold-300' : 'text-cream-500' }}">
                                                {{ $result['subtitle'] }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Arrow Indicator -->
                                    <div class="{{ $selectedIndex === $index ? 'opacity-100' : 'opacity-0 group-hover:opacity-50' }} transition-opacity">
                                        <svg aria-hidden="true" class="h-5 w-5 text-cream-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </div>

                                    <!-- Type Badge -->
                                    <span class="flex-shrink-0 rounded-md px-2 py-0.5 text-xs font-medium {{ $selectedIndex === $index ? 'bg-gold-600/30 text-gold-300' : 'bg-brown-700/50 text-cream-500' }} capitalize transition-colors">
                                        {{ $result['type'] }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <!-- No Results -->
                        <div class="px-6 py-12 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brown-700/50">
                                <svg aria-hidden="true" class="h-6 w-6 text-cream-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-cream-400">
                                No results found for "<span class="font-medium text-cream-300">{{ $search }}</span>"
                            </p>
                            <p class="mt-1 text-xs text-cream-500">
                                Try searching with different keywords
                            </p>
                        </div>
                    @endif
                @else
                    <!-- Initial State - Quick Links -->
                    <div class="p-4">
                        <p class="mb-3 px-2 text-xs font-semibold uppercase tracking-wider text-cream-500">
                            Quick Links
                        </p>
                        <div class="space-y-1">
                            <a href="{{ route('peptides.index') }}" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-cream-300 transition-colors hover:bg-brown-700/50 hover:text-cream-100">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gold-600/20 text-gold-400 transition-colors group-hover:bg-gold-600/30">
                                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">All Peptides</p>
                                    <p class="text-xs text-cream-500">Browse our complete database</p>
                                </div>
                                <svg aria-hidden="true" class="h-4 w-4 text-brown-600 transition-colors group-hover:text-cream-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>

                            <a href="{{ route('peptides.index') }}" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-cream-300 transition-colors hover:bg-brown-700/50 hover:text-cream-100">
                                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-caramel-600/20 text-caramel-400 transition-colors group-hover:bg-caramel-600/30">
                                    <svg aria-hidden="true" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">Categories</p>
                                    <p class="text-xs text-cream-500">Explore by category</p>
                                </div>
                                <svg aria-hidden="true" class="h-4 w-4 text-brown-600 transition-colors group-hover:text-cream-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </div>

                        <!-- Search Tip -->
                        <div class="mt-4 rounded-xl bg-brown-700/30 px-4 py-3">
                            <p class="flex items-center gap-2 text-xs text-cream-500">
                                <svg aria-hidden="true" class="h-4 w-4 flex-shrink-0 text-gold-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                                </svg>
                                <span>Type at least 2 characters to search peptides and categories</span>
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer with Keyboard Shortcuts -->
            <div class="flex items-center justify-between border-t border-brown-700/50 bg-brown-800/50 px-4 py-2.5">
                <div class="flex items-center gap-4 text-xs text-cream-500">
                    <span class="flex items-center gap-1.5">
                        <kbd class="inline-flex h-5 min-w-[20px] items-center justify-center rounded bg-brown-700 px-1.5 font-mono text-[10px] font-medium text-cream-300">
                            <svg aria-hidden="true" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                            </svg>
                        </kbd>
                        <kbd class="inline-flex h-5 min-w-[20px] items-center justify-center rounded bg-brown-700 px-1.5 font-mono text-[10px] font-medium text-cream-300">
                            <svg aria-hidden="true" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </kbd>
                        <span>Navigate</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <kbd class="inline-flex h-5 min-w-[20px] items-center justify-center rounded bg-brown-700 px-1.5 font-mono text-[10px] font-medium text-cream-300">
                            <svg aria-hidden="true" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                            </svg>
                        </kbd>
                        <span>Select</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <kbd class="inline-flex h-5 min-w-[20px] items-center justify-center rounded bg-brown-700 px-1.5 font-mono text-[10px] font-medium text-cream-300">esc</kbd>
                        <span>Close</span>
                    </span>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-cream-500">
                    <kbd class="inline-flex h-5 items-center justify-center rounded bg-brown-700 px-1.5 font-mono text-[10px] font-medium text-cream-300">
                        <svg aria-hidden="true" class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.5 17.5L22 22M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        </svg>
                    </kbd>
                    <kbd class="inline-flex h-5 items-center justify-center rounded bg-brown-700 px-1.5 font-mono text-[10px] font-medium text-cream-300">K</kbd>
                    <span>to open</span>
                </div>
            </div>
        </div>
    </div>
</div>
