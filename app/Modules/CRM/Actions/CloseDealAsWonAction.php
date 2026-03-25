<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\CloseDealAsWonData;
use App\Modules\CRM\Enums\DealStageEnum;
use App\Modules\CRM\Exceptions\DealLifecycleViolationException;
use App\Modules\CRM\Models\Deal;

/** @final */
class CloseDealAsWonAction
{
    public function execute(CloseDealAsWonData $data): Deal
    {
        if ($data->deal->isClosed()) {
            throw DealLifecycleViolationException::forClosedTransition();
        }

        $data->deal->stage = DealStageEnum::ClosedWon;
        $data->deal->lost_reason = null;
        $data->deal->save();

        return $data->deal;
    }
}
