<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>Customer.io Integration</span>
            <a href="{{ route('admin.settings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Settings
            </a>
        </div>
    </x-slot>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.customerio.update') }}" method="POST" class="space-y-6" id="cio-form">
        @csrf
        @method('PUT')

        {{-- API Configuration --}}
        <div class="card p-6 border-l-4 border-green-400">
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                API Configuration
            </h3>
            <p class="text-sm text-gray-500 mb-4">Connect your Customer.io workspace to sync subscribers and track events.</p>

            <div class="space-y-4">
                {{-- Enable toggle --}}
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_enabled" value="0">
                    <input type="checkbox" name="is_enabled" value="1"
                        {{ $settings->is_enabled ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-gray-300 text-green-500 focus:ring-green-500">
                    <div>
                        <span class="font-medium text-gray-700">Enable Customer.io Integration</span>
                        <p class="text-sm text-gray-500">When ON, quiz events and subscriber data sync to Customer.io.</p>
                    </div>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Site ID --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Site ID</label>
                        <input type="password" name="site_id" id="cio-site-id"
                            value=""
                            placeholder="{{ $settings->site_id ? '--------  (saved)' : 'Enter Site ID' }}"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 font-mono text-sm">
                        <p class="text-xs text-gray-500 mt-1">Found in Customer.io under Settings &rarr; Workspace Settings &rarr; API credentials.</p>
                    </div>

                    {{-- API Key --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Key (Tracking)</label>
                        <input type="password" name="api_key" id="cio-api-key"
                            value=""
                            placeholder="{{ $settings->api_key ? '--------  (saved)' : 'Enter API Key' }}"
                            class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 font-mono text-sm">
                        <p class="text-xs text-gray-500 mt-1">The Tracking API key from your Customer.io workspace. Leave blank to keep current value.</p>
                    </div>
                </div>

                {{-- Region --}}
                <div class="max-w-xs">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Region</label>
                    <select name="region" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-sm">
                        <option value="us" {{ $settings->region === 'us' ? 'selected' : '' }}>US (track.customer.io)</option>
                        <option value="eu" {{ $settings->region === 'eu' ? 'selected' : '' }}>EU (track-eu.customer.io)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Select the region matching your Customer.io workspace.</p>
                </div>

                {{-- Test Connection --}}
                <div class="pt-2 border-t border-gray-100">
                    <button type="button" id="cio-test-btn"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border transition-colors bg-green-50 text-green-700 border-green-200 hover:bg-green-100">
                        <svg id="cio-test-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <svg id="cio-test-spinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                        <span id="cio-test-label">Test Connection</span>
                    </button>
                    <div id="cio-test-result" class="mt-2 hidden">
                        <p id="cio-test-message" class="p-3 rounded-lg text-sm font-medium"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Setup Instructions --}}
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-medium mb-1">How to get your Customer.io credentials</p>
                    <ol class="list-decimal list-inside space-y-1 text-blue-700">
                        <li>Log in to your <a href="https://fly.customer.io" target="_blank" rel="noopener" class="underline hover:text-blue-900">Customer.io dashboard</a>.</li>
                        <li>Go to <strong>Settings</strong> &rarr; <strong>Workspace Settings</strong>.</li>
                        <li>Click <strong>API credentials</strong> in the left sidebar.</li>
                        <li>Copy the <strong>Site ID</strong> and <strong>Tracking API Key</strong> into the fields above.</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Event Tracking --}}
        <div class="card p-6 border-l-4 border-green-400">
            <h3 class="text-lg font-semibold mb-1 flex items-center gap-2">
                <svg aria-hidden="true" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Event Tracking
            </h3>
            <p class="text-sm text-gray-500 mb-4">Choose which events are sent to Customer.io when they occur on your site.</p>

            <div class="space-y-3">
                @php
                    $events = [
                        ['name' => 'track_quiz_started', 'label' => 'Quiz Started', 'desc' => 'When a quiz begins'],
                        ['name' => 'track_quiz_completed', 'label' => 'Quiz Completed', 'desc' => 'When a quiz is finished'],
                        ['name' => 'track_email_captured', 'label' => 'Email Captured', 'desc' => 'When email is collected during quiz'],
                        ['name' => 'track_quiz_abandoned', 'label' => 'Quiz Abandoned', 'desc' => 'When user leaves mid-quiz'],
                        ['name' => 'track_lead_magnet_download', 'label' => 'Lead Magnet Download', 'desc' => 'When a lead magnet is downloaded'],
                        ['name' => 'track_outbound_click', 'label' => 'Outbound Click', 'desc' => 'When user clicks to vendor shop'],
                        ['name' => 'track_stack_completed', 'label' => 'Stack Builder Completed', 'desc' => 'When stack builder is finished'],
                        ['name' => 'track_subscribed', 'label' => 'Subscribed', 'desc' => 'When user subscribes via popup/form'],
                        ['name' => 'enable_page_tracking', 'label' => 'Page View Tracking', 'desc' => 'Auto-track page views via JS snippet'],
                    ];
                @endphp

                @foreach($events as $event)
                    <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <input type="hidden" name="{{ $event['name'] }}" value="0">
                        <input type="checkbox" name="{{ $event['name'] }}" value="1"
                            {{ $settings->{$event['name']} ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-green-500 focus:ring-green-500">
                        <div>
                            <span class="font-medium text-gray-700">{{ $event['label'] }}</span>
                            <span class="text-sm text-gray-500 ml-1">&mdash; {{ $event['desc'] }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Save --}}
        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const testBtn = document.getElementById('cio-test-btn');
            const testIcon = document.getElementById('cio-test-icon');
            const testSpinner = document.getElementById('cio-test-spinner');
            const testLabel = document.getElementById('cio-test-label');
            const testResultDiv = document.getElementById('cio-test-result');
            const testMessage = document.getElementById('cio-test-message');
            const siteIdField = document.getElementById('cio-site-id');
            const apiKeyField = document.getElementById('cio-api-key');

            let siteIdDirty = false;
            let apiKeyDirty = false;

            siteIdField.addEventListener('input', function () { siteIdDirty = this.value.length > 0; });
            apiKeyField.addEventListener('input', function () { apiKeyDirty = this.value.length > 0; });

            testBtn.addEventListener('click', async function () {
                testBtn.disabled = true;
                testBtn.classList.add('cursor-wait', 'bg-gray-50', 'text-gray-400', 'border-gray-200');
                testBtn.classList.remove('bg-green-50', 'text-green-700', 'border-green-200', 'hover:bg-green-100');
                testIcon.classList.add('hidden');
                testSpinner.classList.remove('hidden');
                testLabel.textContent = 'Testing...';
                testResultDiv.classList.add('hidden');

                const body = {};
                if (siteIdDirty) body.site_id = siteIdField.value;
                if (apiKeyDirty) body.api_key = apiKeyField.value;

                const regionSelect = document.querySelector('select[name="region"]');
                if (regionSelect) body.region = regionSelect.value;

                try {
                    const res = await fetch('{{ route("admin.settings.customerio.test") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(body),
                    });
                    const data = await res.json();

                    testResultDiv.classList.remove('hidden');
                    testMessage.textContent = data.message;
                    if (data.success) {
                        testMessage.className = 'p-3 rounded-lg text-sm font-medium bg-green-50 text-green-700';
                    } else {
                        testMessage.className = 'p-3 rounded-lg text-sm font-medium bg-red-50 text-red-700';
                    }
                } catch (e) {
                    testResultDiv.classList.remove('hidden');
                    testMessage.textContent = 'Network error: ' + e.message;
                    testMessage.className = 'p-3 rounded-lg text-sm font-medium bg-red-50 text-red-700';
                } finally {
                    testBtn.disabled = false;
                    testBtn.classList.remove('cursor-wait', 'bg-gray-50', 'text-gray-400', 'border-gray-200');
                    testBtn.classList.add('bg-green-50', 'text-green-700', 'border-green-200', 'hover:bg-green-100');
                    testIcon.classList.remove('hidden');
                    testSpinner.classList.add('hidden');
                    testLabel.textContent = 'Test Connection';
                }
            });
        });
    </script>
</x-admin-layout>
