<?php

namespace App\Modules\CRM\DataTransferObjects;

use App\Http\Api\CRM\Requests\CloseDealAsLostRequest;
use App\Modules\CRM\Enums\DealLostReasonEnum;
use App\Modules\CRM\Models\Deal;

final readonly class CloseDealAsLostData
{
    public function __construct(
        public Deal $deal,
        public DealLostReasonEnum $lostReason,
    ) {}

    public static function fromRequest(CloseDealAsLostRequest $request, Deal $deal): self
    {
        return new self(
            deal: $deal,
            lostReason: $request->enum('lost_reason', DealLostReasonEnum::class),
        );
    }
}
