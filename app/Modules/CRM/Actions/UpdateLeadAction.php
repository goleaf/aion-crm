<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\UpdateLeadData;
use App\Modules\CRM\Models\Lead;
use App\Modules\CRM\Services\LeadScoringService;

/** @final */
class UpdateLeadAction
{
    public function __construct(
        private readonly LeadScoringService $leadScoringService,
    ) {}

    public function execute(UpdateLeadData $data): Lead
    {
        $leadScore = $this->leadScoringService->score(
            email: $data->email,
            phone: $data->phone,
            leadSource: $data->leadSource,
            status: $data->status,
        );

        $data->lead->fill([
            'first_name' => $data->firstName,
            'last_name' => $data->lastName,
            'company' => $data->company,
            'email' => $data->email,
            'phone' => $data->phone,
            'lead_source' => $data->leadSource,
            'status' => $data->status,
            'score' => $leadScore->score,
            'rating' => $leadScore->rating,
            'campaign_id' => $data->campaignId,
            'owner_id' => $data->owner->getKey(),
            'description' => $data->description,
        ]);

        $data->lead->save();

        return $data->lead->refresh()->load('owner');
    }
}
