<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $platformSettings['product_name'] ?? 'SmartCast Tickets' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100">
        <main class="mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-16 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-amber-300">{{ $platformSettings['product_name'] ?? 'SmartCast Tickets' }}</p>
                <h1 class="mt-6 text-4xl font-semibold tracking-tight text-white sm:text-6xl">
                    Multi-tenant ticketing foundation for organizers, approvals, and team onboarding.
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
                    Phase 1 is focused on organizer registration, platform administration, KYC readiness, and tenancy-safe access controls before payments, ticketing, or scanners are introduced.
                </p>
                <p class="mt-6 text-sm leading-6 text-slate-400">
                    Support: {{ $platformSettings['support_email'] ?? 'support@smartcast.test' }} • {{ $platformSettings['support_phone'] ?? '+233200000999' }}
                </p>
            </div>

            <div class="mt-10 flex flex-wrap gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full bg-amber-400 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                        Open dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="rounded-full bg-amber-400 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                        Register as organizer
                    </a>
                    <a href="{{ route('login') }}" class="rounded-full border border-slate-700 px-6 py-3 text-sm font-semibold text-slate-100 transition hover:border-slate-500">
                        Sign in
                    </a>
                @endauth
            </div>
        </main>
    </body>
</html>
