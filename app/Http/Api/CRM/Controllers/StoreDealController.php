<?php

namespace App\Http\Api\CRM\Controllers;

use App\Http\Api\CRM\Requests\StoreDealRequest;
use App\Http\Api\CRM\Resources\DealResource;
use App\Modules\CRM\Actions\CreateDealAction;
use App\Modules\CRM\Authorization\CrmRecordManagement;
use App\Modules\CRM\DataTransferObjects\CreateDealData;
use App\Modules\Shared\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class StoreDealController
{
    public function __invoke(
        StoreDealRequest $request,
        CreateDealAction $createDealAction,
    ): DealResource {
        /** @var User $actor */
        $actor = $request->user();

        $data = CreateDealData::fromRequest($request);

        if (! CrmRecordManagement::canAssignRecordToOwner($actor, $data->owner)) {
            throw new HttpResponseException(response()->json(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN));
        }

        $deal = $createDealAction->execute($data);

        return DealResource::make($deal->load([
            'account:id,name',
            'contact:id,first_name,last_name,email',
            'owner:id,name',
            'pipeline:id,name',
        ]))->additional([
            'meta' => [
                'status' => Response::HTTP_CREATED,
            ],
        ]);
    }
}
