<x-organizer-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-600">Organizer Team</p>
                <h2 class="text-xl font-semibold text-slate-900">{{ $organization->name }}</h2>
            </div>
            <a href="{{ route('organizer.dashboard', $organization) }}" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400">
                Back to dashboard
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if (session('invitation_link'))
            <div class="mb-6 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                Invitation link:
                <a href="{{ session('invitation_link') }}" class="font-semibold underline">{{ session('invitation_link') }}</a>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Active team members</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-700">
                        {{ $organization->memberships->count() }} members
                    </span>
                </div>

                <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Member</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Role</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($organization->memberships->sortBy('created_at') as $membership)
                                <tr>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-900">{{ $membership->user?->name ?? 'Unknown user' }}</div>
                                        <div class="text-sm text-slate-500">{{ $membership->user?->email }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $membership->role }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $membership->status->value }}</td>
                                    <td class="px-5 py-4 text-sm text-slate-700">{{ $membership->joined_at?->format('M d, Y') ?? 'Pending' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    <h4 class="text-base font-semibold text-slate-900">Pending invitations</h4>
                    <div class="mt-4 space-y-3">
                        @forelse($organization->invitations->whereNull('accepted_at')->sortByDesc('created_at') as $invitation)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $invitation->email }}</p>
                                        <p class="text-sm text-slate-500">{{ $invitation->role }} • invited by {{ $invitation->inviter?->name ?? 'Unknown' }}</p>
                                    </div>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Expires {{ $invitation->expires_at?->format('M d, Y') ?? 'never' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No pending invitations yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <aside class="rounded-3xl bg-slate-950 p-6 text-slate-100 shadow-sm">
                <h3 class="text-lg font-semibold">Invite teammate</h3>

                @if($canManageTeam)
                    <form method="POST" action="{{ route('organizer.team.store', $organization) }}" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-300">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" class="mt-2 block w-full rounded-2xl border-slate-700 bg-slate-900 text-slate-100 shadow-sm focus:border-amber-400 focus:ring-amber-400">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-300">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-2 block w-full rounded-2xl border-slate-700 bg-slate-900 text-slate-100 shadow-sm focus:border-amber-400 focus:ring-amber-400">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-medium text-slate-300">Role</label>
                            <select id="role" name="role" class="mt-2 block w-full rounded-2xl border-slate-700 bg-slate-900 text-slate-100 shadow-sm focus:border-amber-400 focus:ring-amber-400">
                                <option value="">Select role</option>
                                @foreach($assignableRoles as $roleValue => $roleLabel)
                                    <option value="{{ $roleValue }}" @selected(old('role') === $roleValue)>{{ $roleLabel }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                        <button type="submit" class="w-full rounded-full bg-amber-400 px-5 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                            Create invitation
                        </button>
                    </form>
                @else
                    <p class="mt-5 text-sm leading-6 text-slate-300">
                        You can view team members, but only users with the `team.manage` permission can invite new teammates.
                    </p>
                @endif
            </aside>
        </div>
    </div>
</x-organizer-layout>
