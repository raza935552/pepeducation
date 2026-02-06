<x-account-layout>
    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('account.preferences.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Notifications --}}
        <div class="bg-white rounded-xl shadow-sm border border-cream-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Notifications</h2>

            <div class="space-y-5">
                {{-- Edit Status --}}
                <label class="flex items-start gap-4 cursor-pointer">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="notify_edit_status" value="1"
                               {{ $preferences->notify_edit_status ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-cream-200 rounded-full peer-checked:bg-gold-500 transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">Edit Status Notifications</div>
                        <div class="text-sm text-gray-500">Get notified when your edits are reviewed, approved, or rejected</div>
                    </div>
                </label>

                {{-- Marketing --}}
                <label class="flex items-start gap-4 cursor-pointer">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="notify_marketing" value="1"
                               {{ $preferences->notify_marketing ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-cream-200 rounded-full peer-checked:bg-gold-500 transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">Marketing Emails</div>
                        <div class="text-sm text-gray-500">Receive occasional updates about new features and platform announcements</div>
                    </div>
                </label>

                {{-- Weekly Digest --}}
                <label class="flex items-start gap-4 cursor-pointer">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="notify_weekly_digest" value="1"
                               {{ $preferences->notify_weekly_digest ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-cream-200 rounded-full peer-checked:bg-gold-500 transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">Weekly Digest</div>
                        <div class="text-sm text-gray-500">Get a weekly summary of your contributions and platform activity</div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Privacy --}}
        <div class="bg-white rounded-xl shadow-sm border border-cream-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Privacy & Data Usage</h2>

            <label class="flex items-start gap-4 cursor-pointer">
                <div class="relative flex items-center">
                    <input type="checkbox" name="data_usage_opt_in" value="1"
                           {{ $preferences->data_usage_opt_in ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-cream-200 rounded-full peer-checked:bg-gold-500 transition-colors"></div>
                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform"></div>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">Join the Future of Personalized Peptide Research</div>
                    <div class="text-sm text-gray-500">Opt in early! We're not collecting data yet, but when community features launch, your anonymous usage patterns will help improve recommendations.</div>
                </div>
            </label>
        </div>

        {{-- Save --}}
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-gold-500 text-white rounded-full font-medium text-sm hover:bg-gold-600 transition-colors">
                Save Preferences
            </button>
        </div>
    </form>
</x-account-layout>
