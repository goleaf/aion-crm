<x-layout.app>
    <main class="mx-auto flex min-h-screen w-full max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight text-stone-950">Accounts</h1>
                <p class="text-sm text-stone-600">Track companies, parent organizations, owners, and commercial context in one registry.</p>
            </div>

            <nav class="flex items-center gap-3 text-sm">
                <a href="{{ route('crm.accounts.index') }}" class="rounded-full bg-stone-950 px-4 py-2 font-medium text-white">Accounts</a>
                <a href="{{ route('crm.contacts.index') }}" class="rounded-full border border-stone-300 px-4 py-2 font-medium text-stone-700">Contacts</a>
            </nav>
        </div>

        <livewire:crm.accounts.accounts-index-page />
    </main>
</x-layout.app>
