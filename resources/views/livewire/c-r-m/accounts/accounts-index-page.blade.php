<div class="space-y-4">
    <div class="flex flex-col gap-3 rounded-2xl border border-stone-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-semibold text-stone-950">Accounts</h2>
            <p class="text-sm text-stone-600">Companies that deals and contacts roll up to.</p>
        </div>

        <input
            type="search"
            wire:model.live="search"
            placeholder="Search accounts"
            class="w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200 sm:max-w-xs"
        >
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-left text-sm">
                <thead class="bg-stone-50">
                    <tr>
                        <th class="px-4 py-3 font-medium text-stone-700 sm:px-6">Account</th>
                        <th class="px-4 py-3 font-medium text-stone-700 sm:px-6">Type</th>
                        <th class="px-4 py-3 font-medium text-stone-700 sm:px-6">Owner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($accounts as $account)
                        <tr wire:key="account-{{ $account->getKey() }}">
                            <td class="px-4 py-3 text-stone-950 sm:px-6">{{ $account->name }}</td>
                            <td class="px-4 py-3 text-stone-600 sm:px-6">{{ $account->type?->value ?? 'untyped' }}</td>
                            <td class="px-4 py-3 text-stone-600 sm:px-6">{{ $account->owner?->name ?? 'Unassigned' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-stone-500 sm:px-6">No accounts match the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
