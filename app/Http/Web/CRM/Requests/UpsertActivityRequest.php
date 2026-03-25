<?php

namespace App\Http\Web\CRM\Requests;

use App\Modules\Activities\Enums\ActivityPriorityEnum;
use App\Modules\Activities\Enums\ActivityStatusEnum;
use App\Modules\Activities\Enums\ActivityTypeEnum;
use App\Modules\CRM\Enums\CrmRecordTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(ActivityTypeEnum::class)],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(ActivityStatusEnum::class)],
            'priority' => ['required', Rule::enum(ActivityPriorityEnum::class)],
            'dueDate' => ['nullable', 'date'],
            'durationMinutes' => ['nullable', 'integer', 'min:1'],
            'outcome' => ['nullable', 'string'],
            'relatedToType' => ['nullable', Rule::enum(CrmRecordTypeEnum::class)],
            'relatedToId' => ['nullable', 'string'],
            'attendeeIds' => ['array'],
            'attendeeIds.*' => ['integer', 'exists:users,id'],
            'reminderAt' => ['nullable', 'date'],
        ];
    }
}
