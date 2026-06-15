<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Accept Invitation - SmartCast Tickets</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100">
        <main class="mx-auto flex min-h-screen max-w-3xl items-center px-6 py-16">
            <div class="w-full rounded-[2rem] bg-white p-8 text-slate-900 shadow-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-600">Team Invitation</p>
                <h1 class="mt-4 text-3xl font-semibold">{{ $invitation->organization->name }}</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    {{ $invitation->inviter?->name ?? 'An organizer admin' }} invited <strong>{{ $invitation->email }}</strong> to join as <strong>{{ $invitation->role }}</strong>.
                </p>

                @if($authUser && $authUser->email !== $invitation->email)
                    <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        This invitation belongs to {{ $invitation->email }}. Sign in with that email address to accept it.
                    </div>
                @elseif(!$authUser && $existingUser)
                    <div class="mt-6 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">
                        An account already exists for {{ $invitation->email }}. Please sign in first, then reopen this invitation link to accept it.
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('login') }}" class="rounded-full bg-slate-950 px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Sign in
                        </a>
                    </div>
                @else
                    <form method="POST" action="{{ route('invitations.accept', ['token' => $token]) }}" class="mt-8 space-y-5">
                        @csrf

                        @guest
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700">Full name</label>
                                <input id="name" name="name" type="text" value="{{ old('name', $invitation->name) }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-slate-700">Phone number</label>
                                <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number') }}" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                                <input id="password" name="password" type="password" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="mt-2 block w-full rounded-2xl border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                            </div>
                        @else
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                Signed in as <strong>{{ $authUser->email }}</strong>. Accepting this invitation will add you to the organizer workspace.
                            </div>
                        @endguest

                        <button type="submit" class="rounded-full bg-amber-400 px-5 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                            Accept invitation
                        </button>
                    </form>
                @endif
            </div>
        </main>
    </body>
</html>
