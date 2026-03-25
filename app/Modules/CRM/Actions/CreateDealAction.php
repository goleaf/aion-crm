<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\CreateDealData;
use App\Modules\CRM\Foundation\ValueObjects\Money;
use App\Modules\CRM\Models\Deal;
use App\Modules\CRM\Support\CrmUserProfileFactory;

/** @final */
class CreateDealAction
{
    public function execute(CreateDealData $data): Deal
    {
        $teamId = CrmUserProfileFactory::forUser($data->owner)->primary_team_id;

        return Deal::query()->create([
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
    }
}
