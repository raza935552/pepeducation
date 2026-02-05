<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Supporter Details</h3>

            <div class="space-y-4">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name', $supporter->name ?? '') }}"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gold-500 focus:border-gold-500"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Website URL --}}
                <div>
                    <label for="website_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Website URL
                    </label>
                    <input type="url" name="website_url" id="website_url"
                           value="{{ old('website_url', $supporter->website_url ?? '') }}"
                           placeholder="https://example.com"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gold-500 focus:border-gold-500">
                    @error('website_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Logo --}}
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Logo
                    </label>
                    @if(isset($supporter) && $supporter->logo)
                        <div class="mb-3 flex items-center gap-4">
                            <img src="{{ Storage::url($supporter->logo) }}" alt="{{ $supporter->name }}"
                                 class="w-16 h-16 rounded-lg object-contain bg-gray-100 dark:bg-gray-700 p-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Current logo</span>
                        </div>
                    @endif
                    <input type="file" name="logo" id="logo"
                           accept="image/*"
                           class="w-full text-sm text-gray-500 dark:text-gray-400
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-medium
                                  file:bg-gold-50 file:text-gold-700
                                  hover:file:bg-gold-100
                                  dark:file:bg-gold-900/20 dark:file:text-gold-400">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Recommended: Square image, 200x200px or larger. Max 2MB.
                    </p>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Settings --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Settings</h3>

            <div class="space-y-4">
                {{-- Tier --}}
                <div>
                    <label for="tier" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tier <span class="text-red-500">*</span>
                    </label>
                    <select name="tier" id="tier"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gold-500 focus:border-gold-500">
                        <option value="platinum" {{ old('tier', $supporter->tier ?? '') === 'platinum' ? 'selected' : '' }}>Platinum</option>
                        <option value="gold" {{ old('tier', $supporter->tier ?? '') === 'gold' ? 'selected' : '' }}>Gold</option>
                        <option value="silver" {{ old('tier', $supporter->tier ?? '') === 'silver' ? 'selected' : '' }}>Silver</option>
                        <option value="bronze" {{ old('tier', $supporter->tier ?? 'bronze') === 'bronze' ? 'selected' : '' }}>Bronze</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gold-500 focus:border-gold-500">
                        <option value="active" {{ old('status', $supporter->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $supporter->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Display Order --}}
                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Display Order
                    </label>
                    <input type="number" name="display_order" id="display_order"
                           value="{{ old('display_order', $supporter->display_order ?? 0) }}"
                           min="0"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-gold-500 focus:border-gold-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Lower numbers appear first
                    </p>
                </div>

                {{-- Featured --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1"
                           {{ old('is_featured', $supporter->is_featured ?? false) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-gold-500 focus:ring-gold-500">
                    <label for="is_featured" class="text-sm text-gray-700 dark:text-gray-300">
                        Featured supporter
                    </label>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-4 py-2 bg-gold-500 hover:bg-gold-600 text-white font-medium rounded-lg transition-colors">
                    {{ isset($supporter) ? 'Update Supporter' : 'Create Supporter' }}
                </button>
                <a href="{{ route('admin.supporters.index') }}"
                   class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-center hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</div>
