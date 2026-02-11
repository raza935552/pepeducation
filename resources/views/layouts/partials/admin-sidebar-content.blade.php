<div class="flex h-16 shrink-0 items-center px-6 lg:px-0">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
        <span class="text-xl font-bold text-primary-600">Pep</span>
        <span class="text-xl font-bold text-gray-900">Profesor</span>
    </a>
</div>

<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                {{-- Dashboard --}}
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="{{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Dashboard
                    </a>
                </li>

                {{-- Analytics --}}
                <li>
                    <a href="{{ route('admin.analytics') }}"
                       class="{{ request()->routeIs('admin.analytics') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        Analytics
                    </a>
                </li>

                {{-- Peptides --}}
                <li>
                    <a href="{{ route('admin.peptides.index') }}"
                       class="{{ request()->routeIs('admin.peptides.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611l-.772.129a9.003 9.003 0 01-5.363-.172c-3.27-1.046-6.72-1.046-9.99 0-.48.154-.975.268-1.48.337l-.772.129c-1.717.293-2.299-2.379-1.067-3.611L5 14.5" />
                        </svg>
                        Peptides
                    </a>
                </li>

                {{-- Categories --}}
                <li>
                    <a href="{{ route('admin.categories.index') }}"
                       class="{{ request()->routeIs('admin.categories.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                        Categories
                    </a>
                </li>

                {{-- Users --}}
                <li>
                    <a href="{{ route('admin.users.index') }}"
                       class="{{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Users
                    </a>
                </li>
            </ul>
        </li>

        {{-- Community section --}}
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wider">Community</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                {{-- Contributions --}}
                <li>
                    <a href="{{ route('admin.contributions.index') }}"
                       class="{{ request()->routeIs('admin.contributions.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Contributions
                        @php $pendingCount = Cache::remember('admin_pending_contributions', 120, fn() => \App\Models\Contribution::where('status', 'pending')->count()); @endphp
                        @if($pendingCount > 0)
                            <span class="ml-auto bg-gold-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>

                {{-- Messages --}}
                <li>
                    <a href="{{ route('admin.messages.index') }}"
                       class="{{ request()->routeIs('admin.messages.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        Messages
                        @php $newCount = Cache::remember('admin_new_messages', 120, fn() => \App\Models\ContactMessage::where('status', 'new')->count()); @endphp
                        @if($newCount > 0)
                            <span class="ml-auto bg-blue-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $newCount }}</span>
                        @endif
                    </a>
                </li>

                {{-- Peptide Requests --}}
                <li>
                    <a href="{{ route('admin.requests.index') }}"
                       class="{{ request()->routeIs('admin.requests.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Requests
                        @php $reqCount = Cache::remember('admin_pending_requests', 120, fn() => \App\Models\PeptideRequest::where('status', 'pending')->count()); @endphp
                        @if($reqCount > 0)
                            <span class="ml-auto bg-yellow-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $reqCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </li>

        {{-- Content section --}}
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wider">Content</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                {{-- Blog Posts --}}
                <li>
                    <a href="{{ route('admin.blog-posts.index') }}"
                       class="{{ request()->routeIs('admin.blog-posts.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                        </svg>
                        Blog Posts
                    </a>
                </li>

                {{-- Blog Categories --}}
                <li>
                    <a href="{{ route('admin.blog-categories.index') }}"
                       class="{{ request()->routeIs('admin.blog-categories.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                        Blog Categories
                    </a>
                </li>
            </ul>
        </li>

        {{-- Marketing section --}}
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wider">Marketing</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                {{-- Subscribers --}}
                <li>
                    <a href="{{ route('admin.subscribers.index') }}"
                       class="{{ request()->routeIs('admin.subscribers.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        Subscribers
                        @php $subCount = Cache::remember('admin_active_subscribers', 120, fn() => \App\Models\Subscriber::active()->count()); @endphp
                        @if($subCount > 0)
                            <span class="ml-auto bg-emerald-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $subCount }}</span>
                        @endif
                    </a>
                </li>

                {{-- Pages --}}
                <li>
                    <a href="{{ route('admin.pages.index') }}"
                       class="{{ request()->routeIs('admin.pages.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Pages
                    </a>
                </li>

                {{-- Supporters --}}
                <li>
                    <a href="{{ route('admin.supporters.index') }}"
                       class="{{ request()->routeIs('admin.supporters.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                        Supporters
                    </a>
                </li>

                {{-- Quizzes --}}
                <li>
                    <a href="{{ route('admin.quizzes.index') }}"
                       class="{{ request()->routeIs('admin.quizzes.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                        </svg>
                        Quizzes
                    </a>
                </li>

                {{-- Popups --}}
                <li>
                    <a href="{{ route('admin.popups.index') }}"
                       class="{{ request()->routeIs('admin.popups.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                        Popups
                    </a>
                </li>

                {{-- Lead Magnets --}}
                <li>
                    <a href="{{ route('admin.lead-magnets.index') }}"
                       class="{{ request()->routeIs('admin.lead-magnets.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Lead Magnets
                    </a>
                </li>

                {{-- Outbound Links --}}
                <li>
                    <a href="{{ route('admin.outbound-links.index') }}"
                       class="{{ request()->routeIs('admin.outbound-links.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        Outbound Links
                    </a>
                </li>
            </ul>
        </li>

        {{-- Stack Builder section --}}
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wider">Stack Builder</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                <li>
                    <a href="{{ route('admin.stack-goals.index') }}"
                       class="{{ request()->routeIs('admin.stack-goals.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" />
                        </svg>
                        Stack Goals
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.stack-products.index') }}"
                       class="{{ request()->routeIs('admin.stack-products.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        Stack Products
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.stack-bundles.index') }}"
                       class="{{ request()->routeIs('admin.stack-bundles.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L12 12.75l-5.571-3m11.142 0l4.179 2.25L12 17.25l-9.75-5.25 4.179-2.25m11.142 0l4.179 2.25L12 22.5l-9.75-5.25 4.179-2.25" />
                        </svg>
                        Stack Bundles
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.stack-stores.index') }}"
                       class="{{ request()->routeIs('admin.stack-stores.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                        </svg>
                        Stack Stores
                    </a>
                </li>
            </ul>
        </li>

        {{-- Settings section --}}
        <li>
            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wider">System</div>
            <ul role="list" class="-mx-2 mt-2 space-y-1">
                {{-- Settings --}}
                <li>
                    <a href="{{ route('admin.settings.index') }}"
                       class="{{ request()->routeIs('admin.settings.*') ? 'bg-gray-100 text-primary-600' : 'text-gray-700 hover:bg-gray-100' }} group flex gap-x-3 rounded-lg p-2 text-sm font-semibold leading-6 transition-colors">
                        <svg aria-hidden="true" class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</nav>
