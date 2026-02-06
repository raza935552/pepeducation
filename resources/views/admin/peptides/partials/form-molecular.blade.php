<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Molecular & Pharmacokinetics</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Molecular Weight -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Molecular Weight (Da)</label>
            <input type="number" step="0.01" name="molecular_weight"
                   value="{{ old('molecular_weight', $peptide?->molecular_weight) }}"
                   class="input w-full" placeholder="e.g., 1419.53">
        </div>

        <!-- Amino Acid Length -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amino Acid Length</label>
            <input type="number" name="amino_acid_length"
                   value="{{ old('amino_acid_length', $peptide?->amino_acid_length) }}"
                   class="input w-full" placeholder="e.g., 15">
        </div>

        <!-- Peak Time -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Peak Time</label>
            <input type="text" name="peak_time" value="{{ old('peak_time', $peptide?->peak_time) }}"
                   class="input w-full" placeholder="e.g., 1 hour">
        </div>

        <!-- Half Life -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Half Life</label>
            <input type="text" name="half_life" value="{{ old('half_life', $peptide?->half_life) }}"
                   class="input w-full" placeholder="e.g., 4 hours">
        </div>

        <!-- Clearance Time -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Clearance Time</label>
            <input type="text" name="clearance_time" value="{{ old('clearance_time', $peptide?->clearance_time) }}"
                   class="input w-full" placeholder="e.g., ~20 hours">
        </div>
    </div>
</div>
