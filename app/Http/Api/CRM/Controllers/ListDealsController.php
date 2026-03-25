<?php

namespace App\Http\Api\CRM\Controllers;

use App\Http\Api\CRM\Requests\ListDealsRequest;
use App\Http\Api\CRM\Resources\DealResource;
use App\Modules\CRM\Models\Deal;
use App\Modules\CRM\Models\Pipeline;
use App\Modules\Shared\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListDealsController
{
    public function __invoke(ListDealsRequest $request): AnonymousResourceCollection
    {
        /** @var User $actor */
        $actor = $request->user();

        $deals = Deal::query()
            ->select([
                'id',
                'name',
                'account_id',
                'contact_id',
                'owner_id',
                'team_id',
                'stage',
                'amount_minor',
                'currency',
                'probability',
                'close_date',
                'deal_type',
                'pipeline_id',
                'lost_reason',
                'source',
                'created_at',
                'updated_at',
            ])
            ->with([
                'account:id,name',
                'contact:id,first_name,last_name,email',
                'owner:id,name',
                'pipeline:id,name',
            ])
            ->visibleTo($actor)
            ->search($request->validated('search'))
            ->when($request->filled('pipeline_id'), fn ($query) => $query->whereBelongsTo(
                related: Pipeline::query()->findOrFail($request->validated('pipeline_id')),
                relationshipName: 'pipeline',
            ))
            ->when($request->filled('owner_id'), fn ($query) => $query->where('owner_id', $request->validated('owner_id')))
            ->when($request->filled('stage'), fn ($query) => $query->where('stage', $request->validated('stage')))
            ->when($request->validated('status') === 'open', fn ($query) => $query->open())
            ->when($request->validated('status') === 'closed', fn ($query) => $query->closed())
            ->orderBy('close_date')
            ->orderByDesc('created_at')
            ->paginate((int) $request->validated('per_page'))
            ->withQueryString();

        return DealResource::collection($deals);
    }
}
