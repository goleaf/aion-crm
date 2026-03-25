<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\CreateAccountData;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use App\Modules\CRM\Foundation\Support\TagList;
use App\Modules\CRM\Foundation\ValueObjects\Money;
use App\Modules\CRM\Foundation\ValueObjects\PostalAddress;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Support\CrmUserProfileFactory;
use App\Modules\Shared\Models\User;

/** @final */
class CreateAccountAction
{
    public function execute(CreateAccountData $data): Account
    {
        $teamId = $data->owner instanceof User ? CrmUserProfileFactory::forUser($data->owner)->primary_team_id : null;

        return Account::query()->create([
            'name' => trim($data->name),
            'industry' => $data->industry,
            'type' => $data->type,
            'website' => $this->normalizeNullableString($data->website),
            'phone' => $this->normalizeNullableString($data->phone),
            'email' => $this->normalizeNullableString($data->email),
            'billing_address' => $this->normalizeAddress($data->billingAddress),
            'shipping_address' => $this->normalizeAddress($data->shippingAddress),
            'annual_revenue_minor' => $data->annualRevenue === null
                ? null
                : Money::fromDecimal($data->annualRevenue, CurrencyCodeEnum::USD)->amountInMinorUnits,
            'employee_count' => $data->employeeCount,
            'owner_id' => $data->owner?->getKey(),
            'team_id' => $teamId,
            'parent_account_id' => $data->parentAccount?->getKey(),
            'tags' => TagList::normalize($data->tags),
        ]);
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
