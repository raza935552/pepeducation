<div class="grid lg:grid-cols-3 gap-8">
    <!-- Left Column: Inputs -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Reconstitution Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-cream-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg aria-hidden="true" class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                Reconstitution
            </h2>

            <div class="grid sm:grid-cols-2 gap-6">
                <!-- Peptide Amount -->
                <div>
                    <label for="peptideAmount" class="block text-sm font-medium text-gray-700 mb-2">
                        Peptide Amount (mg)
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            id="peptideAmount"
                            wire:model.live="peptideAmount"
                            min="0.1"
                            max="100"
                            step="0.1"
                            class="block w-full rounded-xl border-cream-300 focus:border-gold-500 focus:ring-gold-500 pr-12"
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">mg</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Amount of peptide in vial</p>
                </div>

                <!-- Bacteriostatic Water -->
                <div>
                    <label for="waterAmount" class="block text-sm font-medium text-gray-700 mb-2">
                        Bacteriostatic Water (mL)
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            id="waterAmount"
                            wire:model.live="waterAmount"
                            min="0.5"
                            max="10"
                            step="0.5"
                            class="block w-full rounded-xl border-cream-300 focus:border-gold-500 focus:ring-gold-500 pr-12"
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">mL</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Water to add for reconstitution</p>
                </div>
            </div>

            <!-- Concentration Result -->
            <div class="mt-6 p-4 bg-cream-50 rounded-xl">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Concentration:</span>
                    <span class="text-lg font-bold text-gold-600">
                        {{ number_format($this->concentration, 2) }} mcg/mL
                    </span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-sm text-gray-600">Per syringe unit:</span>
                    <span class="font-medium text-gray-900">
                        {{ number_format($this->mcgPerUnit, 2) }} mcg/unit
                    </span>
                </div>
            </div>
        </div>

        <!-- Dosing Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-cream-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg aria-hidden="true" class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Dosing
            </h2>

            <!-- Body Weight Toggle -->
            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model.live="useBodyWeight"
                        class="rounded border-cream-300 text-gold-500 focus:ring-gold-500"
                    >
                    <span class="text-sm text-gray-700">Calculate dose based on body weight</span>
                </label>
            </div>

            @if($useBodyWeight)
                <!-- Body Weight Dosing -->
                <div class="grid sm:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Body Weight</label>
                        <div class="flex gap-2">
                            <input
                                type="number"
                                wire:model.live="bodyWeight"
                                min="20"
                                max="300"
                                class="block w-full rounded-xl border-cream-300 focus:border-gold-500 focus:ring-gold-500"
                            >
                            <select
                                wire:model.live="weightUnit"
                                class="rounded-xl border-cream-300 focus:border-gold-500 focus:ring-gold-500"
                            >
                                <option value="kg">kg</option>
                                <option value="lb">lb</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dose per kg</label>
                        <div class="relative">
                            <input
                                type="number"
                                wire:model.live="dosePerKg"
                                min="0.1"
                                max="100"
                                step="0.1"
                                class="block w-full rounded-xl border-cream-300 focus:border-gold-500 focus:ring-gold-500 pr-16"
                            >
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">mcg/kg</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Calculated Dose</label>
                        <div class="h-[42px] flex items-center px-4 bg-cream-50 rounded-xl">
                            <span class="font-bold text-gold-600">{{ number_format($this->effectiveDose, 1) }} mcg</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Fixed Dose -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Desired Dose (mcg)</label>
                    <div class="relative max-w-xs">
                        <input
                            type="number"
                            wire:model.live="desiredDose"
                            min="1"
                            max="10000"
                            step="1"
                            class="block w-full rounded-xl border-cream-300 focus:border-gold-500 focus:ring-gold-500 pr-14"
                        >
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">mcg</span>
                    </div>
                </div>
            @endif

            <!-- Syringe Size -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Syringe Size</label>
                <div class="flex gap-3">
                    @foreach(['30' => '0.3mL (30u)', '50' => '0.5mL (50u)', '100' => '1.0mL (100u)'] as $value => $label)
                        <label class="flex-1">
                            <input type="radio" wire:model.live="syringeSize" value="{{ $value }}" class="sr-only peer">
                            <div class="text-center py-3 px-4 rounded-xl border-2 cursor-pointer transition-all
                                peer-checked:border-gold-500 peer-checked:bg-gold-50
                                border-cream-200 hover:border-gold-300">
                                <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Cycle Planning -->
        <div class="bg-white rounded-2xl shadow-sm border border-cream-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg aria-hidden="true" class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Cycle Planning
            </h2>

            <div class="grid sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Injections per Day</label>
                    <select
                        wire:model.live="injectionsPerDay"
                        class="block w-full rounded-xl border-cream-300 focus:border-gold-500 focus:ring-gold-500"
                    >
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}">{{ $i }}x daily</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cycle Length (days)</label>
                    <input
                        type="number"
                        wire:model.live="cycleDays"
                        min="1"
                        max="365"
                        class="block w-full rounded-xl border-cream-300 focus:border-gold-500 focus:ring-gold-500"
                    >
                </div>
            </div>

            <!-- Cycle Results -->
            <div class="mt-6 grid sm:grid-cols-3 gap-4">
                <div class="p-4 bg-cream-50 rounded-xl text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($this->totalDosesInVial, 0) }}</div>
                    <div class="text-sm text-gray-500">doses per vial</div>
                </div>
                <div class="p-4 bg-cream-50 rounded-xl text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($this->totalPeptideForCycle, 1) }} mg</div>
                    <div class="text-sm text-gray-500">total for cycle</div>
                </div>
                <div class="p-4 bg-gold-50 rounded-xl text-center">
                    <div class="text-2xl font-bold text-gold-600">{{ $this->vialsNeeded }}</div>
                    <div class="text-sm text-gold-700">vials needed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Results & Syringe -->
    <div class="space-y-6">
        <!-- Main Result Card -->
        <div class="bg-gradient-to-br from-brown-800 to-brown-900 rounded-2xl shadow-lg p-6 text-white sticky top-24">
            <h3 class="text-lg font-medium text-cream-200 mb-2">Draw this amount:</h3>

            @if($this->exceedsSyringe)
                <div class="bg-red-500/20 border border-red-400 rounded-xl p-4 mb-4">
                    <p class="text-red-200 text-sm">
                        <strong>Warning:</strong> Dose exceeds syringe capacity ({{ $this->maxSyringeUnits }}u).
                        Use a larger syringe or add more bacteriostatic water.
                    </p>
                </div>
            @endif

            <div class="text-center py-6">
                <div class="text-6xl font-bold text-gold-400">
                    {{ number_format($this->unitsToDrawRaw, 1) }}
                </div>
                <div class="text-xl text-cream-300 mt-1">units</div>
                <div class="text-sm text-cream-400 mt-2">
                    ({{ number_format($this->volumeNeeded, 3) }} mL)
                </div>
            </div>

            <!-- Syringe Visualization -->
            <div class="mt-6">
                <div class="text-sm text-cream-300 mb-3 text-center">Syringe Preview ({{ $syringeSize }}u)</div>

                <!-- Syringe SVG -->
                <div class="relative mx-auto" style="width: 200px; height: 60px;">
                    <!-- Syringe body -->
                    <svg viewBox="0 0 200 60" class="w-full h-full" aria-hidden="true">
                        <!-- Barrel -->
                        <rect x="30" y="15" width="150" height="30" rx="4" fill="#e5e5e5" stroke="#999" stroke-width="1"/>

                        <!-- Fill level -->
                        <rect
                            x="31"
                            y="16"
                            width="{{ min($this->syringeFillPercent * 1.48, 148) }}"
                            height="28"
                            rx="3"
                            fill="#C9A227"
                            class="transition-all duration-300"
                        />

                        <!-- Tick marks -->
                        @php
                            $tickCount = match($syringeSize) {
                                '30' => 6,
                                '50' => 10,
                                default => 10,
                            };
                            $tickSpacing = 148 / $tickCount;
                        @endphp
                        @for($i = 0; $i <= $tickCount; $i++)
                            <line
                                x1="{{ 31 + ($i * $tickSpacing) }}"
                                y1="15"
                                x2="{{ 31 + ($i * $tickSpacing) }}"
                                y2="{{ $i % 2 == 0 ? 10 : 12 }}"
                                stroke="#666"
                                stroke-width="1"
                            />
                        @endfor

                        <!-- Plunger -->
                        <rect x="180" y="20" width="15" height="20" rx="2" fill="#888"/>
                        <rect x="192" y="15" width="6" height="30" rx="1" fill="#666"/>

                        <!-- Needle hub -->
                        <polygon points="30,22 30,38 15,33 15,27" fill="#999"/>

                        <!-- Needle -->
                        <line x1="15" y1="30" x2="0" y2="30" stroke="#ccc" stroke-width="2"/>
                    </svg>
                </div>

                <!-- Scale labels -->
                <div class="flex justify-between text-xs text-cream-400 mt-2 px-4">
                    <span>0</span>
                    <span>{{ $this->maxSyringeUnits / 2 }}</span>
                    <span>{{ $this->maxSyringeUnits }}</span>
                </div>
            </div>

            <!-- Quick Reference -->
            <div class="mt-6 pt-6 border-t border-brown-700">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-cream-400">Dose:</span>
                        <span class="text-cream-100 font-medium ml-2">{{ number_format($this->effectiveDose, 1) }} mcg</span>
                    </div>
                    <div>
                        <span class="text-cream-400">Concentration:</span>
                        <span class="text-cream-100 font-medium ml-2">{{ number_format($this->concentration, 0) }} mcg/mL</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formula Reference -->
        <div class="bg-white rounded-2xl shadow-sm border border-cream-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Formulas Used</h3>

            <div class="space-y-4 text-sm">
                <div>
                    <div class="text-gray-500 mb-1">Concentration</div>
                    <code class="block bg-cream-50 px-3 py-2 rounded-lg text-gray-800">
                        (peptide mg × 1000) ÷ water mL
                    </code>
                </div>
                <div>
                    <div class="text-gray-500 mb-1">Volume Needed</div>
                    <code class="block bg-cream-50 px-3 py-2 rounded-lg text-gray-800">
                        dose mcg ÷ concentration
                    </code>
                </div>
                <div>
                    <div class="text-gray-500 mb-1">Syringe Units</div>
                    <code class="block bg-cream-50 px-3 py-2 rounded-lg text-gray-800">
                        volume mL × 100
                    </code>
                </div>
            </div>
        </div>
    </div>
</div>
