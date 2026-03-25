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
                    <label for="contact-search" class="block text-sm font-medium text-stone-700">Search contacts</label>
                    <input
                        id="contact-search"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                        placeholder="Search by person, email, or phone"
                    >
                </div>

                <div class="space-y-2">
                    <label for="contact-owner-filter" class="block text-sm font-medium text-stone-700">Owner</label>
                    <select
                        id="contact-owner-filter"
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
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Contact</th>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Company</th>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Source</th>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Owner</th>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Channel</th>
                            <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($contacts as $contact)
                            <tr wire:key="contact-row-{{ $contact->id }}">
                                <td class="px-4 py-3 sm:px-6">
                                    <div class="font-medium text-stone-950">{{ $contact->first_name }} {{ $contact->last_name }}</div>
                                    <div class="text-xs text-stone-500">{{ $contact->email ?? $contact->phone ?? 'No contact method' }}</div>
                                </td>
                                <td class="px-4 py-3 text-stone-700 sm:px-6">{{ $contact->account?->name ?? 'Independent' }}</td>
                                <td class="px-4 py-3 text-stone-700 sm:px-6">{{ $contact->lead_source->name }}</td>
                                <td class="px-4 py-3 text-stone-700 sm:px-6">{{ $contact->owner?->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3 text-stone-700 sm:px-6">{{ $contact->preferred_channel->name }}</td>
                                <td class="px-4 py-3 text-right sm:px-6">
                                    <button
                                        type="button"
                                        wire:click="editContact('{{ $contact->id }}')"
                                        class="rounded-full border border-stone-300 px-3 py-1.5 text-xs font-medium text-stone-700 transition hover:border-stone-400 hover:text-stone-950"
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-stone-500 sm:px-6">No contacts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <div class="mb-5 space-y-1">
            <h2 class="text-lg font-semibold text-stone-950">{{ $editingContactId ? 'Edit contact' : 'Create contact' }}</h2>
            <p class="text-sm text-stone-600">Capture person records, account links, ownership, and communication preferences.</p>
        </div>

        <form wire:submit="saveContact" class="space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="contact-first-name" class="block text-sm font-medium text-stone-700">First name</label>
                    <input id="contact-first-name" type="text" wire:model="form.first_name" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.first_name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-last-name" class="block text-sm font-medium text-stone-700">Last name</label>
                    <input id="contact-last-name" type="text" wire:model="form.last_name" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.last_name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-account" class="block text-sm font-medium text-stone-700">Account</label>
                    <select id="contact-account" wire:model="form.account_id" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                        <option value="">Independent contact</option>
                        @foreach ($accounts as $accountOption)
                            <option value="{{ $accountOption->id }}">{{ $accountOption->name }}</option>
                        @endforeach
                    </select>
                    @error('form.account_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-owner" class="block text-sm font-medium text-stone-700">Owner</label>
                    <select id="contact-owner" wire:model="form.owner_id" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                        <option value="">Unassigned</option>
                        @foreach ($owners as $ownerOption)
                            <option value="{{ $ownerOption->id }}">{{ $ownerOption->name }}</option>
                        @endforeach
                    </select>
                    @error('form.owner_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-email" class="block text-sm font-medium text-stone-700">Email</label>
                    <input id="contact-email" type="email" wire:model="form.email" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-phone" class="block text-sm font-medium text-stone-700">Phone</label>
                    <input id="contact-phone" type="text" wire:model="form.phone" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.phone') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-mobile" class="block text-sm font-medium text-stone-700">Mobile</label>
                    <input id="contact-mobile" type="text" wire:model="form.mobile" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.mobile') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-job-title" class="block text-sm font-medium text-stone-700">Job title</label>
                    <input id="contact-job-title" type="text" wire:model="form.job_title" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.job_title') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-department" class="block text-sm font-medium text-stone-700">Department</label>
                    <input id="contact-department" type="text" wire:model="form.department" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.department') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-lead-source" class="block text-sm font-medium text-stone-700">Lead source</label>
                    <select id="contact-lead-source" wire:model="form.lead_source" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                        @foreach ($leadSourceOptions as $leadSourceOption)
                            <option value="{{ $leadSourceOption->value }}">{{ $leadSourceOption->name }}</option>
                        @endforeach
                    </select>
                    @error('form.lead_source') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-preferred-channel" class="block text-sm font-medium text-stone-700">Preferred channel</label>
                    <select id="contact-preferred-channel" wire:model="form.preferred_channel" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                        @foreach ($preferredChannelOptions as $preferredChannelOption)
                            <option value="{{ $preferredChannelOption->value }}">{{ $preferredChannelOption->name }}</option>
                        @endforeach
                    </select>
                    @error('form.preferred_channel') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact-birthday" class="block text-sm font-medium text-stone-700">Birthday</label>
                    <input id="contact-birthday" type="date" wire:model="form.birthday" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @error('form.birthday') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="flex items-center gap-3 text-sm text-stone-700">
                <input type="checkbox" wire:model="form.do_not_contact" class="h-4 w-4 rounded border-stone-300 text-stone-900 focus:ring-2 focus:ring-stone-200">
                <span>Do not contact</span>
            </label>

            <div class="space-y-2">
                <label for="contact-notes" class="block text-sm font-medium text-stone-700">Notes</label>
                <textarea id="contact-notes" rows="4" wire:model="form.notes" class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"></textarea>
                @error('form.notes') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-stone-950 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-300">
                    {{ $editingContactId ? 'Update contact' : 'Create contact' }}
                </button>

                @if ($editingContactId)
                    <button type="button" wire:click="cancelEditing" class="inline-flex items-center justify-center rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:border-stone-400 hover:text-stone-950">
                        Cancel
                    </button>
                @endif
            </div>
        </form>
    </section>
</div>
