<?php

namespace App\Modules\Activities\Actions;

use App\Modules\Activities\DataTransferObjects\CreateActivityData;
use App\Modules\Activities\Models\Activity;

/** @final */
class CreateActivityAction
{
    public function execute(CreateActivityData $data): Activity
    {
        $activity = Activity::query()->create([
            'type' => $data->type,
            'subject' => $data->subject,
            'description' => $data->description,
            'status' => $data->status,
            'priority' => $data->priority,
            'due_date' => $data->dueDate,
            'duration_minutes' => $data->durationMinutes,
            'outcome' => $data->outcome,
            'related_to_type' => $data->relatedTo?->getMorphClass(),
            'related_to_id' => $data->relatedTo?->getKey(),
            'owner_id' => $data->owner->getKey(),
            'reminder_at' => $data->reminderAt,
        ]);

        if ($data->attendees !== []) {
            $activity->attendees()->sync(
                collect($data->attendees)->map->getKey()->all(),
            );
        }

        return $activity->load(['owner', 'attendees', 'relatedTo']);
    }
}
