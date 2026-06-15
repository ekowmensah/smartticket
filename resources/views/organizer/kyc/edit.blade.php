<x-organizer-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-600">Organizer KYC</p>
                <h2 class="text-xl font-semibold text-slate-900">{{ $organization->name }}</h2>
            </div>
            @if($submission)
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700">
                    {{ $submission->status->value }}
                </span>
            @endif
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('submission'))
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ $errors->first('submission') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Submission details</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Upload your organization verification details and required supporting documents. Files are stored privately for platform review.
                </p>

                @if($isLocked)
                    <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        This KYC submission is locked while it is under review or already approved.
                    </div>
                @endif

                <form method="POST" action="{{ route('organizer.kyc.update', $organization) }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="business_type" class="block text-sm font-medium text-slate-700">Business type</label>
                        <input id="business_type" name="business_type" type="text" value="{{ old('business_type', $submission?->business_type) }}" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                        <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="registration_number" class="block text-sm font-medium text-slate-700">Registration number</label>
                            <input id="registration_number" name="registration_number" type="text" value="{{ old('registration_number', $submission?->registration_number) }}" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                            <x-input-error :messages="$errors->get('registration_number')" class="mt-2" />
                        </div>
                        <div>
                            <label for="tax_identifier" class="block text-sm font-medium text-slate-700">Tax identifier</label>
                            <input id="tax_identifier" name="tax_identifier" type="text" value="{{ old('tax_identifier', $submission?->tax_identifier) }}" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                            <x-input-error :messages="$errors->get('tax_identifier')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <label for="legal_name" class="block text-sm font-medium text-slate-700">Legal name</label>
                        <input id="legal_name" name="legal_name" type="text" value="{{ old('legal_name', $submission?->legal_name ?? $organization->name) }}" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                        <x-input-error :messages="$errors->get('legal_name')" class="mt-2" />
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="contact_name" class="block text-sm font-medium text-slate-700">Contact name</label>
                            <input id="contact_name" name="contact_name" type="text" value="{{ old('contact_name', $submission?->contact_name) }}" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                            <x-input-error :messages="$errors->get('contact_name')" class="mt-2" />
                        </div>
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-slate-700">Contact email</label>
                            <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $submission?->contact_email ?? $organization->public_email) }}" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                            <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-slate-700">Contact phone</label>
                            <input id="contact_phone" name="contact_phone" type="text" value="{{ old('contact_phone', $submission?->contact_phone ?? $organization->public_phone) }}" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                            <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-3">
                        <div>
                            <label for="payout_method" class="block text-sm font-medium text-slate-700">Payout method</label>
                            <select id="payout_method" name="payout_method" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                                <option value="">Select</option>
                                <option value="bank_transfer" @selected(old('payout_method', $submission?->payout_method) === 'bank_transfer')>Bank transfer</option>
                                <option value="mobile_money" @selected(old('payout_method', $submission?->payout_method) === 'mobile_money')>Mobile money</option>
                            </select>
                            <x-input-error :messages="$errors->get('payout_method')" class="mt-2" />
                        </div>
                        <div>
                            <label for="payout_account_name" class="block text-sm font-medium text-slate-700">Payout account name</label>
                            <input id="payout_account_name" name="payout_account_name" type="text" value="{{ old('payout_account_name', $submission?->payout_account_name) }}" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                            <x-input-error :messages="$errors->get('payout_account_name')" class="mt-2" />
                        </div>
                        <div>
                            <label for="payout_account_number" class="block text-sm font-medium text-slate-700">Payout account number</label>
                            <input id="payout_account_number" name="payout_account_number" type="text" value="{{ old('payout_account_number', $submission?->payout_account_number) }}" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                            <x-input-error :messages="$errors->get('payout_account_number')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <label for="payout_provider" class="block text-sm font-medium text-slate-700">Payout provider</label>
                        <input id="payout_provider" name="payout_provider" type="text" value="{{ old('payout_provider', $submission?->payout_provider) }}" @disabled($isLocked) class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-slate-100">
                        <x-input-error :messages="$errors->get('payout_provider')" class="mt-2" />
                    </div>

                    <div class="rounded-2xl border border-slate-200 p-4">
                        <h4 class="text-sm font-semibold text-slate-900">Required documents</h4>
                        <div class="mt-4 space-y-4">
                            @foreach ([
                                'registration_certificate' => 'Registration certificate',
                                'government_id' => 'Government ID',
                                'bank_or_momo_proof' => 'Bank or Mobile Money proof',
                            ] as $documentType => $documentLabel)
                                <div class="grid gap-3 md:grid-cols-[1fr_2fr] md:items-center">
                                    <input type="hidden" name="documents[{{ $loop->index }}][type]" value="{{ $documentType }}">
                                    <div class="text-sm font-medium text-slate-700">{{ $documentLabel }}</div>
                                    <div>
                                        <input type="file" name="documents[{{ $loop->index }}][file]" @disabled($isLocked) class="block w-full text-sm text-slate-700 file:mr-4 file:rounded-full file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white disabled:bg-slate-100">
                                        <x-input-error :messages="$errors->get('documents.'.$loop->index.'.file')" class="mt-2" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @unless($isLocked)
                        <div class="flex justify-end">
                            <x-primary-button>
                                Submit KYC for review
                            </x-primary-button>
                        </div>
                    @endunless
                </form>
            </section>

            <aside class="rounded-3xl bg-slate-950 p-6 text-slate-100 shadow-sm">
                <h3 class="text-lg font-semibold">Review status</h3>
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-slate-400">Current status</dt>
                        <dd class="mt-1 font-medium text-white">{{ $submission?->status->value ?? 'not_submitted' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Submitted at</dt>
                        <dd class="mt-1 font-medium text-white">{{ $submission?->submitted_at?->format('M d, Y H:i') ?? 'Not yet submitted' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Last review note</dt>
                        <dd class="mt-1 font-medium text-white">{{ $submission?->rejection_reason ?? 'No review notes yet' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Uploaded files</dt>
                        <dd class="mt-2 space-y-2">
                            @forelse($submission?->documents ?? [] as $document)
                                <div class="rounded-2xl bg-slate-900/60 px-3 py-2 text-xs text-slate-200">
                                    {{ $document->document_type }}: {{ $document->original_name }}
                                </div>
                            @empty
                                <p class="text-slate-400">No documents uploaded yet.</p>
                            @endforelse
                        </dd>
                    </div>
                </dl>
            </aside>
        </div>
    </div>
</x-organizer-layout>
