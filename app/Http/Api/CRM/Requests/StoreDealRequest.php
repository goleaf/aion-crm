<?php

namespace App\Http\Api\CRM\Requests;

use App\Modules\CRM\Enums\DealLostReasonEnum;
use App\Modules\CRM\Enums\DealSourceEnum;
use App\Modules\CRM\Enums\DealStageEnum;
use App\Modules\CRM\Enums\DealTypeEnum;
use App\Modules\CRM\Foundation\Enums\CurrencyCodeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDealRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'account_id' => ['required', 'uuid', Rule::exists('crm_accounts', 'id')],
            'contact_id' => ['nullable', 'uuid', Rule::exists('crm_contacts', 'id')],
            'owner_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'stage' => ['required', Rule::enum(DealStageEnum::class)],
            'amount' => ['required', 'decimal:0,2'],
            'currency' => ['required', Rule::enum(CurrencyCodeEnum::class)],
            'probability' => ['required', 'integer', 'between:0,100'],
            'close_date' => ['nullable', 'date'],
            'deal_type' => ['required', Rule::enum(DealTypeEnum::class)],
            'pipeline_id' => ['required', 'uuid', Rule::exists('crm_pipelines', 'id')],
            'lost_reason' => ['nullable', Rule::enum(DealLostReasonEnum::class)],
            'source' => ['nullable', Rule::enum(DealSourceEnum::class)],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $this->validateStageRules($validator);
            },
        ];
    }

    protected function validateStageRules(Validator $validator): void
    {
        $stage = $this->input('stage');
        $lostReason = $this->input('lost_reason');

        if ($stage === DealStageEnum::ClosedLost->value && $lostReason === null) {
            $validator->errors()->add('lost_reason', 'Lost reason is required when closing a deal as lost.');
        }

        if ($stage !== DealStageEnum::ClosedLost->value && $lostReason !== null) {
            $validator->errors()->add('lost_reason', 'Lost reason can only be set when the deal is closed lost.');
        }
    }
}
