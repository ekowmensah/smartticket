<x-platform-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-sky-600">Organization Review</p>
                <h2 class="text-xl font-semibold text-slate-900">{{ $organization->name }}</h2>
            </div>
            <a href="{{ route('platform.organizations.index') }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                Back to organizations
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Organization summary</h3>
                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Approval status</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $organization->approval_status->value }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Organization status</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $organization->status->value }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Public email</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $organization->public_email ?? 'Not set' }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Public phone</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $organization->public_phone ?? 'Not set' }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Created by</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $organization->creator?->name ?? 'Unknown' }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Approved by</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $organization->approver?->name ?? 'Not approved yet' }}</dd>
                    </div>
                </dl>

                <div class="mt-8 grid gap-6 lg:grid-cols-2">
                    <form method="POST" action="{{ route('platform.organizations.review', $organization) }}" class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="approve">
                        <h4 class="text-base font-semibold text-emerald-900">Approve organizer</h4>
                        <p class="mt-2 text-sm text-emerald-800">
                            Marks the organization as active and approved for platform-managed onboarding.
                        </p>
                        <button type="submit" class="mt-5 rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                            Approve organization
                        </button>
                    </form>

                    <form method="POST" action="{{ route('platform.organizations.review', $organization) }}" class="rounded-3xl border border-rose-200 bg-rose-50 p-5">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="suspend">
                        <h4 class="text-base font-semibold text-rose-900">Suspend organizer</h4>
                        <p class="mt-2 text-sm text-rose-800">
                            Suspended organizers are blocked from protected organizer routes until reviewed again.
                        </p>
                        <label for="reason" class="mt-5 block text-sm font-medium text-rose-900">Suspension reason</label>
                        <textarea id="reason" name="reason" rows="4" class="mt-2 block w-full rounded-2xl border-rose-200 shadow-sm focus:border-rose-400 focus:ring-rose-400">{{ old('reason', $organization->suspension_reason) }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        <button type="submit" class="mt-5 rounded-full bg-rose-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-rose-500">
                            Suspend organization
                        </button>
                    </form>
                </div>
            </section>

            <aside class="rounded-3xl bg-slate-950 p-6 text-slate-100 shadow-sm">
                <h3 class="text-lg font-semibold">KYC review</h3>

                @if($latestKycSubmission)
                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="text-slate-400">Status</dt>
                            <dd class="mt-1 font-medium text-white">{{ $latestKycSubmission->status->value }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Legal name</dt>
                            <dd class="mt-1 font-medium text-white">{{ $latestKycSubmission->legal_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Payout method</dt>
                            <dd class="mt-1 font-medium text-white">{{ $latestKycSubmission->payout_method }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Documents</dt>
                            <dd class="mt-2 space-y-2">
                                @foreach($latestKycSubmission->documents as $document)
                                    <div class="rounded-2xl bg-slate-900/60 px-3 py-2 text-xs text-slate-200">
                                        {{ $document->document_type }}: {{ $document->original_name }}
                                    </div>
                                @endforeach
                            </dd>
                        </div>
                    </dl>

                    <form method="POST" action="{{ route('platform.organizations.kyc.review', $organization) }}" class="mt-6 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="submit" name="action" value="approve" class="rounded-full bg-emerald-500 px-5 py-2 text-sm font-semibold text-slate-950 transition hover:bg-emerald-400">
                                Approve KYC
                            </button>
                            <button type="submit" name="action" value="reject" class="rounded-full bg-amber-400 px-5 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                                Reject KYC
                            </button>
                        </div>

                        <div>
                            <label for="rejection_reason" class="block text-sm font-medium text-slate-300">Review note</label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="4" class="mt-2 block w-full rounded-2xl border-slate-700 bg-slate-900 text-slate-100 shadow-sm focus:border-amber-400 focus:ring-amber-400">{{ old('rejection_reason', $latestKycSubmission->rejection_reason) }}</textarea>
                            <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />
                        </div>
                    </form>
                @else
                    <p class="mt-5 text-sm leading-6 text-slate-300">
                        No KYC submission has been received yet for this organization.
                    </p>
                @endif
            </aside>
        </div>
    </div>
</x-platform-layout>
