{{-- Question (Choice) Slide --}}
@php
    $isMultiple = ($this->currentSlide['question_type'] ?? '') === 'multiple_choice';
    $options = $this->currentSlide['options'] ?? [];
    $maxSelections = $this->currentSlide['max_selections'] ?? null;
    $showSearch = count($options) >= 8;
@endphp
<div class="card p-8" wire:key="slide-question-{{ $currentStep }}"
    @if($showSearch)
        x-data="{ search: '' }"
        x-init="search = ''"
    @endif
>
    <h2 class="text-2xl font-semibold mb-6">{{ $this->currentSlide['question_text'] }}</h2>

    @if(!empty($this->currentSlide['question_subtext']))
        <p class="text-gray-600 mb-6">{{ $this->currentSlide['question_subtext'] }}</p>
    @endif

    @if($isMultiple)
        <p class="text-sm text-gray-500 mb-4">
            @if($maxSelections)
                Select up to {{ $maxSelections }}
            @else
                Select all that apply
            @endif
        </p>
    @endif

    @if($showSearch)
        <div class="relative mb-4">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                x-model="search"
                placeholder="Search options..."
                class="w-full pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                style="padding-left: 2.5rem"
            />
        </div>
    @endif

    <!-- Options -->
    <div class="space-y-3">
        @foreach($options as $option)
            @php
                $optionKey = $option['value'] ?? $option['id'] ?? '';
                $optionLabel = $option['text'] ?? $option['label'] ?? '';
            @endphp

            @if($isMultiple)
                {{-- Multiple choice: toggle selection without advancing --}}
                @php $atLimit = $maxSelections && count($multiSelections) >= $maxSelections && !in_array($optionKey, $multiSelections); @endphp
                <button
                    wire:click="toggleMultipleAnswer({{ $currentStep }}, '{{ addslashes($optionKey) }}')"
                    @if($atLimit) disabled @endif
                    @if($showSearch) x-show="!search || '{{ strtolower(addslashes($optionLabel)) }}'.includes(search.toLowerCase())" @endif
                    class="w-full text-left p-4 rounded-lg border-2 transition-all flex items-center gap-3
                        {{ in_array($optionKey, $multiSelections)
                            ? 'border-primary bg-primary/10'
                            : ($atLimit ? 'border-gray-100 bg-gray-50 opacity-50 cursor-not-allowed' : 'border-gray-200 hover:border-primary/50 hover:bg-gray-50') }}"
                >
                    <span class="flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center
                        {{ in_array($optionKey, $multiSelections)
                            ? 'border-primary bg-primary'
                            : 'border-gray-300' }}">
                        @if(in_array($optionKey, $multiSelections))
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </span>
                    <span>
                        <span class="font-medium">{{ $optionLabel }}</span>
                        @if(!empty($option['subtext']))
                            <span class="block text-sm text-gray-500 mt-1">{{ $option['subtext'] }}</span>
                        @endif
                    </span>
                </button>
            @else
                {{-- Single choice: select and advance immediately --}}
                <button
                    wire:click="selectAnswer({{ $currentStep }}, '{{ addslashes($optionKey) }}')"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 pointer-events-none"
                    @if($showSearch) x-show="!search || '{{ strtolower(addslashes($optionLabel)) }}'.includes(search.toLowerCase())" @endif
                    class="w-full text-left p-4 rounded-lg border-2 transition-all
                        {{ isset($answers[$currentStep]) && ($answers[$currentStep]['option_id'] ?? '') === $optionKey
                            ? 'border-primary bg-primary/10'
                            : 'border-gray-200 hover:border-primary/50 hover:bg-gray-50' }}"
                >
                    <span class="font-medium">{{ $optionLabel }}</span>
                    @if(!empty($option['subtext']))
                        <span class="block text-sm text-gray-500 mt-1">{{ $option['subtext'] }}</span>
                    @endif
                </button>
            @endif
        @endforeach
    </div>

    <!-- Continue button for multiple choice -->
    @if($isMultiple)
        <div class="flex items-center justify-between mt-6">
            @if($maxSelections)
                <span class="text-xs text-gray-400">{{ count($multiSelections) }}/{{ $maxSelections }} selected</span>
            @else
                <span></span>
            @endif
            <button
                wire:click="submitMultipleAnswer"
                wire:loading.attr="disabled"
                @if(empty($multiSelections)) disabled @endif
                class="btn {{ empty($multiSelections) ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-primary text-white hover:bg-primary/90' }} disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="submitMultipleAnswer">Continue</span>
                <span wire:loading wire:target="submitMultipleAnswer">Saving...</span>
            </button>
        </div>
    @endif

    @include('livewire.partials.slide-accordion')

    {{-- Optional CTA --}}
    @if(!empty($this->currentSlide['cta_text']))
        <div class="text-center mt-6">
            @if(!empty($this->currentSlide['cta_url']))
                <a href="{{ $this->currentSlide['cta_url'] }}" target="_blank" rel="noopener noreferrer" class="text-sm text-primary hover:underline">
                    {{ $this->currentSlide['cta_text'] }}
                </a>
            @endif
        </div>
    @endif

    <!-- Back Button -->
    @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
        <div class="flex justify-start mt-8">
            <button wire:click="previousStep" wire:loading.attr="disabled" wire:loading.class="opacity-50 pointer-events-none" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                <svg aria-hidden="true" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
        </div>
    @endif
</div>
