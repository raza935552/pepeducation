<x-admin-layout title="Edit Supporter">
    <x-slot name="header">Edit Supporter</x-slot>

    <form action="{{ route('admin.supporters.update', $supporter) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.supporters.partials.form', ['supporter' => $supporter])
    </form>
</x-admin-layout>
