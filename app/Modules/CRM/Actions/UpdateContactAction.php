<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\UpdateContactData;
use App\Modules\CRM\Models\Contact;

/** @final */
class UpdateContactAction
{
    public function execute(UpdateContactData $data): Contact
    {
        $data->contact->fill([
            'first_name' => trim($data->firstName),
            'last_name' => trim($data->lastName),
            'email' => $this->normalizeNullableString($data->email),
            'phone' => $this->normalizeNullableString($data->phone),
            'mobile' => $this->normalizeNullableString($data->mobile),
            'job_title' => $this->normalizeNullableString($data->jobTitle),
            'department' => $this->normalizeNullableString($data->department),
            'account_id' => $data->account?->getKey(),
            'owner_id' => $data->owner?->getKey(),
            'owner_team_id' => $data->ownerTeamId,
            'lead_source' => $data->leadSource,
            'do_not_contact' => $data->doNotContact,
            'birthday' => $data->birthday,
            'preferred_channel' => $data->preferredChannel,
            'notes' => $this->normalizeNullableString($data->notes),
        ]);

        $data->contact->save();

        return $data->contact->refresh();
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
