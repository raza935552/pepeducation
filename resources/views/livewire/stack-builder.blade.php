<div class="stack-builder" x-data="{ productTab: @entangle('productTab'), expandedBundle: null }">

    {{-- ===== HERO + PROGRESS ===== --}}
    <header class="text-center mb-10 md:mb-14">
        <h1 class="text-3xl md:text-5xl font-bold mb-3">
            <span class="text-gradient-gold">{{ $this->settings['hero_title'] ?? 'Build Your Peptide Stack' }}</span>
        </h1>
        <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto mb-10">
            {{ $this->settings['hero_subtitle'] ?? 'Select your goal and we\'ll recommend the perfect peptide combination.' }}
        </p>

        {{-- Progress Bar --}}
        <div class="max-w-sm mx-auto">
            <div class="flex items-center justify-between relative">
                {{-- Connecting line (background) --}}
                <div class="absolute top-5 left-10 right-10 h-0.5 bg-cream-200 rounded-full"></div>
                {{-- Connecting line (filled) --}}
                <div class="absolute top-5 left-10 h-0.5 bg-gradient-to-r from-gold-500 to-gold-400 rounded-full transition-all duration-700 ease-out"
                     style="width: calc({{ $this->progress }}% * 0.7)"></div>

                @php
                    $steps = [
                        1 => ['label' => 'Goal', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>'],
                        2 => ['label' => 'Stacks', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>'],
                        3 => ['label' => 'Browse', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>'],
                    ];
                @endphp

                @foreach($steps as $num => $step)
                    <div class="relative z-10 flex flex-col items-center gap-2">
                        @if($num < $currentStep)
                            {{-- Completed --}}
                            <button wire:click="goToStep({{ $num }})"
                                class="w-10 h-10 rounded-full bg-gold-500 text-white flex items-center justify-center shadow-md hover:shadow-lg hover:scale-110 transition-all duration-200 cursor-pointer">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        @elseif($num === $currentStep)
                            {{-- Active --}}
                            <div class="w-10 h-10 rounded-full bg-gold-500 text-white flex items-center justify-center shadow-md ring-4 ring-gold-500/20 animate-scale-in">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">{!! $step['icon'] !!}</svg>
                            </div>
                        @else
                            {{-- Upcoming --}}
                            <div class="w-10 h-10 rounded-full bg-cream-100 border-2 border-cream-200 text-cream-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">{!! $step['icon'] !!}</svg>
                            </div>
                        @endif
                        <span class="text-xs font-semibold {{ $num <= $currentStep ? 'text-gray-700' : 'text-cream-400' }}">{{ $step['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </header>

    {{-- ===== STEP 1: CHOOSE YOUR GOAL ===== --}}
    <section class="mb-5" wire:key="step-1">
        @if($currentStep === 1)
            {{-- Active State --}}
            <div class="card glow-gold animate-slide-up">
                <div class="flex items-center gap-3 mb-6">
                    <div class="section-icon-sm bg-gold-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">What's Your Goal?</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach($this->goals as $goal)
                        <button
                            wire:click="selectGoal('{{ $goal->slug }}')"
                            wire:loading.attr="disabled"
                            wire:target="selectGoal"
                            class="group relative p-4 rounded-xl text-left border-2 border-cream-200 hover:border-gold-500/50 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:ring-offset-2 bg-white"
                        >
                            <div class="flex items-start gap-3">
                                @if($goal->icon)
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-sm transition-transform duration-200 group-hover:scale-110"
                                        style="background-color: {{ $goal->color ?? '#9A7B4F' }}">
                                        {!! $goal->icon !!}
                                    </div>
                                @elseif($goal->image)
                                    <img src="{{ Storage::url($goal->image) }}" alt="" class="w-10 h-10 rounded-xl object-cover shadow-sm">
                                @else
                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110"
                                        style="background-color: {{ $goal->color ?? '#9A7B4F' }}15">
                                        <svg class="w-5 h-5" style="color: {{ $goal->color ?? '#9A7B4F' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-gray-900 group-hover:text-gold-600 transition-colors text-sm leading-tight">{{ $goal->name }}</h3>
                                    @if($goal->description)
                                        <p class="text-xs text-gray-400 mt-1 line-clamp-2 leading-relaxed">{{ $goal->description }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Hover arrow --}}
                            <div class="absolute top-4 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <svg class="w-4 h-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </div>

                            {{-- Loading spinner --}}
                            <div wire:loading wire:target="selectGoal('{{ $goal->slug }}')" class="absolute inset-0 bg-white/90 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="animate-spin h-5 w-5 text-gold-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            </div>
                        </button>
                    @endforeach
                </div>

                <div class="mt-8 text-center">
                    <button wire:click="skipToProducts" class="inline-flex items-center gap-1.5 text-gold-500 hover:text-gold-600 font-semibold text-sm transition-colors group">
                        {{ $this->settings['browse_all_title'] ?? 'I Already Know What I Need' }}
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </div>
            </div>
        @elseif($currentStep > 1 && $selectedGoalSlug && $this->selectedGoal)
            {{-- Completed State --}}
            <div class="card-cream border-l-4 border-gold-500 cursor-pointer hover:shadow-sm transition-all duration-200" wire:click="goToStep(1)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-gold-500 text-white flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm text-gray-500">Step 1</span>
                        <span class="font-semibold text-gray-900">{{ $this->selectedGoal->name }}</span>
                        @if($this->selectedGoal->color)
                            <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $this->selectedGoal->color }}"></div>
                        @endif
                    </div>
                    <span class="text-xs text-gold-500 font-semibold hover:underline">Change</span>
                </div>
            </div>
        @elseif($currentStep > 1 && $skippedGoal)
            {{-- Skipped State --}}
            <div class="card-cream border-l-4 border-gold-500 cursor-pointer hover:shadow-sm transition-all duration-200" wire:click="goToStep(1)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-gold-500 text-white flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm text-gray-500">Step 1</span>
                        <span class="font-semibold text-gray-700">Browsing All Products</span>
                    </div>
                    <span class="text-xs text-gold-500 font-semibold hover:underline">Change</span>
                </div>
            </div>
        @endif
    </section>

    {{-- ===== STEP 2: PROFESSOR'S RECOMMENDED STACKS ===== --}}
    <section class="mb-5" wire:key="step-2">
        @if($currentStep === 2 && $selectedGoalSlug && $this->selectedGoal)
            {{-- Active State --}}
            <div class="card glow-gold animate-slide-up">
                <div class="flex items-center gap-3 mb-6">
                    <div class="section-icon-sm bg-gold-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $this->settings['professor_picks_title'] ?? "Professor's Recommended Stacks" }}
                        </h2>
                        <p class="text-sm text-gray-400 mt-0.5">
                            Curated for <span class="font-semibold" style="color: {{ $this->selectedGoal->color ?? '#9A7B4F' }}">{{ $this->selectedGoal->name }}</span> &middot; Compare prices across stores
                        </p>
                    </div>
                </div>

                @if($this->goalBundles->count() > 0)
                    <div class="space-y-5 mb-6">
                        @foreach($this->goalBundles as $bundle)
                            <div class="relative rounded-2xl border overflow-hidden transition-all duration-300 {{ $bundle->is_professor_pick ? 'border-gold-500/30 bg-gradient-to-br from-white to-cream-50 hover:shadow-lg hover:shadow-gold-500/10' : 'border-cream-200 bg-white hover:shadow-md' }}"
                                 wire:key="bundle-{{ $bundle->id }}">

                                <div class="p-6">
                                    @if($bundle->is_professor_pick)
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-gold-500 text-white text-xs font-bold rounded-full shadow-sm mb-3">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                            Professor's Pick
                                        </div>
                                    @endif

                                    <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $bundle->name }}</h3>
                                    @if($bundle->description)
                                        <p class="text-sm text-gray-500 mb-4 leading-relaxed">{{ $bundle->description }}</p>
                                    @endif

                                    {{-- Bundle items summary --}}
                                    <div class="flex flex-wrap gap-2 mb-5">
                                        @foreach($bundle->items as $item)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-cream-200 rounded-full text-sm text-gray-700 shadow-sm">
                                                <svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                {{ $item->product->name }}
                                                @if($item->quantity > 1) <span class="text-gray-400 font-medium">x{{ $item->quantity }}</span> @endif
                                            </span>
                                        @endforeach
                                    </div>

                                    @php
                                        $hasBundleStorePricing = $bundle->stores->where('pivot.is_in_stock', true)->count() > 0;
                                    @endphp

                                    @if($hasBundleStorePricing)
                                        {{-- Bundle-level store pricing (inline) --}}
                                        <div class="mb-1">
                                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Buy This Bundle</h4>
                                            @include('livewire.partials.bundle-store-comparison', ['bundle' => $bundle])
                                        </div>

                                        {{-- Expand/collapse for per-product breakdown --}}
                                        <button
                                            @click="expandedBundle = expandedBundle === {{ $bundle->id }} ? null : {{ $bundle->id }}; if (expandedBundle === {{ $bundle->id }}) $wire.dispatch('stack-bundle-viewed', { bundleName: '{{ addslashes($bundle->name) }}' })"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200"
                                            :class="expandedBundle === {{ $bundle->id }} ? 'bg-cream-200 text-gray-600' : 'bg-cream-100 text-gray-500 hover:bg-cream-200'"
                                        >
                                            <span x-text="expandedBundle === {{ $bundle->id }} ? 'Hide Per-Product Prices' : 'View Per-Product Prices'">View Per-Product Prices</span>
                                            <svg class="w-4 h-4 transition-transform duration-200" :class="expandedBundle === {{ $bundle->id }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    @else
                                        {{-- No bundle store pricing — show per-product expand --}}
                                        <button
                                            @click="expandedBundle = expandedBundle === {{ $bundle->id }} ? null : {{ $bundle->id }}; if (expandedBundle === {{ $bundle->id }}) $wire.dispatch('stack-bundle-viewed', { bundleName: '{{ addslashes($bundle->name) }}' })"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200"
                                            :class="expandedBundle === {{ $bundle->id }} ? 'bg-gold-500 text-white shadow-md' : 'bg-cream-100 text-gold-600 hover:bg-cream-200'"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                                            <span x-text="expandedBundle === {{ $bundle->id }} ? 'Hide Store Prices' : 'Compare Store Prices'">Compare Store Prices</span>
                                            <svg class="w-4 h-4 transition-transform duration-200" :class="expandedBundle === {{ $bundle->id }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    @endif
                                </div>

                                {{-- Expandable store prices per product --}}
                                <div
                                    x-show="expandedBundle === {{ $bundle->id }}"
                                    x-collapse
                                    x-cloak
                                    class="border-t border-cream-200"
                                >
                                    <div class="p-6 space-y-6 bg-cream-50/50">
                                        @foreach($bundle->items as $item)
                                            <div>
                                                <div class="flex items-center gap-2.5 mb-3">
                                                    @if($item->product->image)
                                                        <img src="{{ Storage::url($item->product->image) }}" alt="" class="w-8 h-8 rounded-lg object-cover shadow-sm">
                                                    @else
                                                        <div class="w-8 h-8 rounded-lg bg-cream-100 flex items-center justify-center flex-shrink-0">
                                                            <svg class="w-4 h-4 text-cream-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5"/></svg>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 text-sm">{{ $item->product->name }}</h4>
                                                        @if($item->quantity > 1)
                                                            <span class="text-xs text-gray-400">Qty: {{ $item->quantity }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @include('livewire.partials.store-comparison', ['product' => $item->product])
                                            </div>
                                            @if(!$loop->last)
                                                <div class="border-t border-cream-200"></div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-cream-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                        <p class="text-sm">No curated stacks for this goal yet. Browse individual products below.</p>
                    </div>
                @endif

                <div class="text-center pt-2">
                    <button wire:click="skipBundles" class="inline-flex items-center gap-1.5 text-gold-500 hover:text-gold-600 font-semibold text-sm transition-colors group">
                        Or browse individual products
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </div>
            </div>
        @elseif($currentStep > 2 && $selectedGoalSlug)
            {{-- Completed State --}}
            <div class="card-cream border-l-4 border-gold-500 cursor-pointer hover:shadow-sm transition-all duration-200" wire:click="goToStep(2)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full bg-gold-500 text-white flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm text-gray-500">Step 2</span>
                        @if($skippedBundles)
                            <span class="font-semibold text-gray-700">Skipped &mdash; Browsing products</span>
                        @else
                            <span class="font-semibold text-gray-900">Reviewed recommended stacks</span>
                        @endif
                    </div>
                    <span class="text-xs text-gold-500 font-semibold hover:underline">View Again</span>
                </div>
            </div>
        @elseif($currentStep < 2)
            {{-- Upcoming State --}}
            <div class="card opacity-40 pointer-events-none">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-cream-100 border-2 border-cream-200 text-cream-400 flex items-center justify-center text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-400">Professor's Picks</span>
                        <span class="text-sm text-cream-400 ml-2">&mdash; Select a goal first</span>
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- ===== STEP 3: BROWSE PRODUCTS ===== --}}
    <section class="mb-6" wire:key="step-3">
        @if($currentStep === 3)
            {{-- Active State --}}
            <div class="card glow-gold animate-slide-up">
                <div class="flex items-center gap-3 mb-2">
                    <div class="section-icon-sm bg-gold-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Browse Products</h2>
                </div>

                <p class="text-sm text-gray-400 mb-6 ml-11">Compare prices across stores and click <strong class="text-gray-600">Visit</strong> to buy directly.</p>

                {{-- Sub-tabs: Goal Products vs All Products --}}
                @if($selectedGoalSlug && $this->selectedGoal)
                    <div class="flex gap-1 p-1 bg-cream-100 rounded-xl mb-6 max-w-md">
                        <button
                            @click="productTab = 'goal'"
                            wire:click="$set('productTab', 'goal')"
                            :class="productTab === 'goal' ? 'bg-white shadow-sm text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm transition-all duration-200"
                        >
                            For {{ $this->selectedGoal->name }}
                        </button>
                        <button
                            @click="productTab = 'all'"
                            wire:click="$set('productTab', 'all')"
                            :class="productTab === 'all' ? 'bg-white shadow-sm text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-4 py-2.5 rounded-lg text-sm transition-all duration-200"
                        >
                            All Products
                        </button>
                    </div>
                @endif

                {{-- Product Grid --}}
                <div x-show="productTab === 'goal'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    @if($selectedGoalSlug && $this->goalProducts->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach($this->goalProducts as $product)
                                @include('livewire.partials.stack-product-card', ['product' => $product])
                            @endforeach
                        </div>
                    @elseif($selectedGoalSlug)
                        <div class="text-center py-10 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-cream-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                            <p class="text-sm">No specific products for this goal. Check the All Products tab.</p>
                        </div>
                    @endif
                </div>

                <div x-show="productTab === 'all'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($this->allProducts as $product)
                            @include('livewire.partials.stack-product-card', ['product' => $product])
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            {{-- Upcoming State --}}
            <div class="card opacity-40 pointer-events-none">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-cream-100 border-2 border-cream-200 text-cream-400 flex items-center justify-center text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-400">Browse Products</span>
                        <span class="text-sm text-cream-400 ml-2">&mdash; Complete previous steps first</span>
                    </div>
                </div>
            </div>
        @endif
    </section>

    @script
    <script>
        $wire.on('stack-started', () => {
            if (window.PPTracker) window.PPTracker.trackStackStart();
        });
        $wire.on('stack-goal-selected', ({ goalSlug, goalName }) => {
            if (window.PPTracker) window.PPTracker.trackStackGoalSelected(goalSlug, goalName);
        });
        $wire.on('stack-bundle-viewed', ({ bundleName }) => {
            if (window.PPTracker) window.PPTracker.trackStackBundleViewed(bundleName);
        });
        $wire.on('stack-completed', (data) => {
            if (window.PPTracker) {
                const payload = Array.isArray(data) ? data[0] : data;
                window.PPTracker.trackStackComplete(payload?.goalSlug, payload?.goalName);
            }
        });
    </script>
    @endscript
</div>
