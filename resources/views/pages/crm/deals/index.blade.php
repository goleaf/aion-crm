<x-layout.app>
    <main class="mx-auto flex min-h-screen w-full max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight text-stone-950">Deals</h1>
                <p class="text-sm text-stone-600">Track every opportunity from prospecting through won or lost.</p>
            </div>

            <a href="{{ route('crm.deals.create') }}" class="inline-flex items-center justify-center rounded-xl bg-stone-950 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-stone-800">
                Create Deal
            </a>
        </div>

        <livewire:c-r-m.deals.deals-index-page />
    </main>
</x-layout.app>
