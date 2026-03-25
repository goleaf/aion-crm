<?php

namespace App\Http\Api\CRM\Controllers;

use App\Http\Api\CRM\Resources\DealResource;
use App\Modules\CRM\Authorization\CrmRecordVisibility;
use App\Modules\CRM\Models\Deal;
use App\Modules\Shared\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ShowDealController
{
    public function __invoke(Deal $deal): DealResource
    {
        /** @var User $actor */
        $actor = request()->user();

        if (! CrmRecordVisibility::canViewRecord($actor, $deal->owner_id, $deal->team_id)) {
            throw new HttpResponseException(response()->json(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN));
        }

        return DealResource::make($deal->load([
            'account:id,name',
            'contact:id,first_name,last_name,email',
            'owner:id,name',
            'pipeline:id,name',
        ]));
    }
}
