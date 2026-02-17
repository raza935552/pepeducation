{{-- Loading Screen Slide — animated checklist + auto-advance --}}
@php
    $autoSeconds = $this->currentSlide['auto_advance_seconds'] ?? 5;
    $items = array_filter(array_map('trim', explode("\n", $this->currentSlide['content_body'] ?? '')));
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
                }, 500);
            }, this.totalSeconds * 1000);
        }
     }">

    @if(!empty($this->currentSlide['content_title']))
        <h2 class="text-2xl font-bold mb-6">{{ $this->currentSlide['content_title'] }}</h2>
    @else
        <h2 class="text-2xl font-bold mb-6">Analyzing your answers...</h2>
    @endif

    <!-- Animated checklist -->
    @if($itemCount > 0)
        <div class="space-y-3 max-w-sm mx-auto text-left mb-8">
            @foreach($items as $index => $item)
                <div class="flex items-center gap-3 transition-all duration-300"
                     :class="currentItem > {{ $index }} ? 'opacity-100' : 'opacity-40'">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 transition-colors duration-300"
                         :class="currentItem > {{ $index }} ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">
                        <svg x-show="currentItem > {{ $index }}" aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-transition>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg x-show="currentItem <= {{ $index }}" aria-hidden="true" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             x-cloak :class="currentItem === {{ $index }} ? '' : 'hidden'">
                            <circle cx="12" cy="12" r="10" stroke-width="2" stroke-dasharray="31" stroke-dashoffset="10"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium" :class="currentItem > {{ $index }} ? 'text-gray-800' : 'text-gray-400'">{{ $item }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Progress bar -->
    <div class="w-full max-w-sm mx-auto mb-4">
        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-brand-gold rounded-full transition-all duration-500 ease-out"
                 :style="'width: ' + progressPercent + '%'"></div>
        </div>
    </div>

    <p class="text-sm text-gray-500" x-show="!done">Please wait...</p>
    <p class="text-sm text-green-600 font-medium" x-show="done" x-cloak>Done!</p>
</div>
