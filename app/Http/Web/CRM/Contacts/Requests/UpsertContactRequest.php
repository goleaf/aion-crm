<?php

namespace App\Http\Web\CRM\Contacts\Requests;

use App\Modules\CRM\Enums\ContactLeadSourceEnum;
use App\Modules\CRM\Enums\ContactPreferredChannelEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpsertContactRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return self::rulesFor();
    }

    /**
     * @return array<string, mixed>
     */
    public static function rulesFor(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'account_id' => ['nullable', 'exists:crm_accounts,id'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'lead_source' => ['required', Rule::enum(ContactLeadSourceEnum::class)],
            'do_not_contact' => ['required', 'boolean'],
            'birthday' => ['nullable', 'date'],
            'preferred_channel' => ['required', Rule::enum(ContactPreferredChannelEnum::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
