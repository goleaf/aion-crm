<?php

namespace App\Modules\CRM\DataTransferObjects;

use App\Modules\CRM\Enums\AccountIndustryEnum;
use App\Modules\CRM\Enums\AccountTypeEnum;
use App\Modules\CRM\Models\Account;
use App\Modules\Shared\Models\User;

final readonly class UpdateAccountData
{
    /**
     * @param  array<string, mixed>|null  $billingAddress
     * @param  array<string, mixed>|null  $shippingAddress
     * @param  list<string>  $tags
     */
    public function __construct(
        public Account $account,
        public string $name,
        public AccountIndustryEnum $industry,
        public AccountTypeEnum $type,
        public ?string $website,
        public ?string $phone,
        public ?string $email,
        public ?array $billingAddress,
        public ?array $shippingAddress,
        public ?string $annualRevenue,
        public ?int $employeeCount,
        public ?User $owner,
        public ?int $ownerTeamId,
        public ?Account $parentAccount,
        public array $tags,
    ) {}
}
