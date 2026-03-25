<?php

namespace App\Http\Web\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
