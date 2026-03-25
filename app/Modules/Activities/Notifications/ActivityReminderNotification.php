<?php

namespace App\Modules\Activities\Notifications;

use App\Modules\Activities\Models\Activity;
use App\Modules\Shared\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ActivityReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Activity $activity,
    ) {}

    /**
     * @return list<string>
     */
    public function via(User $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        return [
            'activity_id' => $this->activity->getKey(),
            'activity_type' => $this->activity->type->value,
            'subject' => $this->activity->subject,
            'due_date' => $this->activity->due_date?->toIso8601String(),
        ];
    }
}
