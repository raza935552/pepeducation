<x-admin-layout title="Add Supporter">
    <x-slot name="header">Add Supporter</x-slot>

    <form action="{{ route('admin.supporters.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.supporters.partials.form')
    </form>
</x-admin-layout>
