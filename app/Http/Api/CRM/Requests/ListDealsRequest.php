<?php

namespace App\Http\Api\CRM\Requests;

use App\Modules\CRM\Enums\DealStageEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListDealsRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:255'],
            'pipeline_id' => ['nullable', 'uuid', Rule::exists('crm_pipelines', 'id')],
            'owner_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'stage' => ['nullable', Rule::enum(DealStageEnum::class)],
            'status' => ['nullable', Rule::in(['open', 'closed'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'per_page' => $this->input('per_page', 15),
        ]);
    }
}
