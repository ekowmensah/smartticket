<x-platform-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-sky-600">Platform Audit</p>
                <h2 class="text-xl font-semibold text-slate-900">Audit Logs</h2>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Event</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Actor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Organization</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Entity</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Request</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Recorded</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($auditLogs as $auditLog)
                        <tr>
                            <td class="px-6 py-4 align-top">
                                <div class="font-semibold text-slate-900">{{ $auditLog->description }}</div>
                                <div class="mt-1 text-sm text-slate-500">{{ $auditLog->event }}</div>
                            </td>
                            <td class="px-6 py-4 align-top text-sm text-slate-700">
                                <div>{{ $auditLog->causer?->name ?? 'System' }}</div>
                                <div class="text-slate-500">{{ $auditLog->causer?->email ?? 'No actor email' }}</div>
                            </td>
                            <td class="px-6 py-4 align-top text-sm text-slate-700">
                                {{ $auditLog->organization?->name ?? 'Platform' }}
                            </td>
                            <td class="px-6 py-4 align-top text-sm text-slate-700">
                                <div>{{ class_basename($auditLog->subject_type ?? 'None') }}</div>
                                <div class="text-slate-500">#{{ $auditLog->subject_id ?? 'n/a' }}</div>
                            </td>
                            <td class="px-6 py-4 align-top text-sm text-slate-700">
                                <div>{{ $auditLog->request_id ?? 'Generated' }}</div>
                                <div class="text-slate-500">{{ $auditLog->ip_address ?? 'Unknown IP' }}</div>
                            </td>
                            <td class="px-6 py-4 align-top text-sm text-slate-700">
                                {{ $auditLog->created_at?->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                No audit records have been captured yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $auditLogs->links() }}
        </div>
    </div>
</x-platform-layout>
