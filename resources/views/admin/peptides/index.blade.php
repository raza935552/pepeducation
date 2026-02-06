<x-admin-layout>
    <x-slot name="header">Peptides</x-slot>
    <x-slot name="headerAction">
        <a href="{{ route('admin.peptides.create') }}" class="btn btn-primary">
            <svg aria-hidden="true" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Peptide
        </a>
    </x-slot>

    @include('admin.peptides.partials.filters')
    @include('admin.peptides.partials.table')
</x-admin-layout>
