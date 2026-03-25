<?php

namespace App\Modules\CRM\DataTransferObjects;

use App\Modules\CRM\Enums\ContactLeadSourceEnum;
use App\Modules\CRM\Enums\ContactPreferredChannelEnum;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\Shared\Models\User;

final readonly class UpdateContactData
{
    public function __construct(
        public Contact $contact,
        public string $firstName,
        public string $lastName,
        public ?string $email,
        public ?string $phone,
        public ?string $mobile,
        public ?string $jobTitle,
        public ?string $department,
        public ?Account $account,
        public ?User $owner,
        public ?int $ownerTeamId,
        public ContactLeadSourceEnum $leadSource,
        public bool $doNotContact,
        public ?string $birthday,
        public ContactPreferredChannelEnum $preferredChannel,
        public ?string $notes,
    ) {}
}
