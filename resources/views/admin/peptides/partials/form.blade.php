@php $peptide = $peptide ?? null; @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        @include('admin.peptides.partials.form-basic')
        @include('admin.peptides.partials.form-dosing')
        @include('admin.peptides.partials.form-content')
        @include('admin.peptides.partials.form-molecular')
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        @include('admin.peptides.partials.form-sidebar')
    </div>
</div>

<!-- Submit -->
<div class="mt-6 flex items-center justify-end gap-4">
    <a href="{{ route('admin.peptides.index') }}" class="btn btn-ghost">Cancel</a>
    <button type="submit" class="btn btn-primary">
        {{ $peptide ? 'Update Peptide' : 'Create Peptide' }}
    </button>
</div>
