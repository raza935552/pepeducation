<x-public-layout title="FAQ">
    {{-- Hero --}}
    <section class="bg-gradient-to-b from-cream-100 to-cream-50 py-16 lg:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                Frequently Asked Questions
            </h1>
            <p class="text-lg text-gray-600">
                Find answers to common questions about PepProfesor.
            </p>
        </div>
    </section>

    {{-- FAQ Content --}}
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-4" x-data="{ open: 1 }">
                {{-- FAQ 1 --}}
                <div class="bg-white rounded-2xl border border-cream-200 overflow-hidden">
                    <button @click="open = open === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                        <span class="font-semibold text-gray-900">What is PepProfesor?</span>
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === 1" x-collapse>
                        <div class="px-6 pb-5 text-gray-600">
                            PepProfesor is a free educational resource providing comprehensive information about peptides for research purposes. We aggregate data from scientific studies, clinical research, and community contributions to help researchers and curious minds learn about peptides.
                        </div>
                    </div>
                </div>

                {{-- FAQ 2 --}}
                <div class="bg-white rounded-2xl border border-cream-200 overflow-hidden">
                    <button @click="open = open === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                        <span class="font-semibold text-gray-900">Do you sell peptides?</span>
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === 2" x-collapse>
                        <div class="px-6 pb-5 text-gray-600">
                            <strong>No.</strong> PepProfesor does not sell, supply, or distribute any peptides. We are purely an educational resource. We do not recommend or endorse any vendors or products.
                        </div>
                    </div>
                </div>

                {{-- FAQ 3 --}}
                <div class="bg-white rounded-2xl border border-cream-200 overflow-hidden">
                    <button @click="open = open === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                        <span class="font-semibold text-gray-900">Is the information here medical advice?</span>
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === 3" x-collapse>
                        <div class="px-6 pb-5 text-gray-600">
                            <strong>No.</strong> All information on PepProfesor is for educational and research purposes only. It is not intended as medical advice and should not be used for self-diagnosis or self-treatment. Always consult qualified healthcare professionals for medical decisions.
                        </div>
                    </div>
                </div>

                {{-- FAQ 4 --}}
                <div class="bg-white rounded-2xl border border-cream-200 overflow-hidden">
                    <button @click="open = open === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                        <span class="font-semibold text-gray-900">How do I use the dosing calculator?</span>
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === 4" x-collapse>
                        <div class="px-6 pb-5 text-gray-600">
                            Our <a href="{{ route('calculator') }}" class="text-gold-600 hover:underline">dosing calculator</a> helps you calculate reconstitution volumes and dosing. Enter the peptide amount (mg), bacteriostatic water volume (mL), and desired dose to get the injection volume in mL and insulin units. The calculator is for research reference only.
                        </div>
                    </div>
                </div>

                {{-- FAQ 5 --}}
                <div class="bg-white rounded-2xl border border-cream-200 overflow-hidden">
                    <button @click="open = open === 5 ? null : 5" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                        <span class="font-semibold text-gray-900">How can I contribute to PepProfesor?</span>
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 5 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === 5" x-collapse>
                        <div class="px-6 pb-5 text-gray-600">
                            We welcome community contributions! You can:
                            <ul class="mt-2 space-y-1 list-disc list-inside">
                                <li>Suggest edits to existing peptide information</li>
                                <li>Request new peptides to be added</li>
                                <li>Report errors or outdated information</li>
                            </ul>
                            Create a free account to get started. All contributions are reviewed by our team before publication.
                        </div>
                    </div>
                </div>

                {{-- FAQ 6 --}}
                <div class="bg-white rounded-2xl border border-cream-200 overflow-hidden">
                    <button @click="open = open === 6 ? null : 6" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                        <span class="font-semibold text-gray-900">Is PepProfesor free to use?</span>
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 6 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === 6" x-collapse>
                        <div class="px-6 pb-5 text-gray-600">
                            Yes! PepProfesor is completely free to use. All peptide information, the calculator, and other features are available without any subscription or paywall. We believe research information should be accessible to everyone.
                        </div>
                    </div>
                </div>

                {{-- FAQ 7 --}}
                <div class="bg-white rounded-2xl border border-cream-200 overflow-hidden">
                    <button @click="open = open === 7 ? null : 7" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                        <span class="font-semibold text-gray-900">Where does your information come from?</span>
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 7 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === 7" x-collapse>
                        <div class="px-6 pb-5 text-gray-600">
                            Our information is aggregated from:
                            <ul class="mt-2 space-y-1 list-disc list-inside">
                                <li>Peer-reviewed scientific studies</li>
                                <li>Clinical trial data</li>
                                <li>Published research papers</li>
                                <li>Community contributions (reviewed before publication)</li>
                            </ul>
                            We strive to provide accurate, up-to-date information, but always recommend verifying with primary sources.
                        </div>
                    </div>
                </div>

                {{-- FAQ 8 --}}
                <div class="bg-white rounded-2xl border border-cream-200 overflow-hidden">
                    <button @click="open = open === 8 ? null : 8" class="w-full px-6 py-5 text-left flex items-center justify-between gap-4">
                        <span class="font-semibold text-gray-900">How can I contact you?</span>
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 8 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === 8" x-collapse>
                        <div class="px-6 pb-5 text-gray-600">
                            You can reach us through our <button onclick="Livewire.dispatch('openContactModal')" class="text-gold-600 hover:underline">contact form</button>. We typically respond within 1-2 business days.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Still have questions? --}}
            <div class="mt-12 text-center">
                <p class="text-gray-600 mb-4">Still have questions?</p>
                <button onclick="Livewire.dispatch('openContactModal')" class="inline-flex items-center gap-2 px-6 py-3 bg-gold-500 hover:bg-gold-600 text-white font-medium rounded-full transition-colors">
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Contact Us
                </button>
            </div>
        </div>
    </section>
</x-public-layout>
