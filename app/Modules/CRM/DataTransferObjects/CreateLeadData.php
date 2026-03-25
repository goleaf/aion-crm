<?php

namespace App\Modules\CRM\DataTransferObjects;

use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;
use App\Modules\Shared\Models\User;

final readonly class CreateLeadData
{
    public function __construct(
        public string $firstName,
        public ?string $lastName,
        public ?string $company,
        public ?string $email,
        public ?string $phone,
        public LeadSourceEnum $leadSource,
        public LeadStatusEnum $status,
        public ?int $campaignId,
        public User $owner,
        public ?string $description,
    ) {}

    /**
     * @param  array{
     *     first_name: string,
     *     last_name?: string|null,
     *     company?: string|null,
     *     email?: string|null,
     *     phone?: string|null,
     *     lead_source: string,
     *     status: string,
     *     campaign_id?: int|null,
     *     owner_id: int,
     *     description?: string|null
     * }  $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            firstName: $validated['first_name'],
            lastName: $validated['last_name'] ?? null,
            company: $validated['company'] ?? null,
            email: $validated['email'] ?? null,
            phone: $validated['phone'] ?? null,
            leadSource: LeadSourceEnum::from($validated['lead_source']),
            status: LeadStatusEnum::from($validated['status']),
            campaignId: $validated['campaign_id'] ?? null,
            owner: User::query()->findOrFail($validated['owner_id']),
            description: $validated['description'] ?? null,
        );
    }
}
