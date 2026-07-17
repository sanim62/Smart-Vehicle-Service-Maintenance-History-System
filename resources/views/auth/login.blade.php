<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if(request()->get('role') === 'admin')
        <div class="mb-4 text-center">
            <div class="inline-flex items-center justify-center bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full px-3.5 py-1.5 font-semibold text-sm gap-2 mb-2 border border-blue-200 dark:border-blue-800">
                <i class="bi bi-shield-lock-fill"></i> Admin Portal
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Please authenticate with your administrator credentials.</p>
        </div>
    @elseif(request()->get('role') === 'workshop')
        <div class="mb-4 text-center">
            <div class="inline-flex items-center justify-center bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 rounded-full px-3.5 py-1.5 font-semibold text-sm gap-2 mb-2 border border-amber-200 dark:border-amber-800">
                <i class="bi bi-shop text-amber-600"></i> Workshop Partner Portal
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Sign in to manage your workshop configurations, bookings, and parts.</p>
        </div>
    @else
        <div class="mb-4 text-center">
            <div class="inline-flex items-center justify-center bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 rounded-full px-3.5 py-1.5 font-semibold text-sm gap-2 mb-2 border border-emerald-200 dark:border-emerald-800">
                <i class="bi bi-people-fill text-emerald-600"></i> Vehicle Owner Portal
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Sign in to manage your vehicles, book diagnostics, and pay invoices.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login', ['role' => request()->get('role')]) }}">
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
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
