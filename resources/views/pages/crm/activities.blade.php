<x-layout.app>
    <main class="mx-auto flex min-h-screen w-full max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight text-stone-950">Calendar & Scheduler</h1>
            <p class="text-sm text-stone-600">Plan internal meetings, demos, reminders, and follow-ups without relying on an external calendar service.</p>
        </div>

        <livewire:c-r-m.activities.activities-index-page />
    </main>
</x-layout.app>
