<x-admin-layout>
    <x-slot name="header">Edit {{ $peptide->name }}</x-slot>

    <form action="{{ route('admin.peptides.update', $peptide) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.peptides.partials.form', ['peptide' => $peptide])
    </form>
</x-admin-layout>
