<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-900">Register your organizer account</h1>
            <p class="mt-2 text-sm text-slate-600">
                This Phase 1 flow creates an organizer owner account and a pending organization workspace.
            </p>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Your Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Your Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone_number" :value="__('Your Phone Number')" />
            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        <div class="mt-8 border-t border-slate-200 pt-6">
            <h2 class="text-lg font-semibold text-slate-900">Organization details</h2>
        </div>

        <div class="mt-4">
            <x-input-label for="organization_name" :value="__('Organization Name')" />
            <x-text-input id="organization_name" class="block mt-1 w-full" type="text" name="organization_name" :value="old('organization_name')" required autocomplete="organization" />
            <x-input-error :messages="$errors->get('organization_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="organization_type" :value="__('Organization Type')" />
            <select id="organization_type" name="organization_type" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" required>
                <option value="">Select type</option>
                <option value="business" @selected(old('organization_type') === 'business')>Business</option>
                <option value="individual" @selected(old('organization_type') === 'individual')>Individual</option>
            </select>
            <x-input-error :messages="$errors->get('organization_type')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="organization_email" :value="__('Organization Email')" />
            <x-text-input id="organization_email" class="block mt-1 w-full" type="email" name="organization_email" :value="old('organization_email')" autocomplete="email" />
            <x-input-error :messages="$errors->get('organization_email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="organization_phone" :value="__('Organization Phone')" />
            <x-text-input id="organization_phone" class="block mt-1 w-full" type="text" name="organization_phone" :value="old('organization_phone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('organization_phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-8 border-t border-slate-200 pt-6">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already have an account?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Create organizer workspace') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
