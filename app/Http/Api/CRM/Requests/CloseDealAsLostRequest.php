<?php

namespace App\Http\Api\CRM\Requests;

use App\Modules\CRM\Enums\DealLostReasonEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CloseDealAsLostRequest extends FormRequest
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
            'lost_reason' => ['required', Rule::enum(DealLostReasonEnum::class)],
        ];
    }
}
