<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\UpdateDealData;
use App\Modules\CRM\Exceptions\DealLifecycleViolationException;
use App\Modules\CRM\Foundation\ValueObjects\Money;
use App\Modules\CRM\Models\Deal;
use App\Modules\CRM\Support\CrmUserProfileFactory;

/** @final */
class UpdateDealAction
{
    public function execute(UpdateDealData $data): Deal
    {
        if ($data->deal->isClosed()) {
            throw DealLifecycleViolationException::forClosedUpdate();
        }

        $teamId = CrmUserProfileFactory::forUser($data->owner)->primary_team_id;

        $data->deal->fill([
            'name' => trim($data->name),
            'account_id' => $data->account->getKey(),
            'contact_id' => $data->contact?->getKey(),
            'owner_id' => $data->owner->getKey(),
            'team_id' => $teamId,
            'stage' => $data->stage,
            'amount_minor' => Money::fromDecimal($data->amount, $data->currency)->amountInMinorUnits,
            'currency' => $data->currency,
            'probability' => $data->probability,
            'close_date' => $data->closeDate,
            'deal_type' => $data->dealType,
            'pipeline_id' => $data->pipeline->getKey(),
            'lost_reason' => $data->lostReason,
            'source' => $data->source,
        ]);

        $data->deal->save();

        return $data->deal;
    }
}
