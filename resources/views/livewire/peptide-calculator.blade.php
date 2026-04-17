<div class="space-y-4">
    <!-- Result Card (top, always visible) -->
    <div class="bg-gradient-to-br from-brown-800 to-brown-900 rounded-2xl shadow-lg p-5 text-white">
        @if($this->exceedsSyringe)
            <div class="bg-red-500/20 border border-red-400 rounded-xl p-3 mb-3">
                <p class="text-red-200 text-sm">
                    <strong>Warning:</strong> Dose exceeds syringe capacity ({{ $this->maxSyringeUnits }}u).
                    Use a larger syringe or add more water.
                </p>
            </div>
        @endif

        <div class="flex items-center justify-between gap-4">
            <!-- Draw Amount -->
            <div class="text-center flex-1">
                <div class="text-xs text-cream-400 uppercase tracking-wide mb-1">Draw</div>
                <div class="text-4xl font-bold text-gold-400">{{ number_format($this->unitsToDrawRaw, 1) }}</div>
                <div class="text-sm text-cream-300">units ({{ number_format($this->volumeNeeded, 3) }} mL)</div>
            </div>

            <!-- Syringe Visualization -->
            <div class="flex-1 max-w-[180px]">
                <div class="text-xs text-cream-400 text-center mb-1">{{ $syringeSize }}u Syringe</div>
                <div class="relative mx-auto" style="width: 160px; height: 50px;">
                    <svg viewBox="0 0 200 60" class="w-full h-full" aria-hidden="true">
                        <rect x="30" y="15" width="150" height="30" rx="4" fill="#e5e5e5" stroke="#999" stroke-width="1"/>
                        <rect x="31" y="16" width="{{ min($this->syringeFillPercent * 1.48, 148) }}" height="28" rx="3" fill="#C9A227" class="transition-all duration-300"/>
                        @php
                            $tickCount = match($syringeSize) { '30' => 6, '50' => 10, default => 10 };
                            $tickSpacing = 148 / $tickCount;
                        @endphp
                        @for($i = 0; $i <= $tickCount; $i++)
                            <line x1="{{ 31 + ($i * $tickSpacing) }}" y1="15" x2="{{ 31 + ($i * $tickSpacing) }}" y2="{{ $i % 2 == 0 ? 10 : 12 }}" stroke="#666" stroke-width="1"/>
                        @endfor
                        <rect x="180" y="20" width="15" height="20" rx="2" fill="#888"/>
                        <rect x="192" y="15" width="6" height="30" rx="1" fill="#666"/>
                        <polygon points="30,22 30,38 15,33 15,27" fill="#999"/>
                        <line x1="15" y1="30" x2="0" y2="30" stroke="#ccc" stroke-width="2"/>
                    </svg>
                </div>
                <div class="flex justify-between text-[10px] text-cream-500 px-2">
                    <span>0</span>
                    <span>{{ $this->maxSyringeUnits / 2 }}</span>
                    <span>{{ $this->maxSyringeUnits }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-3 gap-3 mt-4 pt-3 border-t border-brown-700">
            <div class="text-center">
                <div class="text-xs text-cream-400">Dose</div>
                <div class="text-sm font-semibold text-cream-100">{{ number_format($this->effectiveDose, 1) }} mcg</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-cream-400">Concentration</div>
                <div class="text-sm font-semibold text-cream-100">{{ number_format($this->concentration, 0) }} mcg/mL</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-cream-400">Per Unit</div>
                <div class="text-sm font-semibold text-cream-100">{{ number_format($this->mcgPerUnit, 2) }} mcg</div>
            </div>
        </div>
    </div>

    <!-- Reconstitution Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-cream-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <svg aria-hidden="true" class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
            Reconstitution
        </h3>

        <div class="grid grid-cols-2 gap-3">
            <!-- Vial Size (Select) -->
            <div>
                <label for="peptideAmount" class="block text-xs font-medium text-gray-600 mb-1">Vial Size</label>
                <select
                    id="peptideAmount"
                    wire:model.live="peptideAmount"
                    class="block w-full rounded-lg border-cream-300 focus:border-gold-500 focus:ring-gold-500 text-sm py-2"
                >
                    <option value="1">1 mg</option>
                    <option value="2">2 mg</option>
                    <option value="2.5">2.5 mg</option>
                    <option value="3">3 mg</option>
                    <option value="5">5 mg</option>
                    <option value="10">10 mg</option>
                    <option value="15">15 mg</option>
                    <option value="20">20 mg</option>
                    <option value="30">30 mg</option>
                    <option value="50">50 mg</option>
                </select>
            </div>

            <!-- Bacteriostatic Water (Select) -->
            <div>
                <label for="waterAmount" class="block text-xs font-medium text-gray-600 mb-1">BAC Water</label>
                <select
                    id="waterAmount"
                    wire:model.live="waterAmount"
                    class="block w-full rounded-lg border-cream-300 focus:border-gold-500 focus:ring-gold-500 text-sm py-2"
                >
                    <option value="0.5">0.5 mL</option>
                    <option value="1">1 mL</option>
                    <option value="1.5">1.5 mL</option>
                    <option value="2">2 mL</option>
                    <option value="2.5">2.5 mL</option>
                    <option value="3">3 mL</option>
                    <option value="4">4 mL</option>
                    <option value="5">5 mL</option>
                </select>
            </div>
        </div>

        <!-- Concentration Result -->
        <div class="mt-3 p-3 bg-cream-50 rounded-lg flex justify-between items-center">
            <span class="text-xs text-gray-500">Concentration</span>
            <span class="text-sm font-bold text-gold-600">{{ number_format($this->concentration, 2) }} mcg/mL</span>
        </div>
    </div>

    <!-- Dosing Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-cream-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <svg aria-hidden="true" class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Dosing
        </h3>

        <!-- Body Weight Toggle -->
        <label class="flex items-center gap-2 cursor-pointer mb-3">
            <input type="checkbox" wire:model.live="useBodyWeight" class="rounded border-cream-300 text-gold-500 focus:ring-gold-500">
            <span class="text-xs text-gray-600">Calculate by body weight</span>
        </label>

        @if($useBodyWeight)
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Body Weight</label>
                        <div class="flex gap-1">
                            <input type="number" wire:model.live="bodyWeight" min="20" max="300" class="block w-full rounded-lg border-cream-300 focus:border-gold-500 focus:ring-gold-500 text-sm py-2">
                            <select wire:model.live="weightUnit" class="rounded-lg border-cream-300 focus:border-gold-500 focus:ring-gold-500 text-sm py-2 w-16">
                                <option value="kg">kg</option>
                                <option value="lb">lb</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dose per kg</label>
                        <div class="relative">
                            <input type="number" wire:model.live="dosePerKg" min="0.1" max="100" step="0.1" class="block w-full rounded-lg border-cream-300 focus:border-gold-500 focus:ring-gold-500 text-sm py-2 pr-14">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-medium bg-cream-100 px-1.5 py-0.5 rounded">mcg/kg</span>
                        </div>
                    </div>
                </div>
                <div class="p-3 bg-gold-50 rounded-lg flex justify-between items-center">
                    <span class="text-xs text-gray-500">Calculated Dose</span>
                    <span class="text-sm font-bold text-gold-600">{{ number_format($this->effectiveDose, 1) }} mcg</span>
                </div>
            </div>
        @else
            <!-- Fixed Dose with unit toggle -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Desired Dose</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="number" wire:model.live="desiredDose" min="0.01" max="10000" step="any" class="block w-full rounded-lg border-cream-300 focus:border-gold-500 focus:ring-gold-500 text-sm py-2 pr-14">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-medium bg-cream-100 px-1.5 py-0.5 rounded">{{ $doseUnit }}</span>
                    </div>
                    <div class="flex rounded-lg border border-cream-300 overflow-hidden">
                        <button type="button" wire:click="$set('doseUnit', 'mcg')"
                            class="px-3 py-2 text-xs font-medium transition-colors {{ $doseUnit === 'mcg' ? 'bg-gold-500 text-white' : 'bg-white text-gray-600 hover:bg-cream-50' }}">
                            mcg
                        </button>
                        <button type="button" wire:click="$set('doseUnit', 'mg')"
                            class="px-3 py-2 text-xs font-medium transition-colors {{ $doseUnit === 'mg' ? 'bg-gold-500 text-white' : 'bg-white text-gray-600 hover:bg-cream-50' }}">
                            mg
                        </button>
                    </div>
                </div>
                @if($doseUnit === 'mg')
                    <p class="mt-1 text-[11px] text-gray-400">= {{ number_format($this->effectiveDose, 0) }} mcg</p>
                @endif
            </div>
        @endif

        <!-- Syringe Size -->
        <div class="mt-4">
            <label class="block text-xs font-medium text-gray-600 mb-1">Syringe Size</label>
            <div class="grid grid-cols-3 gap-2">
                @foreach(['30' => '0.3 mL (30u)', '50' => '0.5 mL (50u)', '100' => '1.0 mL (100u)'] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="syringeSize" value="{{ $value }}" class="sr-only peer">
                        <div class="text-center py-2 px-2 rounded-lg border-2 transition-all text-xs font-medium
                            peer-checked:border-gold-500 peer-checked:bg-gold-50 peer-checked:text-gold-700
                            border-cream-200 text-gray-600 hover:border-gold-300">
                            {{ $label }}
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Cycle Planning -->
    <div class="bg-white rounded-2xl shadow-sm border border-cream-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
            <svg aria-hidden="true" class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Cycle Planning
        </h3>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Injections / Day</label>
                <select wire:model.live="injectionsPerDay" class="block w-full rounded-lg border-cream-300 focus:border-gold-500 focus:ring-gold-500 text-sm py-2">
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }}x daily</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Cycle Length</label>
                <div class="relative">
                    <input type="number" wire:model.live="cycleDays" min="1" max="365" class="block w-full rounded-lg border-cream-300 focus:border-gold-500 focus:ring-gold-500 text-sm py-2 pr-12">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-medium bg-cream-100 px-1.5 py-0.5 rounded">days</span>
                </div>
            </div>
        </div>

        <!-- Cycle Results -->
        <div class="mt-3 grid grid-cols-3 gap-2">
            <div class="p-3 bg-cream-50 rounded-lg text-center">
                <div class="text-lg font-bold text-gray-900">{{ number_format($this->totalDosesInVial, 0) }}</div>
                <div class="text-[11px] text-gray-500">doses/vial</div>
            </div>
            <div class="p-3 bg-cream-50 rounded-lg text-center">
                <div class="text-lg font-bold text-gray-900">{{ number_format($this->totalPeptideForCycle, 1) }}</div>
                <div class="text-[11px] text-gray-500">mg total</div>
            </div>
            <div class="p-3 bg-gold-50 rounded-lg text-center">
                <div class="text-lg font-bold text-gold-600">{{ $this->vialsNeeded }}</div>
                <div class="text-[11px] text-gold-700">vials needed</div>
            </div>
        </div>
    </div>
</div>
