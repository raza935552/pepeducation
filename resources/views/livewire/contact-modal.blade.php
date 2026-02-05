<div>
    @if($show)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="$el.querySelector('input')?.focus()">
            <div class="flex min-h-screen items-center justify-center p-4">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="close"></div>

                {{-- Modal --}}
                <div class="relative w-full max-w-lg bg-white dark:bg-brown-800 rounded-2xl shadow-2xl">
                    {{-- Header --}}
                    <div class="flex items-center justify-between p-6 border-b border-cream-200 dark:border-brown-700">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-cream-100">Contact Support</h3>
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
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-cream-100 mb-2">Message Sent!</h4>
                            <p class="text-gray-600 dark:text-cream-400 mb-6">We typically respond within 24-48 hours.</p>
                            <button wire:click="close" class="px-6 py-2.5 bg-gold-500 text-white rounded-full font-medium hover:bg-gold-600">
                                Close
                            </button>
                        </div>
                    @else
                        {{-- Form --}}
                        <form wire:submit="submit" class="p-6 space-y-5">
                            <p class="text-sm text-gray-600 dark:text-cream-400">Have a question, feedback, or need help? We'd love to hear from you.</p>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-1">Your Name *</label>
                                    <input type="text" wire:model="name" class="w-full rounded-lg border-cream-200 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 focus:ring-gold-500 focus:border-gold-500">
                                    @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-1">Email Address *</label>
                                    <input type="email" wire:model="email" class="w-full rounded-lg border-cream-200 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 focus:ring-gold-500 focus:border-gold-500">
                                    @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-1">Subject *</label>
                                <select wire:model="subject" class="w-full rounded-lg border-cream-200 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 focus:ring-gold-500 focus:border-gold-500">
                                    <option value="general">General Question</option>
                                    <option value="bug">Bug Report</option>
                                    <option value="feature">Feature Request</option>
                                    <option value="correction">Content Correction</option>
                                    <option value="partnership">Partnership Inquiry</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-1">Message *</label>
                                <textarea wire:model="message" rows="4" class="w-full rounded-lg border-cream-200 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 focus:ring-gold-500 focus:border-gold-500" placeholder="Tell us how we can help..."></textarea>
                                @error('message') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div class="bg-cream-100 dark:bg-brown-700 rounded-lg p-4">
                                <p class="text-sm text-gray-600 dark:text-cream-400">
                                    <span class="font-medium">Response time:</span> We typically respond within 24-48 hours. For urgent matters, please mention it in your message.
                                </p>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" wire:click="close" class="px-4 py-2.5 text-gray-700 dark:text-cream-300 hover:bg-cream-100 dark:hover:bg-brown-700 rounded-lg">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-2.5 bg-gold-500 text-white rounded-full font-medium hover:bg-gold-600" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Send Message</span>
                                    <span wire:loading>Sending...</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
