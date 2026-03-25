<?php

namespace App\Http\Web\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'valueAmount' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
