<?php

namespace App\Http\Web\CRM\Requests;

use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'lead_source' => ['required', Rule::enum(LeadSourceEnum::class)],
            'status' => ['required', Rule::enum(LeadStatusEnum::class)->except([LeadStatusEnum::Converted])],
            'campaign_id' => ['nullable', 'integer', 'min:1'],
            'owner_id' => ['required', 'integer', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
