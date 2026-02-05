<x-account-layout>
    <div class="space-y-6">
        {{-- Profile Form --}}
        <form action="{{ route('account.profile.update') }}" method="POST" class="bg-white dark:bg-brown-800 rounded-xl shadow-sm border border-cream-200 dark:border-brown-700 p-6">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-semibold text-gray-900 dark:text-cream-100 mb-6">Personal Information</h2>

            <div class="space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-2">
                        Display Name
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                           class="w-full rounded-lg border-cream-200 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 focus:ring-gold-500 focus:border-gold-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bio --}}
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-2">
                        Bio
                    </label>
                    <textarea name="bio" id="bio" rows="3" maxlength="200"
                              class="w-full rounded-lg border-cream-200 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 focus:ring-gold-500 focus:border-gold-500"
                              placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-cream-500">
                        <span x-data="{ count: {{ strlen($user->bio ?? '') }} }" x-init="$el.closest('div').querySelector('textarea').addEventListener('input', e => count = e.target.value.length)">
                            <span x-text="count">0</span>/200 characters
                        </span>
                    </p>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-gold-500 text-white rounded-full font-medium text-sm hover:bg-gold-600 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>

        {{-- Email (Read Only) --}}
        <div class="bg-white dark:bg-brown-800 rounded-xl shadow-sm border border-cream-200 dark:border-brown-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-cream-100 mb-6">Contact Information</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-cream-300 mb-2">
                    Email Address
                </label>
                <div class="flex items-center gap-3">
                    <input type="email" value="{{ $user->email }}" disabled
                           class="flex-1 rounded-lg border-cream-200 dark:border-brown-600 dark:bg-brown-700 dark:text-cream-100 bg-cream-50 dark:bg-brown-900 cursor-not-allowed">
                    @if($user->email_verified_at)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Verified
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Account Info --}}
        <div class="bg-white dark:bg-brown-800 rounded-xl shadow-sm border border-cream-200 dark:border-brown-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-cream-100 mb-6">Account Details</h2>

            <dl class="grid sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm text-gray-500 dark:text-cream-500">Member Since</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-cream-100">{{ $user->created_at->format('F j, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-cream-500">Account Type</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-cream-100">{{ ucfirst($user->role) }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-account-layout>
