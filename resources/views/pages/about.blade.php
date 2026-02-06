<x-public-layout title="About Us">
    {{-- Hero --}}
    <section class="bg-gradient-to-b from-cream-100 to-cream-50 py-16 lg:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                About <span class="text-gold-500">PepProfesor</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Your trusted source for peptide research information, designed for researchers, clinicians, and curious minds.
            </p>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-16 lg:py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="prose prose-lg max-w-none">
                {{-- Mission --}}
                <div class="bg-white rounded-2xl p-8 border border-cream-200 mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gold-100 flex items-center justify-center">
                            <svg aria-hidden="true" class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 m-0">Our Mission</h2>
                    </div>
                    <p class="text-gray-600 m-0">
                        PepProfesor aims to make peptide research accessible and understandable. We aggregate research from clinical studies, scientific journals, and community contributions to provide comprehensive, unbiased information about peptides and their applications.
                    </p>
                </div>

                {{-- What We Offer --}}
                <div class="bg-white rounded-2xl p-8 border border-cream-200 mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <svg aria-hidden="true" class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 m-0">What We Offer</h2>
                    </div>
                    <ul class="space-y-3 text-gray-600 m-0 list-none p-0">
                        <li class="flex items-start gap-3">
                            <svg aria-hidden="true" class="w-5 h-5 text-gold-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span><strong>Comprehensive Database:</strong> Detailed profiles for 70+ peptides with research summaries, dosing information, and clinical applications.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg aria-hidden="true" class="w-5 h-5 text-gold-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span><strong>Dosing Calculator:</strong> Precise reconstitution and dosing calculations for safe research practices.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg aria-hidden="true" class="w-5 h-5 text-gold-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span><strong>Community Contributions:</strong> Researchers can suggest edits and improvements to keep information current.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg aria-hidden="true" class="w-5 h-5 text-gold-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span><strong>Free Access:</strong> All information is freely available without paywalls or subscriptions.</span>
                        </li>
                    </ul>
                </div>

                {{-- Disclaimer Note --}}
                <div class="bg-amber-50 rounded-2xl p-8 border border-amber-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                            <svg aria-hidden="true" class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 m-0">Important Note</h2>
                    </div>
                    <p class="text-gray-600 m-0">
                        PepProfesor is for educational and research purposes only. We do not sell peptides or provide medical advice. Always consult qualified healthcare professionals before making any decisions related to your health. See our <a href="{{ route('disclaimer') }}" class="text-gold-600 hover:underline">full disclaimer</a> for more information.
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
