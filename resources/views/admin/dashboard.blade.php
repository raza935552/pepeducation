<x-admin-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Peptides --}}
        <div class="card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900">
                        <svg class="h-6 w-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611l-.772.129a9.003 9.003 0 01-5.363-.172c-3.27-1.046-6.72-1.046-9.99 0-.48.154-.975.268-1.48.337l-.772.129c-1.717.293-2.299-2.379-1.067-3.611L5 14.5" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Peptides</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">0</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Users --}}
        <div class="card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-100 dark:bg-secondary-900">
                        <svg class="h-6 w-6 text-secondary-600 dark:text-secondary-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Users</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ \App\Models\User::count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Subscribers --}}
        <div class="card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Subscribers</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">0</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Page Views --}}
        <div class="card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100 dark:bg-yellow-900">
                        <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Page Views</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">0</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Welcome message --}}
    <div class="mt-8 card p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Welcome to PepProfesor Admin!</h2>
        <p class="text-gray-600 dark:text-gray-400">
            This is your admin dashboard. As we build out more features, you'll be able to manage peptides, users, subscribers, and more from here.
        </p>
        <div class="mt-4 flex gap-3">
            <span class="inline-flex items-center rounded-full bg-primary-100 dark:bg-primary-900 px-3 py-0.5 text-sm font-medium text-primary-800 dark:text-primary-200">
                Phase 1 Complete
            </span>
            <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-0.5 text-sm font-medium text-gray-800 dark:text-gray-200">
                Next: Peptides Module
            </span>
        </div>
    </div>
</x-admin-layout>
