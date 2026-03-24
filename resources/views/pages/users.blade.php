<x-layout.app>
    <main class="mx-auto flex min-h-screen w-full max-w-5xl flex-col gap-6 px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight text-stone-950">Users</h1>
            <p class="text-sm text-stone-600">Authenticated users can view the available demo credentials below.</p>
        </div>

        <livewire:users.users-table-page />
    </main>
</x-layout.app>
