<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-cream-100">Welcome back</h1>
        <p class="text-sm text-gray-600 dark:text-cream-400 mt-1">Sign in to your account</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-cream-300 dark:border-brown-600 text-gold-500 shadow-sm focus:ring-gold-500 dark:bg-brown-700" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-cream-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Sign In') }}
            </x-primary-button>
        </div>

        <div class="flex items-center justify-center mt-4">
            @if (Route::has('password.request'))
                <a class="text-sm text-gold-600 dark:text-gold-400 hover:text-gold-700 dark:hover:text-gold-300 hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        @if (Route::has('register'))
            <div class="text-center mt-6 pt-6 border-t border-cream-200 dark:border-brown-700">
                <p class="text-sm text-gray-600 dark:text-cream-400">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-gold-600 dark:text-gold-400 hover:text-gold-700 dark:hover:text-gold-300 font-medium hover:underline">
                        Sign up
                    </a>
                </p>
            </div>
        @endif
    </form>
</x-guest-layout>
