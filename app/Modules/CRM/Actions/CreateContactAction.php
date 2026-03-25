<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\CreateContactData;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Support\CrmUserProfileFactory;
use App\Modules\Shared\Models\User;

/** @final */
class CreateContactAction
{
    public function execute(CreateContactData $data): Contact
    {
        $teamId = $data->owner instanceof User ? CrmUserProfileFactory::forUser($data->owner)->primary_team_id : null;

        return Contact::query()->create([
            'first_name' => trim($data->firstName),
            'last_name' => trim($data->lastName),
            'email' => $this->normalizeNullableString($data->email),
            'phone' => $this->normalizeNullableString($data->phone),
            'mobile' => $this->normalizeNullableString($data->mobile),
            'job_title' => $this->normalizeNullableString($data->jobTitle),
            'department' => $this->normalizeNullableString($data->department),
            'account_id' => $data->account?->getKey(),
            'owner_id' => $data->owner?->getKey(),
            'team_id' => $teamId,
            'lead_source' => $data->leadSource,
            'do_not_contact' => $data->doNotContact,
            'birthday' => $data->birthday,
            'preferred_channel' => $data->preferredChannel,
            'notes' => $this->normalizeNullableString($data->notes),
        ]);
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
