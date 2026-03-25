<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\CreateLeadData;
use App\Modules\CRM\Models\Lead;
use App\Modules\CRM\Services\LeadScoringService;

/** @final */
class CreateLeadAction
{
    public function __construct(
        private readonly LeadScoringService $leadScoringService,
    ) {}

    public function execute(CreateLeadData $data): Lead
    {
        $leadScore = $this->leadScoringService->score(
            email: $data->email,
            phone: $data->phone,
            leadSource: $data->leadSource,
            status: $data->status,
        );

        return Lead::query()->create([
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
            'converted' => false,
            'converted_to_contact_id' => null,
            'converted_to_deal_id' => null,
            'converted_at' => null,
            'description' => $data->description,
        ])->load('owner');
    }
}
