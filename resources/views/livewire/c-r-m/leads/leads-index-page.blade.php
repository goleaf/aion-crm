<div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
    <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-200 px-4 py-4 sm:px-6">
            <div class="flex flex-col gap-4">
                <div class="space-y-1">
                    <h2 class="text-base font-semibold text-stone-950">Lead Workspace</h2>
                    <p class="text-sm text-stone-600">Search, qualify, and prepare leads for later conversion.</p>
                </div>

                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    <div class="md:col-span-2 xl:col-span-1">
                        <label for="lead-search" class="mb-1 block text-xs font-medium uppercase tracking-wide text-stone-500">Search</label>
                        <input
                            id="lead-search"
                            type="text"
                            wire:model.live="search"
                            placeholder="Search by name, company, email, or phone"
                            class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                        >
                    </div>

                    <div>
                        <label for="lead-status-filter" class="mb-1 block text-xs font-medium uppercase tracking-wide text-stone-500">Status</label>
                        <select
                            id="lead-status-filter"
                            wire:model.live="statusFilter"
                            class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                        >
                            <option value="all">All statuses</option>
                            @foreach ($this->leadStatusOptions() as $option)
                                <option value="{{ $option->value }}">{{ $option->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="lead-source-filter" class="mb-1 block text-xs font-medium uppercase tracking-wide text-stone-500">Source</label>
                        <select
                            id="lead-source-filter"
                            wire:model.live="sourceFilter"
                            class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                        >
                            <option value="all">All sources</option>
                            @foreach ($this->leadSourceOptions() as $option)
                                <option value="{{ $option->value }}">{{ $option->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="lead-rating-filter" class="mb-1 block text-xs font-medium uppercase tracking-wide text-stone-500">Rating</label>
                        <select
                            id="lead-rating-filter"
                            wire:model.live="ratingFilter"
                            class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                        >
                            <option value="all">All ratings</option>
                            @foreach ($this->leadRatingOptions() as $option)
                                <option value="{{ $option->value }}">{{ $option->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="lead-owner-filter" class="mb-1 block text-xs font-medium uppercase tracking-wide text-stone-500">Owner</label>
                        <select
                            id="lead-owner-filter"
                            wire:model.live="ownerFilter"
                            class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                        >
                            <option value="all">All owners</option>
                            @foreach ($this->owners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="lead-converted-filter" class="mb-1 block text-xs font-medium uppercase tracking-wide text-stone-500">Conversion</label>
                        <select
                            id="lead-converted-filter"
                            wire:model.live="convertedFilter"
                            class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                        >
                            <option value="all">All leads</option>
                            <option value="0">Open only</option>
                            <option value="1">Converted only</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-left text-sm">
                <thead class="bg-stone-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Lead</th>
                        <th scope="col" class="px-4 py-3 font-medium text-stone-700">Source</th>
                        <th scope="col" class="px-4 py-3 font-medium text-stone-700">Status</th>
                        <th scope="col" class="px-4 py-3 font-medium text-stone-700">Owner</th>
                        <th scope="col" class="px-4 py-3 font-medium text-stone-700">Score</th>
                        <th scope="col" class="px-4 py-3 font-medium text-stone-700">Conversion</th>
                        <th scope="col" class="px-4 py-3 font-medium text-stone-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($this->leads as $lead)
                        <tr wire:key="lead-row-{{ $lead->lead_id }}">
                            <td class="px-4 py-4 align-top sm:px-6">
                                <div class="font-medium text-stone-950">{{ $lead->first_name }} {{ $lead->last_name }}</div>
                                <div class="text-stone-600">{{ $lead->company ?: 'No company recorded' }}</div>
                                <div class="mt-1 text-xs text-stone-500">{{ $lead->email ?: 'No email' }} · {{ $lead->phone ?: 'No phone' }}</div>
                            </td>
                            <td class="px-4 py-4 align-top text-stone-700">{{ $lead->lead_source->label() }}</td>
                            <td class="px-4 py-4 align-top">
                                <div class="font-medium text-stone-900">{{ $lead->status->label() }}</div>
                                <div class="text-xs text-stone-500">{{ $lead->rating->label() }}</div>
                            </td>
                            <td class="px-4 py-4 align-top text-stone-700">{{ $lead->owner?->name ?? 'Unassigned' }}</td>
                            <td class="px-4 py-4 align-top">
                                <div class="font-medium text-stone-900">{{ $lead->score }}</div>
                                <div class="text-xs text-stone-500">{{ $lead->rating->label() }}</div>
                            </td>
                            <td class="px-4 py-4 align-top text-stone-700">
                                @if ($lead->converted)
                                    <div>Converted</div>
                                    <div class="text-xs text-stone-500">{{ optional($lead->converted_at)->format('Y-m-d H:i') }}</div>
                                @else
                                    <div>Reserved</div>
                                    <div class="text-xs text-stone-500">Contacts and deals are not wired yet.</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 align-top">
                                <button
                                    type="button"
                                    wire:click="editLead('{{ $lead->lead_id }}')"
                                    class="inline-flex items-center rounded-lg border border-stone-300 px-3 py-1.5 text-sm font-medium text-stone-700 transition hover:border-stone-400 hover:text-stone-950"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-stone-500 sm:px-6">No leads match the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-stone-200 px-4 py-4 sm:px-6">
            {{ $this->leads->links() }}
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-200 px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-stone-950">{{ $editingLeadId ? 'Edit lead' : 'Capture lead' }}</h2>
                    <p class="mt-1 text-sm text-stone-600">Score and rating are calculated automatically on save.</p>
                </div>

                <button
                    type="button"
                    wire:click="createNew"
                    class="inline-flex items-center rounded-lg border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:border-stone-400 hover:text-stone-950"
                >
                    New lead
                </button>
            </div>
        </div>

        <form wire:submit="save" class="space-y-5 px-4 py-5 sm:px-6">
            @if ($feedback)
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ $feedback }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="lead-first-name" class="block text-sm font-medium text-stone-700">First name</label>
                    <input
                        id="lead-first-name"
                        type="text"
                        wire:model="first_name"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    >
                </div>

                <div class="space-y-2">
                    <label for="lead-last-name" class="block text-sm font-medium text-stone-700">Last name</label>
                    <input
                        id="lead-last-name"
                        type="text"
                        wire:model="last_name"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    >
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <label for="lead-company" class="block text-sm font-medium text-stone-700">Company</label>
                    <input
                        id="lead-company"
                        type="text"
                        wire:model="company"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    >
                </div>

                <div class="space-y-2">
                    <label for="lead-email" class="block text-sm font-medium text-stone-700">Email</label>
                    <input
                        id="lead-email"
                        type="email"
                        wire:model="email"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    >
                </div>

                <div class="space-y-2">
                    <label for="lead-phone" class="block text-sm font-medium text-stone-700">Phone</label>
                    <input
                        id="lead-phone"
                        type="text"
                        wire:model="phone"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    >
                </div>

                <div class="space-y-2">
                    <label for="lead-source" class="block text-sm font-medium text-stone-700">Lead source</label>
                    <select
                        id="lead-source"
                        wire:model="lead_source"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    >
                        @foreach ($this->leadSourceOptions() as $option)
                            <option value="{{ $option->value }}">{{ $option->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="lead-status" class="block text-sm font-medium text-stone-700">Status</label>
                    <select
                        id="lead-status"
                        wire:model="status"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    >
                        @foreach ($this->leadStatusOptions() as $option)
                            <option value="{{ $option->value }}">{{ $option->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="lead-owner" class="block text-sm font-medium text-stone-700">Owner</label>
                    <select
                        id="lead-owner"
                        wire:model="owner_id"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    >
                        @foreach ($this->owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="lead-campaign" class="block text-sm font-medium text-stone-700">Campaign ID</label>
                    <input
                        id="lead-campaign"
                        type="number"
                        wire:model="campaign_id"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    >
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <label for="lead-description" class="block text-sm font-medium text-stone-700">Description</label>
                    <textarea
                        id="lead-description"
                        wire:model="description"
                        rows="4"
                        class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                    ></textarea>
                </div>
            </div>

            @if ($editingLeadId)
                <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-700">
                    <div class="font-medium text-stone-900">Current qualification</div>
                    <div class="mt-1">Score: {{ $current_score ?? 'Pending' }} · Rating: {{ $current_rating ?? 'Pending' }}</div>
                    <div class="mt-1">Converted: {{ $current_converted ? 'Yes' : 'No' }}</div>
                    <div class="mt-1 text-xs text-stone-500">
                        Contact ID: {{ $current_converted_to_contact_id ?? 'Pending' }} · Deal ID: {{ $current_converted_to_deal_id ?? 'Pending' }}
                    </div>
                    @if ($current_converted_at)
                        <div class="mt-1 text-xs text-stone-500">Converted at: {{ $current_converted_at }}</div>
                    @endif
                </div>
            @endif

            <div class="flex items-center justify-end gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-xl bg-stone-950 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-stone-800"
                >
                    {{ $editingLeadId ? 'Update lead' : 'Create lead' }}
                </button>
            </div>
        </form>
    </section>
</div>
