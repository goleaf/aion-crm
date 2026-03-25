<?php

namespace App\Console\Commands;

use App\Modules\Activities\Actions\SendActivityReminderAction;
use App\Modules\Activities\Models\Activity;
use Illuminate\Console\Command;

class SendActivityRemindersCommand extends Command
{
    protected $signature = 'app:activities:send-reminders';

    protected $description = 'Send due activity reminder notifications.';

    public function handle(SendActivityReminderAction $sendActivityReminderAction): int
    {
        Activity::query()
            ->with('owner')
            ->dueForReminder()
            ->cursor()
            ->each(fn (Activity $activity) => $sendActivityReminderAction->execute($activity));

        return self::SUCCESS;
    }
}
