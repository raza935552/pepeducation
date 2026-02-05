<div>
    @if($show)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="close"></div>

                {{-- Modal --}}
                <div class="relative w-full max-w-2xl bg-white dark:bg-brown-800 rounded-2xl shadow-2xl">
                    {{-- Header --}}
                    <div class="flex items-center justify-between p-6 border-b border-cream-200 dark:border-brown-700">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-cream-100">Suggest an Edit</h3>
                            @if($peptide)
                                <p class="text-sm text-gray-500 dark:text-cream-400 mt-1">{{ $peptide->name }}</p>
                            @endif
                        </div>
                        <button wire:click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-cream-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    @if($submitted)
                        {{-- Success State --}}
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-cream-100 mb-2">Thank You!</h4>
                            <p class="text-gray-600 dark:text-cream-400 mb-6">Your edit suggestion has been submitted for review. We'll notify you once it's been reviewed.</p>
                            <button wire:click="close" class="px-6 py-2.5 bg-gold-500 text-white rounded-full font-medium hover:bg-gold-600">
                                Close
                            </button>
                        </div>
                    @else
                        {{-- Form --}}
                        <form wire:submit="submit" class="p-6 space-y-5">
                            @guest
                                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                                    <p class="text-sm text-amber-800 dark:text-amber-300">
                                        Please <a href="{{ route('login') }}" class="font-medium underline">sign in</a> to suggest edits.
                                    </p>
                                </div>
                            @endguest

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-1">Section *</label>
                                <select wire:model.live="section" class="w-full rounded-lg border-cream-200 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 focus:ring-gold-500 focus:border-gold-500">
                                    <option value="">Select a section...</option>
                                    @foreach($this->getSectionOptions() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('section') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            @if($section && $originalContent)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-1">Current Content</label>
                                    <div class="p-3 bg-cream-100 dark:bg-brown-700 rounded-lg text-sm text-gray-600 dark:text-cream-400 max-h-32 overflow-y-auto">
                                        {{ $originalContent }}
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-1">Your Suggested Edit *</label>
                                <textarea wire:model="newContent" rows="6" class="w-full rounded-lg border-cream-200 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 focus:ring-gold-500 focus:border-gold-500" placeholder="Enter your suggested content..."></textarea>
                                @error('newContent') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-1">Reason for Edit (optional)</label>
                                <textarea wire:model="editReason" rows="2" class="w-full rounded-lg border-cream-200 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 focus:ring-gold-500 focus:border-gold-500" placeholder="Why should this change be made?"></textarea>
                            </div>

                            <div class="bg-cream-100 dark:bg-brown-700 rounded-lg p-4">
                                <p class="text-sm text-gray-600 dark:text-cream-400">
                                    <span class="font-medium">Review process:</span> Your suggestion will be reviewed by our team. Approved edits help keep our information accurate and up-to-date.
                                </p>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" wire:click="close" class="px-4 py-2.5 text-gray-700 dark:text-cream-300 hover:bg-cream-100 dark:hover:bg-brown-700 rounded-lg">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-2.5 bg-gold-500 text-white rounded-full font-medium hover:bg-gold-600 disabled:opacity-50" wire:loading.attr="disabled" @guest disabled @endguest>
                                    <span wire:loading.remove>Submit Suggestion</span>
                                    <span wire:loading>Submitting...</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
