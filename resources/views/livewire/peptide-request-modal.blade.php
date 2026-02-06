<div>
    @if($show)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="$el.querySelector('input')?.focus()">
            <div class="flex min-h-screen items-center justify-center p-4">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="close"></div>

                {{-- Modal --}}
                <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="request-modal-title" x-trap.inert.noscroll="true">
                    {{-- Header --}}
                    <div class="flex items-center justify-between p-6 border-b border-cream-200">
                        <h3 id="request-modal-title" class="text-xl font-semibold text-gray-900">Request a Peptide</h3>
                        <button wire:click="close" aria-label="Close" class="text-gray-400 hover:text-gray-600">
                            <svg aria-hidden="true" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    @if($submitted)
                        {{-- Success State --}}
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg aria-hidden="true" class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Request Submitted!</h4>
                            <p class="text-gray-600 mb-6">New pages typically publish within 1-4 days.</p>
                            <button wire:click="close" class="px-6 py-2.5 bg-gold-500 text-white rounded-full font-medium hover:bg-gold-600">
                                Close
                            </button>
                        </div>
                    @else
                        {{-- Form --}}
                        <form wire:submit="submit" class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Peptide Name *</label>
                                <input type="text" wire:model="peptideName" placeholder="e.g., Epithalon, MOTS-c, etc." class="w-full rounded-lg border-cream-200 focus:ring-gold-500 focus:border-gold-500">
                                @error('peptideName') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Links to Sources *</label>
                                <textarea wire:model="sourceLinks" rows="4" class="w-full rounded-lg border-cream-200 focus:ring-gold-500 focus:border-gold-500" placeholder="Please provide links to research papers, clinical trials, or reputable sources (one per line)"></textarea>
                                <p class="mt-1 text-sm text-gray-500">Include multiple links separated by new lines</p>
                                @error('sourceLinks') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (optional)</label>
                                <textarea wire:model="notes" rows="2" class="w-full rounded-lg border-cream-200 focus:ring-gold-500 focus:border-gold-500" placeholder="Any additional information..."></textarea>
                            </div>

                            <div class="bg-cream-100 rounded-lg p-4">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Our process:</span> We combine your submission with data from established research databases, using AI to synthesize comprehensive guides. Community feedback helps maintain accuracy. New pages typically publish within 1-4 days.
                                </p>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" wire:click="close" class="px-4 py-2.5 text-gray-700 hover:bg-cream-100 rounded-lg">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-2.5 bg-gold-500 text-white rounded-full font-medium hover:bg-gold-600" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Submit Request</span>
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
