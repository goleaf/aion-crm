<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
            <div class="flex-1 space-y-1">
                <label class="block text-xs font-medium uppercase tracking-wide text-stone-500">Search</label>
                <input
                    type="search"
                    wire:model.live="search"
                    placeholder="Search deals"
                    class="w-full rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
                >
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-medium uppercase tracking-wide text-stone-500">Stage</label>
                <select wire:model.live="stage" class="rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    <option value="">All stages</option>
                    @foreach ($stageOptions as $stageOption)
                        <option value="{{ $stageOption->value }}">{{ str($stageOption->value)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-medium uppercase tracking-wide text-stone-500">Pipeline</label>
                <select wire:model.live="pipelineId" class="rounded-xl border border-stone-300 bg-white px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    <option value="">All pipelines</option>
                    @foreach ($pipelines as $pipeline)
                        <option value="{{ $pipeline->getKey() }}">{{ $pipeline->name }}</option>
                    @endforeach
                </select>
            </div>

            <label class="inline-flex items-center gap-2 rounded-xl border border-stone-200 px-3 py-2 text-sm text-stone-700">
                <input type="checkbox" wire:model.live="showClosed" class="rounded border-stone-300 text-stone-950 focus:ring-stone-300">
                <span>Show closed</span>
            </label>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-left text-sm">
                <thead class="bg-stone-50">
                    <tr>
                        <th class="px-4 py-3 font-medium text-stone-700 sm:px-6">Deal</th>
                        <th class="px-4 py-3 font-medium text-stone-700 sm:px-6">Stage</th>
                        <th class="px-4 py-3 font-medium text-stone-700 sm:px-6">Owner</th>
                        <th class="px-4 py-3 font-medium text-stone-700 sm:px-6">Forecast</th>
                        <th class="px-4 py-3 font-medium text-stone-700 sm:px-6">Close Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($deals as $deal)
                        <tr wire:key="deal-{{ $deal->getKey() }}">
                            <td class="px-4 py-3 sm:px-6">
                                <a href="{{ route('crm.deals.edit', $deal) }}" class="font-medium text-stone-950 hover:text-stone-700">{{ $deal->name }}</a>
                                <div class="text-xs text-stone-500">
                                    {{ $deal->account?->name ?? 'No account' }}
                                    @if ($deal->contact !== null)
                                        · {{ $deal->contact->full_name }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-stone-600 sm:px-6">{{ str($deal->stage->value)->replace('_', ' ')->title() }}</td>
                            <td class="px-4 py-3 text-stone-600 sm:px-6">{{ $deal->owner?->name ?? 'Unassigned' }}</td>
                            <td class="px-4 py-3 text-stone-600 sm:px-6">
                                <div>{{ $deal->amountMoney()->toDecimal() }} {{ $deal->currency->value }}</div>
                                <div class="text-xs text-stone-500">{{ $deal->expectedRevenueMoney()->toDecimal() }} forecast</div>
                            </td>
                            <td class="px-4 py-3 text-stone-600 sm:px-6">{{ $deal->close_date?->toDateString() ?? 'TBD' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-stone-500 sm:px-6">No deals match the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
