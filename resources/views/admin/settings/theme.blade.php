<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Theme & Colors</span>
            <a href="{{ route('admin.settings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Settings
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.theme.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        @php $sections = \App\Services\ThemeService::SECTIONS; @endphp

        @foreach($sections as $sectionTitle => $fields)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-base font-semibold text-gray-800">{{ $sectionTitle }}</h3>
                </div>
                <div class="p-6 space-y-5">
                    @foreach($fields as $key => $def)
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <label class="block text-sm font-medium text-gray-700">{{ $def['label'] }}</label>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $def['desc'] }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <input type="color"
                                       class="color-picker w-9 h-9 rounded border border-gray-300 cursor-pointer p-0.5"
                                       value="{{ $colors[$key] }}"
                                       data-target="{{ $key }}">
                                <input type="text"
                                       name="{{ $key }}"
                                       class="color-hex w-24 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs text-center"
                                       value="{{ $colors[$key] }}"
                                       pattern="^#[0-9A-Fa-f]{6}$"
                                       maxlength="7"
                                       data-target="{{ $key }}">
                            </div>
                        </div>
                        @if(!$loop->last)<div class="border-b border-gray-100"></div>@endif
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex items-center justify-between">
            <form action="{{ route('admin.settings.theme.reset') }}" method="POST" class="inline"
                  onsubmit="return confirm('Reset all theme colors to defaults?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border bg-red-50 text-red-700 border-red-200 hover:bg-red-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset to Defaults
                </button>
            </form>
            <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 text-sm font-medium">Save Theme</button>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.color-picker').forEach(picker => {
            const key = picker.dataset.target;
            const hex = document.querySelector(`input.color-hex[data-target="${key}"]`);
            picker.addEventListener('input', () => hex.value = picker.value.toUpperCase());
            hex.addEventListener('input', () => { if (/^#[0-9A-Fa-f]{6}$/.test(hex.value)) picker.value = hex.value; });
        });
    });
    </script>
</x-admin-layout>
