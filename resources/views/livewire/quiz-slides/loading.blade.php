{{-- Loading Screen Slide — animated checklist + auto-advance --}}
@php
    $resolved = $this->resolvedSlide;
    $autoSeconds = $this->currentSlide['auto_advance_seconds'] ?? 5;
    $items = array_filter(array_map('trim', explode("\n", $resolved['content_body'] ?? '')));
    $itemCount = count($items);
@endphp

<div class="card p-8 text-center" wire:key="slide-loading-{{ $currentStep }}"
     x-data="{
        currentItem: 0,
        progressPercent: 0,
        done: false,
        totalSeconds: {{ $autoSeconds }},
        itemCount: {{ $itemCount }},
        init() {
            const itemInterval = (this.totalSeconds * 1000) / Math.max(this.itemCount, 1);
            let elapsed = 0;
            const progressInterval = 50;

            const progressTimer = setInterval(() => {
                elapsed += progressInterval;
                this.progressPercent = Math.min((elapsed / (this.totalSeconds * 1000)) * 100, 100);
                if (elapsed >= this.totalSeconds * 1000) {
                    clearInterval(progressTimer);
                }
            }, progressInterval);

            if (this.itemCount > 0) {
                let itemIndex = 0;
                const itemTimer = setInterval(() => {
                    itemIndex++;
                    this.currentItem = itemIndex;
                    if (itemIndex >= this.itemCount) {
                        clearInterval(itemTimer);
                    }
                }, itemInterval);
            }

            setTimeout(() => {
                this.done = true;
                this.progressPercent = 100;
                this.currentItem = this.itemCount;
                setTimeout(() => {
                    $wire.advanceSlide();
                }, 600);
            }, this.totalSeconds * 1000);
        }
     }">

    @if(!empty($resolved['content_title']))
        <h2 class="text-2xl font-bold mb-6">{{ $resolved['content_title'] }}</h2>
    @else
        <h2 class="text-2xl font-bold mb-6">Analyzing your answers...</h2>
    @endif

    <!-- Animated checklist with staggered entrance -->
    @if($itemCount > 0)
        <div class="space-y-3 max-w-sm mx-auto text-left mb-8">
            @foreach($items as $index => $item)
                <div class="flex items-center gap-3 transition-all duration-500 ease-out"
                     :class="currentItem >= {{ $index }} ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-3'"
                     :style="currentItem >= {{ $index }} ? '' : 'pointer-events: none'">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 transition-all duration-300"
                         :class="currentItem > {{ $index }} ? 'bg-green-500 text-white scale-110' : currentItem === {{ $index }} ? 'bg-brand-gold/20 text-brand-gold' : 'bg-gray-200 text-gray-400'">
                        {{-- Completed checkmark --}}
                        <svg x-show="currentItem > {{ $index }}" aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="scale-0 opacity-0"
                             x-transition:enter-end="scale-100 opacity-100">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{-- Active pulsing dot --}}
                        <span x-show="currentItem === {{ $index }}" x-cloak
                              class="w-2 h-2 rounded-full bg-brand-gold animate-pulse"></span>
                    </div>
                    <span class="text-sm font-medium transition-colors duration-300"
                          :class="currentItem > {{ $index }} ? 'text-gray-800' : currentItem === {{ $index }} ? 'text-gray-700' : 'text-gray-400'">{{ $item }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Gradient progress bar with shimmer -->
    <div class="w-full max-w-sm mx-auto mb-4">
        <div class="h-2.5 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-300 ease-out relative"
                 :class="done ? 'bg-green-500' : ''"
                 :style="'width: ' + progressPercent + '%; ' + (done ? '' : 'background: linear-gradient(90deg, #D4A843, #F0D68A, #D4A843); background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite;')">
            </div>
        </div>
    </div>

    <p class="text-sm text-gray-500 transition-all duration-300" x-show="!done">Please wait...</p>
    <p class="text-sm text-green-600 font-semibold transition-all duration-300" x-show="done" x-cloak
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 scale-95"
       x-transition:enter-end="opacity-100 scale-100">
        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
        </svg>
        Complete!
    </p>
</div>

<style>
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
