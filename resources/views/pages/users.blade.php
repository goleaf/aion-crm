<x-layout.app>
    <main class="mx-auto flex min-h-screen w-full max-w-5xl flex-col gap-6 px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight text-stone-950">Users</h1>
                <p class="text-sm text-stone-600">You are signed in.</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-stone-950 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-300"
                >
                    Logout
                </button>
            </form>
        </div>
    </main>
</x-layout.app>
