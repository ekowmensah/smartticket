<x-platform-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-sky-600">Platform Console</p>
            <h2 class="text-xl font-semibold text-slate-900">Administration Dashboard</h2>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-3">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Organizations</p>
                <p class="mt-3 text-3xl font-semibold text-slate-950">{{ $organizationCount }}</p>
            </section>
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Pending Approval</p>
                <p class="mt-3 text-3xl font-semibold text-amber-600">{{ $pendingOrganizationCount }}</p>
            </section>
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Users</p>
                <p class="mt-3 text-3xl font-semibold text-slate-950">{{ $userCount }}</p>
            </section>
        </div>

        <div class="mt-8 grid gap-6 xl:grid-cols-2">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Organization review queue</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Review onboarding progress, approve organizers, suspend access, and inspect KYC submissions.
                        </p>
                    </div>
                    <a href="{{ route('platform.organizations.index') }}" class="rounded-full bg-slate-950 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Open organizations
                    </a>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Audit trail</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Inspect critical platform and organizer actions with actor, entity, and request context.
                        </p>
                    </div>
                    <a href="{{ route('platform.audit-logs.index') }}" class="rounded-full border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                        Open audit logs
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-platform-layout>
