<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\CloseDealAsLostData;
use App\Modules\CRM\Enums\DealStageEnum;
use App\Modules\CRM\Exceptions\DealLifecycleViolationException;
use App\Modules\CRM\Models\Deal;

/** @final */
class CloseDealAsLostAction
{
    public function execute(CloseDealAsLostData $data): Deal
    {
        if ($data->deal->isClosed()) {
            throw DealLifecycleViolationException::forClosedTransition();
        }

        $data->deal->stage = DealStageEnum::ClosedLost;
        $data->deal->lost_reason = $data->lostReason;
        $data->deal->save();

        return $data->deal;
    }
}
