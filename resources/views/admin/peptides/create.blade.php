<x-admin-layout>
    <x-slot name="header">Add New Peptide</x-slot>

    <form action="{{ route('admin.peptides.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.peptides.partials.form')
    </form>
</x-admin-layout>
