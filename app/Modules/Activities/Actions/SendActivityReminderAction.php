<?php

namespace App\Modules\Activities\Actions;

use App\Modules\Activities\Models\Activity;
use App\Modules\Activities\Notifications\ActivityReminderNotification;

/** @final */
class SendActivityReminderAction
{
    public function execute(Activity $activity): void
    {
        if (! $activity->shouldSendReminder()) {
            return;
        }

        $activity->owner->notify(new ActivityReminderNotification($activity));

        $activity->forceFill([
            'reminded_at' => now(),
        ])->save();
    }
}
