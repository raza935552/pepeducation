<x-admin-layout>
    <x-slot name="header">Settings & Integrations</x-slot>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Maintenance Mode -->
        <div class="card p-6 border-l-4 border-amber-400">
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Maintenance Mode
            </h3>
            <p class="text-sm text-gray-500 mb-4">Block public access while the team tests behind a password.</p>

            <div class="space-y-4">
                @php
                    $mEnabled = ($settings['general'] ?? collect())->firstWhere('key', 'maintenance_enabled');
                    $mPassword = ($settings['general'] ?? collect())->firstWhere('key', 'maintenance_password');
                    $mMessage = ($settings['general'] ?? collect())->firstWhere('key', 'maintenance_message');
                @endphp

                {{-- Enable toggle --}}
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="settings[700][value]" value="0">
                    <input type="checkbox" name="settings[700][value]" value="1"
                        {{ $mEnabled && filter_var($mEnabled->value, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                    <input type="hidden" name="settings[700][group]" value="general">
                    <input type="hidden" name="settings[700][key]" value="maintenance_enabled">
                    <div>
                        <span class="font-medium text-gray-700">Enable Maintenance Mode</span>
                        <p class="text-sm text-gray-500">When ON, all public pages show a maintenance screen.</p>
                    </div>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- QA Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">QA Password</label>
                        <input type="text" name="settings[701][value]"
                            value="{{ $mPassword->value ?? '' }}"
                            placeholder="Set a team password"
                            class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <input type="hidden" name="settings[701][group]" value="general">
                        <input type="hidden" name="settings[701][key]" value="maintenance_password">
                        <p class="text-xs text-gray-500 mt-1">Team enters this to bypass maintenance page</p>
                    </div>

                    {{-- Custom message --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maintenance Message</label>
                        <input type="text" name="settings[702][value]"
                            value="{{ $mMessage->value ?? 'We are getting things ready. Check back soon!' }}"
                            placeholder="Custom maintenance message"
                            class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <input type="hidden" name="settings[702][group]" value="general">
                        <input type="hidden" name="settings[702][key]" value="maintenance_message">
                    </div>
                </div>
            </div>
        </div>

        <!-- Klaviyo Integration -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Klaviyo Integration
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($settings['integrations'] ?? [] as $setting)
                    @if(str_starts_with($setting->key, 'klaviyo'))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $setting->description }}</label>
                            <input type="{{ $setting->type === 'encrypted' ? 'password' : 'text' }}"
                                name="settings[{{ $loop->index }}][value]"
                                value="{{ $setting->type === 'encrypted' ? '' : $setting->value }}"
                                placeholder="{{ $setting->type === 'encrypted' ? '••••••••' : '' }}"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <input type="hidden" name="settings[{{ $loop->index }}][group]" value="{{ $setting->group }}">
                            <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $setting->key }}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Fast Peptix Integration -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                Fast Peptix Integration
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($settings['integrations'] ?? [] as $setting)
                    @if(str_starts_with($setting->key, 'fastpeptix'))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $setting->description }}</label>
                            <input type="{{ $setting->type === 'encrypted' ? 'password' : 'text' }}"
                                name="settings[{{ 100 + $loop->index }}][value]"
                                value="{{ $setting->type === 'encrypted' ? '' : $setting->value }}"
                                placeholder="{{ $setting->type === 'encrypted' ? '••••••••' : '' }}"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <input type="hidden" name="settings[{{ 100 + $loop->index }}][group]" value="{{ $setting->group }}">
                            <input type="hidden" name="settings[{{ 100 + $loop->index }}][key]" value="{{ $setting->key }}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Journey API (Shop Integration) -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                Journey API (Shop Integration)
            </h3>
            <p class="text-sm text-gray-500 mb-4">
                Use this API key to fetch customer journey data from your shop. Endpoint: <code class="bg-gray-100 px-2 py-1 rounded">{{ url('/api/journey/{session_id}') }}?key=YOUR_KEY</code>
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Journey API Key</label>
                    <div class="flex gap-2">
                        <input type="text" name="settings[400][value]"
                            value="{{ $settings['integrations']?->firstWhere('key', 'journey_api_key')?->value ?? '' }}"
                            placeholder="Generate or enter API key"
                            id="journey-api-key"
                            class="flex-1 rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold font-mono text-sm">
                        <input type="hidden" name="settings[400][group]" value="integrations">
                        <input type="hidden" name="settings[400][key]" value="journey_api_key">
                        <button type="button" onclick="generateApiKey()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium">
                            Generate
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Share this key with your shop to enable journey lookups</p>
                </div>
            </div>
        </div>

        <!-- Unsplash (Stock Photos) -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-gray-800" viewBox="0 0 24 24" fill="currentColor"><path d="M7.5 6.75V0h9v6.75h-9zm9 3.75H24V24H0V10.5h7.5v6.75h9V10.5z"/></svg>
                Unsplash (Stock Photos)
            </h3>
            <p class="text-sm text-gray-500 mb-4">
                Add free stock photos to the page builder. <a href="https://unsplash.com/developers" target="_blank" rel="noopener" class="text-brand-gold hover:underline">Get a free API key</a>
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Access Key</label>
                    <input type="text" name="settings[600][value]"
                        value="{{ ($settings['integrations'] ?? collect())->firstWhere('key', 'unsplash_access_key')?->value ?? '' }}"
                        placeholder="Enter Unsplash Access Key"
                        class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold font-mono text-sm">
                    <input type="hidden" name="settings[600][group]" value="integrations">
                    <input type="hidden" name="settings[600][key]" value="unsplash_access_key">
                </div>
            </div>
        </div>

        <!-- Tracking Pixels -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Tracking Pixels
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($settings['tracking'] ?? [] as $setting)
                    @if(str_contains($setting->key, '_id'))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $setting->description }}</label>
                            <input type="text" name="settings[{{ 200 + $loop->index }}][value]" value="{{ $setting->value }}"
                                class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                            <input type="hidden" name="settings[{{ 200 + $loop->index }}][group]" value="{{ $setting->group }}">
                            <input type="hidden" name="settings[{{ 200 + $loop->index }}][key]" value="{{ $setting->key }}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Privacy & Compliance -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Privacy & Compliance
            </h3>
            <div class="space-y-4">
                @php $consentSetting = ($settings['tracking'] ?? collect())->firstWhere('key', 'cookie_consent_enabled'); @endphp
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="settings[500][value]" value="0">
                    <input type="checkbox" name="settings[500][value]" value="1"
                        {{ $consentSetting && filter_var($consentSetting->value, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                    <input type="hidden" name="settings[500][group]" value="tracking">
                    <input type="hidden" name="settings[500][key]" value="cookie_consent_enabled">
                    <div>
                        <span class="font-medium text-gray-700">Cookie Consent Banner</span>
                        <p class="text-sm text-gray-500">Show a cookie consent banner. Tracking will be paused until the visitor accepts.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Engagement Scoring -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                Engagement Scoring
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($settings['scoring'] ?? [] as $setting)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $setting->description }}</label>
                        <input type="number" name="settings[{{ 300 + $loop->index }}][value]" value="{{ $setting->value }}"
                            class="w-full rounded-lg border-gray-300 focus:border-brand-gold focus:ring-brand-gold">
                        <input type="hidden" name="settings[{{ 300 + $loop->index }}][group]" value="{{ $setting->group }}">
                        <input type="hidden" name="settings[{{ 300 + $loop->index }}][key]" value="{{ $setting->key }}">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>

    <script>
        function generateApiKey() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let key = 'pp_';
            for (let i = 0; i < 32; i++) {
                key += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('journey-api-key').value = key;
        }
    </script>
</x-admin-layout>
