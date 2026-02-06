<x-public-layout>
    <x-slot name="title">Peptide Calculator</x-slot>

    <!-- Hero Section -->
    <section class="bg-cream-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Peptide <span class="text-gold-500">Calculator</span>
                </h1>
                <p class="text-lg text-gray-600">
                    Calculate reconstitution volumes and dosing for your peptide research
                </p>
            </div>
        </div>
    </section>

    <!-- Calculator Section -->
    <section class="py-12 bg-cream-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:peptide-calculator />
        </div>
    </section>

    <!-- Info Section -->
    <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                How to Use This Calculator
            </h2>

            <div class="space-y-6">
                <div class="bg-cream-50 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-gold-500 text-white text-sm flex items-center justify-center">1</span>
                        Enter Peptide Amount
                    </h3>
                    <p class="text-gray-600 ml-8">
                        Enter the total amount of lyophilized peptide in your vial (in milligrams). This is typically printed on the vial label (e.g., 5mg, 10mg).
                    </p>
                </div>

                <div class="bg-cream-50 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-gold-500 text-white text-sm flex items-center justify-center">2</span>
                        Set Water Volume
                    </h3>
                    <p class="text-gray-600 ml-8">
                        Enter how much bacteriostatic water you will add to reconstitute the peptide. Common amounts are 1mL, 2mL, or 3mL. More water = easier to measure small doses.
                    </p>
                </div>

                <div class="bg-cream-50 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-gold-500 text-white text-sm flex items-center justify-center">3</span>
                        Enter Desired Dose
                    </h3>
                    <p class="text-gray-600 ml-8">
                        Enter your desired dose in micrograms (mcg). You can use a fixed dose or calculate based on body weight for peptides dosed by weight.
                    </p>
                </div>

                <div class="bg-cream-50 rounded-xl p-6">
                    <h3 class="font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-gold-500 text-white text-sm flex items-center justify-center">4</span>
                        Read the Result
                    </h3>
                    <p class="text-gray-600 ml-8">
                        The calculator will show you exactly how many units to draw on your insulin syringe. The syringe visualization helps you see the fill level.
                    </p>
                </div>
            </div>

            <!-- Warning -->
            <div class="mt-8 bg-amber-50 border border-amber-200 rounded-xl p-6">
                <div class="flex gap-3">
                    <svg aria-hidden="true" class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-amber-800 mb-1">Research Purposes Only</h4>
                        <p class="text-amber-700 text-sm">
                            This calculator is for educational and research purposes only. Always consult with a qualified healthcare professional before using any peptides. Verify all calculations independently.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
