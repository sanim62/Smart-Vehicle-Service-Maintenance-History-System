<x-guest-layout>

    {{-- ── Portal Badge ─────────────────────────────── --}}
    @if(request()->get('role') === 'workshop')
        <div class="mb-5 text-center">
            <div class="inline-flex items-center justify-center bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 rounded-full px-4 py-2 font-bold text-sm gap-2 mb-2 border border-amber-200 dark:border-amber-800">
                <i class="bi bi-shop text-amber-600"></i> Workshop Partner Registration
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Register as a workshop partner — manage repair services, bookings, and payouts.</p>
        </div>
    @else
        <div class="mb-5 text-center">
            <div class="inline-flex items-center justify-center bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 rounded-full px-4 py-2 font-bold text-sm gap-2 mb-2 border border-emerald-200 dark:border-emerald-800">
                <i class="bi bi-person-plus-fill text-emerald-600"></i> Vehicle Owner Registration
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Create your account to register vehicles and book diagnostic services.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="hidden" name="role" value="{{ request()->get('role') === 'workshop' ? 'workshop' : 'owner' }}">

        {{-- ══════════════════════════════════════════
             SECTION 1 — Personal Information (both)
        ══════════════════════════════════════════ --}}
        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-3 mt-1">
            {{ request()->get('role') === 'workshop' ? '① Personal Information' : '① Your Details' }}
        </p>

        {{-- Full Name --}}
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                :value="old('name')" required autofocus autocomplete="name"
                placeholder="e.g. Ahmed Al-Rashidi" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- National ID --}}
        <div class="mt-4">
            <x-input-label for="national_id" :value="__('National ID Number')" />
            <x-text-input id="national_id" class="block mt-1 w-full" type="text" name="national_id"
                :value="old('national_id')" required autocomplete="off"
                placeholder="e.g. 1234567890" />
            <x-input-error :messages="$errors->get('national_id')" class="mt-2" />
        </div>

        {{-- Phone Number --}}
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone"
                :value="old('phone')" required autocomplete="tel"
                placeholder="e.g. +966 5x xxx xxxx" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                :value="old('email')" required autocomplete="username"
                placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                required autocomplete="new-password" placeholder="Min. 8 characters" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password"
                placeholder="Re-enter your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- ══════════════════════════════════════════
             SECTION 2 — Workshop Details (workshop only)
        ══════════════════════════════════════════ --}}
        @if(request()->get('role') === 'workshop')

            <div class="my-5 border-t border-gray-200 dark:border-gray-700"></div>

            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-3">
                ② Workshop Information
            </p>

            {{-- Workshop Name --}}
            <div>
                <x-input-label for="workshop_name" :value="__('Workshop Name')" />
                <x-text-input id="workshop_name" class="block mt-1 w-full" type="text" name="workshop_name"
                    :value="old('workshop_name')" required autocomplete="off"
                    placeholder="e.g. Al-Rashidi Auto Service" />
                <x-input-error :messages="$errors->get('workshop_name')" class="mt-2" />
            </div>

            {{-- Workshop License / ID --}}
            <div class="mt-4">
                <x-input-label for="workshop_license" :value="__('Workshop License / ID')" />
                <x-text-input id="workshop_license" class="block mt-1 w-full" type="text" name="workshop_license"
                    :value="old('workshop_license')" required autocomplete="off"
                    placeholder="e.g. WS-2024-00123" />
                <x-input-error :messages="$errors->get('workshop_license')" class="mt-2" />
            </div>

            {{-- Workshop Location --}}
            <div class="mt-4">
                <x-input-label for="workshop_location" :value="__('Workshop Address / Location')" />
                <x-text-input id="workshop_location" class="block mt-1 w-full" type="text" name="workshop_location"
                    :value="old('workshop_location')" required autocomplete="street-address"
                    placeholder="e.g. King Fahd Road, Riyadh" />
                <x-input-error :messages="$errors->get('workshop_location')" class="mt-2" />
            </div>

            {{-- Bank Account --}}
            <div class="mt-4">
                <x-input-label for="bank_account" :value="__('Bank Account (IBAN)')" />
                <x-text-input id="bank_account" class="block mt-1 w-full" type="text" name="bank_account"
                    :value="old('bank_account')" required autocomplete="off"
                    placeholder="e.g. SA00 0000 0000 0000 0000 0000" />
                <x-input-error :messages="$errors->get('bank_account')" class="mt-2" />
            </div>

        @endif
        {{-- end workshop section --}}

        {{-- ── Footer ──────────────────────────────── --}}
        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button>
                {{ request()->get('role') === 'workshop' ? 'Register Workshop' : 'Create Account' }}
            </x-primary-button>
        </div>

        {{-- Back to welcome --}}
        <div class="mt-4 text-center">
            <a href="{{ url('/') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <i class="bi bi-arrow-left me-1"></i> Back to Welcome Page
            </a>
        </div>
    </form>

</x-guest-layout>
