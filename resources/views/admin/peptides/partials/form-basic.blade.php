<div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $peptide?->name) }}"
                   class="input w-full @error('name') border-red-500 @enderror" required>
            @error('name')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Full Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" name="full_name" value="{{ old('full_name', $peptide?->full_name) }}"
                   class="input w-full">
        </div>

        <!-- Abbreviation -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Abbreviation</label>
            <input type="text" name="abbreviation" value="{{ old('abbreviation', $peptide?->abbreviation) }}"
                   class="input w-full" maxlength="20">
        </div>

        <!-- Type -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <input type="text" name="type" value="{{ old('type', $peptide?->type) }}"
                   class="input w-full" placeholder="e.g., Pentadecapeptide, GLP-1 Agonist">
        </div>
    </div>
</div>
