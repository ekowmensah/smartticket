<x-app-layout>
    @isset($header)
        <x-slot name="header">
            {{ $header }}
        </x-slot>
    @endisset

    <div class="border-b border-amber-200 bg-amber-50">
        <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-700">
                Organizer Workspace
            </p>
        </div>
    </div>

    {{ $slot }}
</x-app-layout>
