<x-platform-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-sky-600">Platform Console</p>
                <h2 class="text-xl font-semibold text-slate-900">Organizations</h2>
            </div>
            <a href="{{ route('platform.dashboard') }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                Back to dashboard
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Organization</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Approval</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">KYC</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Owner</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($organizations as $organization)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">{{ $organization->name }}</div>
                                <div class="text-sm text-slate-500">{{ $organization->slug }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                <div>{{ $organization->approval_status->value }}</div>
                                <div class="text-slate-500">{{ $organization->status->value }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $organization->latestKycSubmission?->status->value ?? 'not_submitted' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $organization->creator?->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('platform.organizations.show', $organization) }}" class="text-sm font-semibold text-sky-700 hover:text-sky-800">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-platform-layout>
