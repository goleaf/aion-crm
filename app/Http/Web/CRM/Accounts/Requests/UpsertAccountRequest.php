<?php

namespace App\Http\Web\CRM\Accounts\Requests;

use App\Modules\CRM\Enums\AccountIndustryEnum;
use App\Modules\CRM\Enums\AccountTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpsertAccountRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['required', Rule::enum(AccountIndustryEnum::class)],
            'type' => ['required', Rule::enum(AccountTypeEnum::class)],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'billing_address' => ['nullable', 'array'],
            'billing_address.line_1' => ['nullable', 'string', 'max:255'],
            'billing_address.line_2' => ['nullable', 'string', 'max:255'],
            'billing_address.city' => ['nullable', 'string', 'max:255'],
            'billing_address.state_or_province' => ['nullable', 'string', 'max:255'],
            'billing_address.postal_code' => ['nullable', 'string', 'max:255'],
            'billing_address.country_code' => ['nullable', 'string', 'size:2'],
            'shipping_address' => ['nullable', 'array'],
            'shipping_address.line_1' => ['nullable', 'string', 'max:255'],
            'shipping_address.line_2' => ['nullable', 'string', 'max:255'],
            'shipping_address.city' => ['nullable', 'string', 'max:255'],
            'shipping_address.state_or_province' => ['nullable', 'string', 'max:255'],
            'shipping_address.postal_code' => ['nullable', 'string', 'max:255'],
            'shipping_address.country_code' => ['nullable', 'string', 'size:2'],
            'annual_revenue' => ['nullable', 'numeric', 'min:0'],
            'employee_count' => ['nullable', 'integer', 'min:0'],
            'owner_id' => ['nullable', 'exists:users,id'],
            'parent_account_id' => ['nullable', 'exists:crm_accounts,id'],
            'tags_input' => ['nullable', 'string'],
        ];
    }
}
