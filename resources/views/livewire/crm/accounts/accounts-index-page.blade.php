<div class="grid gap-6 lg:grid-cols-[minmax(0,1.5fr)_minmax(22rem,1fr)]">
    <section class="space-y-4">
        @if ($statusMessage)
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ $statusMessage }}
            </div>
        @endif

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="space-y-2 md:col-span-2">
                    <label for="account-search" class="block text-sm font-medium text-stone-700">Search accounts</label>
                    <input
                        id="account-search"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                        placeholder="Search by company, email, or phone"
                    >
                </div>

                <div class="space-y-2">
                    <label for="account-owner-filter" class="block text-sm font-medium text-stone-700">Owner</label>
                    <select
                        id="account-owner-filter"
                        wire:model.live="ownerFilter"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    >
                        <option value="">All owners</option>
                        @foreach ($owners as $ownerOption)
                            <option value="{{ $ownerOption->id }}">{{ $ownerOption->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-left text-sm">
                    <thead class="bg-stone-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Account</th>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Type</th>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Owner</th>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Parent</th>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Contact</th>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($accounts as $account)
                            <tr wire:key="account-row-{{ $account->id }}">
                                <td class="px-4 py-3 sm:px-6">
                                    <div class="font-medium text-stone-950">{{ $account->name }}</div>
                                    <div class="text-xs uppercase tracking-wide text-stone-500">{{ $account->industry->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-stone-700 sm:px-6">{{ $account->type->name }}</td>
                                <td class="px-4 py-3 text-stone-700 sm:px-6">{{ $account->owner?->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3 text-stone-700 sm:px-6">{{ $account->parentAccount?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-stone-700 sm:px-6">
                                    <div>{{ $account->email ?? '—' }}</div>
                                    <div class="text-xs text-stone-500">{{ $account->phone ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-right sm:px-6">
                                    <button
                                        type="button"
                                        wire:click="editAccount('{{ $account->id }}')"
                                        class="rounded-full border border-stone-300 px-3 py-1.5 text-xs font-medium text-stone-700 transition hover:border-stone-400 hover:text-stone-950"
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-stone-500 sm:px-6">No accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <div class="mb-5 space-y-1">
            <h2 class="text-lg font-semibold text-stone-950">{{ $editingAccountId ? 'Edit account' : 'Create account' }}</h2>
            <p class="text-sm text-stone-600">Capture commercial context, addresses, hierarchy, and ownership in one place.</p>
        </div>

        <form wire:submit="saveAccount" class="space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2 sm:col-span-2">
                    <label for="account-name" class="block text-sm font-medium text-stone-700">Name</label>
                    <input id="account-name" type="text" wire:model="form.name" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="account-industry" class="block text-sm font-medium text-stone-700">Industry</label>
                    <select id="account-industry" wire:model="form.industry" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                        @foreach ($industryOptions as $industryOption)
                            <option value="{{ $industryOption->value }}">{{ $industryOption->name }}</option>
                        @endforeach
                    </select>
                    @error('form.industry') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="account-type" class="block text-sm font-medium text-stone-700">Type</label>
                    <select id="account-type" wire:model="form.type" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                        @foreach ($typeOptions as $typeOption)
                            <option value="{{ $typeOption->value }}">{{ $typeOption->name }}</option>
                        @endforeach
                    </select>
                    @error('form.type') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="account-owner" class="block text-sm font-medium text-stone-700">Owner</label>
                    <select id="account-owner" wire:model="form.owner_id" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                        <option value="">Unassigned</option>
                        @foreach ($owners as $ownerOption)
                            <option value="{{ $ownerOption->id }}">{{ $ownerOption->name }}</option>
                        @endforeach
                    </select>
                    @error('form.owner_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="parent-account" class="block text-sm font-medium text-stone-700">Parent account</label>
                    <select id="parent-account" wire:model="form.parent_account_id" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                        <option value="">None</option>
                        @foreach ($parentAccounts as $parentAccount)
                            <option value="{{ $parentAccount->id }}">{{ $parentAccount->name }}</option>
                        @endforeach
                    </select>
                    @error('form.parent_account_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="account-email" class="block text-sm font-medium text-stone-700">Email</label>
                    <input id="account-email" type="email" wire:model="form.email" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="account-phone" class="block text-sm font-medium text-stone-700">Phone</label>
                    <input id="account-phone" type="text" wire:model="form.phone" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.phone') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <label for="account-website" class="block text-sm font-medium text-stone-700">Website</label>
                    <input id="account-website" type="url" wire:model="form.website" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.website') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="annual-revenue" class="block text-sm font-medium text-stone-700">Annual revenue</label>
                    <input id="annual-revenue" type="number" step="0.01" min="0" wire:model="form.annual_revenue" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.annual_revenue') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="employee-count" class="block text-sm font-medium text-stone-700">Employee count</label>
                    <input id="employee-count" type="number" min="0" wire:model="form.employee_count" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.employee_count') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label for="account-tags" class="block text-sm font-medium text-stone-700">Tags</label>
                <input id="account-tags" type="text" wire:model="form.tags_input" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="priority, enterprise, north-america">
                @error('form.tags_input') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-stone-200 p-4">
                    <h3 class="mb-3 text-sm font-semibold text-stone-900">Billing address</h3>
                    <div class="space-y-3">
                        <input type="text" wire:model="form.billing_address.line_1" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="Line 1">
                        <input type="text" wire:model="form.billing_address.line_2" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="Line 2">
                        <input type="text" wire:model="form.billing_address.city" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="City">
                        <input type="text" wire:model="form.billing_address.state_or_province" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="State / province">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <input type="text" wire:model="form.billing_address.postal_code" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="Postal code">
                            <input type="text" wire:model="form.billing_address.country_code" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="Country code">
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-stone-200 p-4">
                    <h3 class="mb-3 text-sm font-semibold text-stone-900">Shipping address</h3>
                    <div class="space-y-3">
                        <input type="text" wire:model="form.shipping_address.line_1" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="Line 1">
                        <input type="text" wire:model="form.shipping_address.line_2" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="Line 2">
                        <input type="text" wire:model="form.shipping_address.city" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="City">
                        <input type="text" wire:model="form.shipping_address.state_or_province" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="State / province">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <input type="text" wire:model="form.shipping_address.postal_code" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="Postal code">
                            <input type="text" wire:model="form.shipping_address.country_code" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200" placeholder="Country code">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-stone-950 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-300">
                    {{ $editingAccountId ? 'Update account' : 'Create account' }}
                </button>

                @if ($editingAccountId)
                    <button type="button" wire:click="cancelEditing" class="inline-flex items-center justify-center rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:border-stone-400 hover:text-stone-950">
                        Cancel
                    </button>
                @endif
            </div>
        </form>
    </section>
</div>
