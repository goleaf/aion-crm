<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_24rem]">
    <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-200 px-4 py-4 sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-stone-950">Agenda</h2>
                    <p class="mt-1 text-sm text-stone-600">Track meetings, demos, reminders, and follow-ups in one internal workspace.</p>
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-stone-700" for="reminderFilter">Reminder filter</label>
                    <select
                        id="reminderFilter"
                        wire:model.live="reminderFilter"
                        class="rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200"
                    >
                        <option value="all">All activities</option>
                        <option value="due">Due reminders</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="divide-y divide-stone-200">
            @forelse ($this->agendaItems as $agendaItem)
                <article wire:key="activity-{{ $agendaItem['activity']->getKey() }}-{{ $agendaItem['occurs_at']->format('YmdHi') }}" class="px-4 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm font-semibold text-stone-950">{{ $agendaItem['activity']->title }}</h3>
                                <span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-medium text-stone-700">{{ $agendaItem['activity']->type->label() }}</span>
                                <span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-medium text-stone-700">{{ $agendaItem['activity']->status->label() }}</span>

                                @if ($agendaItem['reminder_state'] === 'due')
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">Reminder due</span>
                                @elseif ($agendaItem['reminder_state'] === 'overdue')
                                    <span class="rounded-full bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-700">Overdue</span>
                                @endif
                            </div>

                            <div class="space-y-1 text-sm text-stone-600">
                                <p>{{ $agendaItem['occurs_at']->format('M d, Y H:i') }} to {{ $agendaItem['ends_at']->format('M d, Y H:i') }}</p>
                                <p>Organizer: {{ $agendaItem['activity']->organizer?->name }}</p>

                                @if ($agendaItem['activity']->location)
                                    <p>Location: {{ $agendaItem['activity']->location }}</p>
                                @endif

                                @if ($agendaItem['activity']->description)
                                    <p>{{ $agendaItem['activity']->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                wire:click="edit('{{ $agendaItem['activity']->getKey() }}')"
                                class="rounded-xl border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:border-stone-400 hover:text-stone-950"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                wire:click="markCompleted('{{ $agendaItem['activity']->getKey() }}')"
                                class="rounded-xl border border-emerald-300 px-3 py-2 text-sm font-medium text-emerald-700 transition hover:border-emerald-400"
                            >
                                Complete
                            </button>

                            <button
                                type="button"
                                wire:click="cancel('{{ $agendaItem['activity']->getKey() }}')"
                                class="rounded-xl border border-rose-300 px-3 py-2 text-sm font-medium text-rose-700 transition hover:border-rose-400"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-4 py-8 text-sm text-stone-600 sm:px-6">
                    No activities match the current filter.
                </div>
            @endforelse
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-200 px-4 py-4 sm:px-6">
            <h2 class="text-base font-semibold text-stone-950">
                {{ $editingActivityId ? 'Edit activity' : 'Create activity' }}
            </h2>
            <p class="mt-1 text-sm text-stone-600">Keep scheduling internal and tied directly to your CRM workflow.</p>
        </div>

        <form wire:submit="save" class="space-y-4 px-4 py-4 sm:px-6">
            <div class="space-y-2">
                <label class="text-sm font-medium text-stone-700" for="title">Title</label>
                <input id="title" type="text" wire:model="title" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200">
                @error('title') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-stone-700" for="type">Type</label>
                    <select id="type" wire:model="type" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200">
                        <option value="meeting">Meeting</option>
                        <option value="demo">Demo</option>
                        <option value="follow-up">Follow-up</option>
                        <option value="reminder">Reminder</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-stone-700" for="status">Status</label>
                    <select id="status" wire:model="status" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200">
                        <option value="scheduled">Scheduled</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-stone-700" for="startAt">Start</label>
                    <input id="startAt" type="datetime-local" wire:model="startAt" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-stone-700" for="endAt">End</label>
                    <input id="endAt" type="datetime-local" wire:model="endAt" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-stone-700" for="location">Location</label>
                    <input id="location" type="text" wire:model="location" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-stone-700" for="reminderMinutes">Reminder minutes</label>
                    <input id="reminderMinutes" type="number" min="0" wire:model="reminderMinutes" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-stone-700" for="recurrence">Recurrence</label>
                    <select id="recurrence" wire:model="recurrence" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200">
                        <option value="none">Does not repeat</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-medium text-stone-700">
                        <input type="checkbox" wire:model="allDay" class="rounded border-stone-300 text-stone-900 focus:ring-stone-400">
                        All day
                    </label>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-stone-700" for="attendeeIds">Attendees</label>
                <select id="attendeeIds" wire:model="attendeeIds" multiple class="min-h-32 w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200">
                    @foreach ($this->availableUsers as $user)
                        <option wire:key="attendee-{{ $user->id }}" value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-stone-700" for="description">Description</label>
                <textarea id="description" rows="4" wire:model="description" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-900 shadow-sm focus:border-stone-500 focus:outline-none focus:ring-2 focus:ring-stone-200"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" wire:click="$refresh" class="rounded-xl border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:border-stone-400">Refresh</button>
                <button type="submit" class="rounded-xl bg-stone-950 px-4 py-2 text-sm font-medium text-white transition hover:bg-stone-800">Save activity</button>
            </div>
        </form>
    </section>
</div>
