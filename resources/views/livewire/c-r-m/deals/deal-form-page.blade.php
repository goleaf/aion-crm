<div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
    <form wire:submit="save" class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="space-y-2 md:col-span-2">
                <label class="block text-sm font-medium text-stone-700">Deal Name</label>
                <input wire:model="name" type="text" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Account</label>
                <select wire:model="accountId" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    <option value="">Select account</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->getKey() }}">{{ $account->name }}</option>
                    @endforeach
                </select>
                @error('account_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Primary Contact</label>
                <select wire:model="contactId" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    <option value="">No contact</option>
                    @foreach ($contacts as $contact)
                        <option value="{{ $contact->getKey() }}">{{ $contact->full_name }}</option>
                    @endforeach
                </select>
                @error('contact_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Owner</label>
                <select wire:model="ownerId" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @foreach ($owners as $owner)
                        <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                    @endforeach
                </select>
                @error('owner_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Pipeline</label>
                <select wire:model="pipelineId" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    <option value="">Select pipeline</option>
                    @foreach ($pipelines as $pipeline)
                        <option value="{{ $pipeline->getKey() }}">{{ $pipeline->name }}</option>
                    @endforeach
                </select>
                @error('pipeline_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Stage</label>
                <select wire:model="stage" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @foreach ($stageOptions as $stageOption)
                        <option value="{{ $stageOption->value }}">{{ str($stageOption->value)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
                @error('stage') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Amount</label>
                <input wire:model="amount" type="text" placeholder="25000.00" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                @error('amount') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Currency</label>
                <select wire:model="currency" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @foreach ($currencyOptions as $currencyOption)
                        <option value="{{ $currencyOption->value }}">{{ $currencyOption->value }}</option>
                    @endforeach
                </select>
                @error('currency') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Probability %</label>
                <input wire:model="probability" type="number" min="0" max="100" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                @error('probability') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Expected Close Date</label>
                <input wire:model="closeDate" type="date" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                @error('close_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Deal Type</label>
                <select wire:model="dealType" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    @foreach ($dealTypeOptions as $dealTypeOption)
                        <option value="{{ $dealTypeOption->value }}">{{ str($dealTypeOption->value)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
                @error('deal_type') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Source</label>
                <select wire:model="source" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    <option value="">No source</option>
                    @foreach ($sourceOptions as $sourceOption)
                        <option value="{{ $sourceOption->value }}">{{ str($sourceOption->value)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
                @error('source') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-stone-700">Lost Reason</label>
                <select wire:model="lostReason" class="w-full rounded-xl border border-stone-300 px-3 py-2 text-sm text-stone-950 outline-none focus:border-stone-400 focus:ring-2 focus:ring-stone-200">
                    <option value="">No lost reason</option>
                    @foreach ($lostReasonOptions as $lostReasonOption)
                        <option value="{{ $lostReasonOption->value }}">{{ str($lostReasonOption->value)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
                @error('lost_reason') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('crm.deals.index') }}" class="text-sm text-stone-600 hover:text-stone-950">Back to deals</a>

            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-stone-950 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-stone-800">
                {{ $isEditing ? 'Update Deal' : 'Create Deal' }}
            </button>
        </div>
    </form>
</div>
