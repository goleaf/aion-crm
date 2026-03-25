<?php

namespace App\Http\Api\CRM\Controllers;

use App\Http\Api\CRM\Requests\CloseDealAsLostRequest;
use App\Http\Api\CRM\Resources\DealResource;
use App\Modules\CRM\Actions\CloseDealAsLostAction;
use App\Modules\CRM\Authorization\CrmRecordManagement;
use App\Modules\CRM\DataTransferObjects\CloseDealAsLostData;
use App\Modules\CRM\Exceptions\DealLifecycleViolationException;
use App\Modules\CRM\Models\Deal;
use App\Modules\Shared\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CloseDealAsLostController
{
    public function __invoke(
        CloseDealAsLostRequest $request,
        Deal $deal,
        CloseDealAsLostAction $closeDealAsLostAction,
    ): DealResource {
        /** @var User $actor */
        $actor = $request->user();

        if (! CrmRecordManagement::canManageRecord($actor, $deal->owner_id, $deal->team_id)) {
            throw new HttpResponseException(response()->json(['message' => 'Forbidden.'], Response::HTTP_FORBIDDEN));
        }

        try {
            $closedDeal = $closeDealAsLostAction->execute(CloseDealAsLostData::fromRequest($request, $deal));
        } catch (DealLifecycleViolationException $exception) {
            throw ValidationException::withMessages([
                'stage' => [$exception->getMessage()],
            ]);
        }

        return DealResource::make($closedDeal->load([
            'account:id,name',
            'contact:id,first_name,last_name,email',
            'owner:id,name',
            'pipeline:id,name',
        ]));
    }
}
