{{-- Question (Choice) Slide --}}
@php
    $isMultiple = ($this->currentSlide['question_type'] ?? '') === 'multiple_choice';
@endphp
<div class="card p-8" wire:key="slide-question-{{ $currentStep }}">
    <h2 class="text-2xl font-semibold mb-6">{{ $this->currentSlide['question_text'] }}</h2>

    @if(!empty($this->currentSlide['question_subtext']))
        <p class="text-gray-600 mb-6">{{ $this->currentSlide['question_subtext'] }}</p>
    @endif

    @if($isMultiple)
        <p class="text-sm text-gray-500 mb-4">Select all that apply</p>
    @endif

    <!-- Options -->
    <div class="space-y-3">
        @foreach($this->currentSlide['options'] ?? [] as $option)
            @php
                $optionKey = $option['value'] ?? $option['id'] ?? '';
                $optionLabel = $option['text'] ?? $option['label'] ?? '';
            @endphp

            @if($isMultiple)
                {{-- Multiple choice: toggle selection without advancing --}}
                <button
                    wire:click="toggleMultipleAnswer({{ $currentStep }}, '{{ addslashes($optionKey) }}')"
                    class="w-full text-left p-4 rounded-lg border-2 transition-all flex items-center gap-3
                        {{ in_array($optionKey, $multiSelections)
                            ? 'border-brand-gold bg-brand-gold/10'
                            : 'border-gray-200 hover:border-brand-gold/50 hover:bg-gray-50' }}"
                >
                    <span class="flex-shrink-0 w-5 h-5 rounded border-2 flex items-center justify-center
                        {{ in_array($optionKey, $multiSelections)
                            ? 'border-brand-gold bg-brand-gold'
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
                    class="w-full text-left p-4 rounded-lg border-2 transition-all
                        {{ isset($answers[$currentStep]) && ($answers[$currentStep]['option_id'] ?? '') === $optionKey
                            ? 'border-brand-gold bg-brand-gold/10'
                            : 'border-gray-200 hover:border-brand-gold/50 hover:bg-gray-50' }}"
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
        <div class="flex justify-end mt-6">
            <button
                wire:click="submitMultipleAnswer"
                @if(empty($multiSelections)) disabled @endif
                class="btn {{ empty($multiSelections) ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-brand-gold text-white hover:bg-brand-gold/90' }}"
            >
                Continue
            </button>
        </div>
    @endif

    <!-- Back Button -->
    @if((($quiz->settings ?? [])['allow_back'] ?? true) && $currentStep > 0)
        <div class="flex justify-start mt-8">
            <button wire:click="previousStep" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
                <svg aria-hidden="true" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </button>
        </div>
    @endif
</div>
