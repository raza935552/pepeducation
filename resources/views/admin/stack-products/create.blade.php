<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.stack-products.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg aria-hidden="true" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span>Create Stack Product</span>
        </div>
    </x-slot>

    @include('admin.stack-products.partials.form', ['product' => null])
</x-admin-layout>
