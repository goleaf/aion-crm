<?php

namespace App\Modules\CRM\Actions;

use App\Modules\CRM\DataTransferObjects\UpsertActivityData;
use App\Modules\CRM\Models\Activity;

/** @final */
class CreateActivityAction
{
    public function execute(UpsertActivityData $data): Activity
    {
        $activity = Activity::query()->create([
            'title' => $data->title,
            'type' => $data->type,
            'start_at' => $data->startAt,
            'end_at' => $data->endAt,
            'all_day' => $data->allDay,
            'location' => $data->location,
            'description' => $data->description,
            'organizer_id' => $data->organizer->getKey(),
            'reminder_minutes' => $data->reminderMinutes,
            'recurrence' => $data->recurrence,
            'status' => $data->status,
        ]);

        $activity->attendees()->sync($data->attendeeIds);

        return $activity->load(['organizer', 'attendees']);
    }
}
