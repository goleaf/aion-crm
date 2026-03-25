<x-layout.app>
    <main class="mx-auto flex min-h-screen w-full max-w-5xl flex-col items-center justify-center gap-8 px-4 py-10 sm:px-6">
        <div class="w-full max-w-md">
            <livewire:auth.login-page />
        </div>

        <section class="w-full max-w-3xl space-y-4">
            <div class="space-y-1 text-center sm:text-left">
                <h2 class="text-xl font-semibold tracking-tight text-stone-950">Demo credentials</h2>
                <p class="text-sm text-stone-600">Use any of the seeded usernames and passwords below to sign in.</p>
            </div>

            <livewire:users.users-table-page />
        </section>
    </main>
</x-layout.app>
