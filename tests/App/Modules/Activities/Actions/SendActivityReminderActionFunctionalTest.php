<?php

namespace Tests\App\Modules\Activities\Actions;

use App\Modules\Activities\Actions\SendActivityReminderAction;
use App\Modules\Activities\Models\Activity;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(SendActivityReminderAction::class)]
#[Group('crm')]
class SendActivityReminderActionFunctionalTest extends FunctionalTestCase
{
    public function test_it_sends_a_database_notification_and_marks_the_activity_as_reminded(): void
    {
        // Arrange

        $owner = User::factory()->create();

        $activity = Activity::factory()
            ->for($owner, 'owner')
            ->dueForReminder()
            ->create();

        // Act

        resolve(SendActivityReminderAction::class)->execute($activity);

        // Assert

        $activity->refresh();

        $this->assertNotNull($activity->reminded_at);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertCount(1, $owner->notifications);
    }

    public function test_it_does_not_send_the_same_reminder_twice(): void
    {
        // Arrange

        $owner = User::factory()->create();

        $activity = Activity::factory()
            ->for($owner, 'owner')
            ->dueForReminder()
            ->create([
                'reminded_at' => now()->subMinute(),
            ]);

        // Act

        resolve(SendActivityReminderAction::class)->execute($activity);

        // Assert

        $this->assertDatabaseCount('notifications', 0);
    }
}
