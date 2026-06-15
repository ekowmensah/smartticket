<x-platform-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-sky-600">Platform Settings</p>
                <h2 class="text-xl font-semibold text-slate-900">Commercial foundation settings</h2>
            </div>
            <a href="{{ route('platform.dashboard') }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                Back to dashboard
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Platform defaults</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    These settings shape the Phase 1 product identity and baseline communication details.
                </p>

                <form method="POST" action="{{ route('platform.settings.update') }}" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="product_name" class="block text-sm font-medium text-slate-700">Product name</label>
                        <input id="product_name" name="product_name" type="text" value="{{ old('product_name', $settings['product_name']) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        <x-input-error :messages="$errors->get('product_name')" class="mt-2" />
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="support_email" class="block text-sm font-medium text-slate-700">Support email</label>
                            <input id="support_email" name="support_email" type="email" value="{{ old('support_email', $settings['support_email']) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            <x-input-error :messages="$errors->get('support_email')" class="mt-2" />
                        </div>
                        <div>
                            <label for="support_phone" class="block text-sm font-medium text-slate-700">Support phone</label>
                            <input id="support_phone" name="support_phone" type="text" value="{{ old('support_phone', $settings['support_phone']) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            <x-input-error :messages="$errors->get('support_phone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-3">
                        <div>
                            <label for="currency_code" class="block text-sm font-medium text-slate-700">Currency</label>
                            <input id="currency_code" name="currency_code" type="text" value="{{ old('currency_code', $settings['currency_code']) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            <x-input-error :messages="$errors->get('currency_code')" class="mt-2" />
                        </div>
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-slate-700">Timezone</label>
                            <input id="timezone" name="timezone" type="text" value="{{ old('timezone', $settings['timezone']) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                            <x-input-error :messages="$errors->get('timezone')" class="mt-2" />
                        </div>
                        <div>
                            <label for="date_format" class="block text-sm font-medium text-slate-700">Date format</label>
                            <select id="date_format" name="date_format" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                                @foreach(['d M Y' => '15 Jun 2026', 'd/m/Y' => '15/06/2026', 'Y-m-d' => '2026-06-15'] as $formatValue => $formatLabel)
                                    <option value="{{ $formatValue }}" @selected(old('date_format', $settings['date_format']) === $formatValue)>{{ $formatLabel }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('date_format')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <label for="contact_address" class="block text-sm font-medium text-slate-700">Contact address</label>
                        <textarea id="contact_address" name="contact_address" rows="4" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">{{ old('contact_address', $settings['contact_address']) }}</textarea>
                        <x-input-error :messages="$errors->get('contact_address')" class="mt-2" />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            Save settings
                        </x-primary-button>
                    </div>
                </form>
            </section>

            <aside class="rounded-3xl bg-slate-950 p-6 text-slate-100 shadow-sm">
                <h3 class="text-lg font-semibold">Current public identity</h3>
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-slate-400">Product name</dt>
                        <dd class="mt-1 font-medium text-white">{{ $settings['product_name'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Support email</dt>
                        <dd class="mt-1 font-medium text-white">{{ $settings['support_email'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Support phone</dt>
                        <dd class="mt-1 font-medium text-white">{{ $settings['support_phone'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Currency and timezone</dt>
                        <dd class="mt-1 font-medium text-white">{{ $settings['currency_code'] }} • {{ $settings['timezone'] }}</dd>
                    </div>
                </dl>
            </aside>
        </div>
    </div>
</x-platform-layout>
