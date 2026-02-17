<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                @if($user->is_suspended)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Suspended
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                    Edit User
                </a>
            </div>
        </div>
    </x-slot>


    <div class="grid lg:grid-cols-3 gap-6">
        {{-- User Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Profile Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                        <span class="text-xl font-bold text-primary-600">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <dl class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">Name</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Email</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Role</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Joined</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                            </div>
                            @if($user->bio)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm text-gray-500">Bio</dt>
                                    <dd class="text-sm text-gray-900">{{ $user->bio }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Bookmarks --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Bookmarks ({{ $user->bookmarks->count() }})
                </h2>
                @if($user->bookmarks->count() > 0)
                    <div class="space-y-2">
                        @foreach($user->bookmarks as $bookmark)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-mono bg-primary-100 text-primary-700 px-2 py-1 rounded">
                                        {{ $bookmark->peptide->abbreviation ?? 'N/A' }}
                                    </span>
                                    <span class="text-sm text-gray-900">{{ $bookmark->peptide->name }}</span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ $bookmark->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No bookmarks yet.</p>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Quick Stats --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h2>
                <dl class="space-y-4">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Bookmarks</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $user->bookmarks->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Email Verified</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ $user->email_verified_at ? 'Yes' : 'No' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Last Updated</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $user->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                <div class="space-y-3">
                    @if(!$user->isAdmin())
                        <form action="{{ route('admin.users.suspend', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $user->is_suspended ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-amber-600 hover:bg-amber-700 text-white' }}">
                                {{ $user->is_suspended ? 'Unsuspend User' : 'Suspend User' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 text-sm font-medium bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                Delete User
                            </button>
                        </form>
                    @else
                        <p class="text-sm text-gray-500">Admin users cannot be suspended or deleted.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
