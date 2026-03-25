<?php

namespace App\Modules\CRM\DataTransferObjects;

use App\Http\Api\CRM\Requests\StoreDealRequest;
use App\Modules\CRM\Enums\DealLostReasonEnum;
use App\Modules\CRM\Enums\DealSourceEnum;
use App\Modules\CRM\Enums\DealStageEnum;
use App\Modules\CRM\Enums\DealTypeEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\Pipeline;
use App\Modules\Shared\Models\User;
use Carbon\CarbonImmutable;

final readonly class CreateDealData
{
    public function __construct(
        public string $name,
        public Account $account,
        public ?Contact $contact,
        public User $owner,
        public DealStageEnum $stage,
        public string $amount,
        public CurrencyCodeEnum $currency,
        public int $probability,
        public ?CarbonImmutable $closeDate,
        public DealTypeEnum $dealType,
        public Pipeline $pipeline,
        public ?DealLostReasonEnum $lostReason,
        public ?DealSourceEnum $source,
    ) {}

    public static function fromRequest(StoreDealRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            account: Account::query()->findOrFail($request->validated('account_id')),
            contact: $request->filled('contact_id')
                ? Contact::query()->findOrFail($request->validated('contact_id'))
                : null,
            owner: User::query()->findOrFail($request->validated('owner_id')),
            stage: $request->enum('stage', DealStageEnum::class),
            amount: $request->validated('amount'),
            currency: $request->enum('currency', CurrencyCodeEnum::class),
            probability: (int) $request->validated('probability'),
            closeDate: $request->date('close_date')?->toImmutable(),
            dealType: $request->enum('deal_type', DealTypeEnum::class),
            pipeline: Pipeline::query()->findOrFail($request->validated('pipeline_id')),
            lostReason: $request->filled('lost_reason')
                ? $request->enum('lost_reason', DealLostReasonEnum::class)
                : null,
            source: $request->filled('source')
                ? $request->enum('source', DealSourceEnum::class)
                : null,
        );
    }
}
