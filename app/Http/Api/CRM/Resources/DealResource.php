<?php

namespace App\Http\Api\CRM\Resources;

use App\Modules\CRM\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Deal $resource
 */
class DealResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'name' => $this->resource->name,
            'stage' => $this->resource->stage->value,
            'amount_minor' => $this->resource->amount_minor,
            'amount' => $this->resource->amountMoney()->toDecimal(),
            'currency' => $this->resource->currency->value,
            'probability' => $this->resource->probability,
            'expected_revenue_minor' => $this->resource->expectedRevenueMoney()->amountInMinorUnits,
            'expected_revenue' => $this->resource->expectedRevenueMoney()->toDecimal(),
            'close_date' => $this->resource->close_date?->toDateString(),
            'deal_type' => $this->resource->deal_type->value,
            'lost_reason' => $this->resource->lost_reason?->value,
            'source' => $this->resource->source?->value,
            'account' => $this->whenLoaded('account', fn (): array => [
                'id' => $this->resource->account?->getKey(),
                'name' => $this->resource->account?->name,
            ]),
            'contact' => $this->whenLoaded('contact', fn (): ?array => $this->resource->contact === null ? null : [
                'id' => $this->resource->contact->getKey(),
                'full_name' => $this->resource->contact->full_name,
                'email' => $this->resource->contact->email,
            ]),
            'owner' => $this->whenLoaded('owner', fn (): array => [
                'id' => $this->resource->owner?->getKey(),
                'name' => $this->resource->owner?->name,
            ]),
            'pipeline' => $this->whenLoaded('pipeline', fn (): array => [
                'id' => $this->resource->pipeline?->getKey(),
                'name' => $this->resource->pipeline?->name,
            ]),
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
