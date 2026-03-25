<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\UpdateAccountData;
use App\Modules\CRM\Foundation\Support\TagList;
use App\Modules\CRM\Foundation\ValueObjects\PostalAddress;
use App\Modules\CRM\Models\Account;

/** @final */
class UpdateAccountAction
{
    public function execute(UpdateAccountData $data): Account
    {
        $data->account->fill([
            'name' => trim($data->name),
            'industry' => $data->industry,
            'type' => $data->type,
            'website' => $this->normalizeNullableString($data->website),
            'phone' => $this->normalizeNullableString($data->phone),
            'email' => $this->normalizeNullableString($data->email),
            'billing_address' => $this->normalizeAddress($data->billingAddress),
            'shipping_address' => $this->normalizeAddress($data->shippingAddress),
            'annual_revenue' => $data->annualRevenue,
            'employee_count' => $data->employeeCount,
            'owner_id' => $data->owner?->getKey(),
            'owner_team_id' => $data->ownerTeamId,
            'parent_account_id' => $data->parentAccount?->getKey(),
            'tags' => TagList::normalize($data->tags),
        ]);

        $data->account->save();

        return $data->account->refresh();
    }

    /**
     * @param  array<string, mixed>|null  $payload
     * @return array<string, string|null>|null
     */
    private function normalizeAddress(?array $payload): ?array
    {
        if (! is_array($payload)) {
            return null;
        }

        $hasValue = collect($payload)
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->isNotEmpty();

        if (! $hasValue) {
            return null;
        }

        return PostalAddress::fromArray($payload)->toArray();
    }

    private function normalizeNullableString(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
