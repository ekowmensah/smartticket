<x-organizer-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-600">Organizer Workspace</p>
                <h2 class="text-xl font-semibold text-slate-900">{{ $organization->name }}</h2>
            </div>
            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700">
                {{ $organization->approval_status->value }}
            </span>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <h3 class="text-lg font-semibold text-slate-900">Phase 1 Organizer Access</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Your organization account has been created. This workspace is active for onboarding, team setup, and KYC preparation while platform approval is pending.
                </p>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Organization Status</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $organization->status->value }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Your Membership</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $membership?->status?->value ?? 'unknown' }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Contact Email</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $organization->public_email ?? 'Not set' }}</dd>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Contact Phone</dt>
                        <dd class="mt-2 text-base font-semibold text-slate-900">{{ $organization->public_phone ?? 'Not set' }}</dd>
                    </div>
                </dl>

                <div class="mt-8 rounded-3xl border border-amber-200 bg-amber-50 p-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h4 class="text-base font-semibold text-amber-950">Know Your Customer submission</h4>
                            <p class="mt-2 text-sm leading-6 text-amber-900">
                                Submit your organization KYC details and supporting documents for platform review.
                            </p>
                        </div>
                        <a href="{{ route('organizer.kyc.edit', $organization) }}" class="rounded-full bg-slate-950 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Open KYC workspace
                        </a>
                    </div>
                </div>

                <div class="mt-5 rounded-3xl border border-sky-200 bg-sky-50 p-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h4 class="text-base font-semibold text-sky-950">Team workspace</h4>
                            <p class="mt-2 text-sm leading-6 text-sky-900">
                                Invite teammates, review active memberships, and control who can access this organization workspace.
                            </p>
                        </div>
                        <a href="{{ route('organizer.team.index', $organization) }}" class="rounded-full bg-slate-950 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Open team workspace
                        </a>
                    </div>
                </div>
            </section>

            <aside class="rounded-3xl bg-slate-950 p-6 text-slate-100 shadow-sm">
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-300">Next In Phase 1</p>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-300">
                    <li>Platform approval workflow</li>
                    <li>Organizer KYC submission and review</li>
                    <li>Team invitation and role management</li>
                    <li>Cross-tenant authorization coverage</li>
                </ul>
            </aside>
        </div>
    </div>
</x-organizer-layout>
