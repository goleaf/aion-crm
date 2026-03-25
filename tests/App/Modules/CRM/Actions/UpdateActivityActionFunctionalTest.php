<?php

namespace Tests\App\Modules\CRM\Actions;

use App\Modules\CRM\Actions\UpdateActivityAction;
use App\Modules\CRM\DataTransferObjects\UpsertActivityData;
use App\Modules\CRM\Enums\ActivityRecurrenceEnum;
use App\Modules\CRM\Enums\ActivityStatusEnum;
use App\Modules\CRM\Enums\ActivityTypeEnum;
use App\Modules\CRM\Models\Activity;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(UpdateActivityAction::class)]
#[CoversClass(UpsertActivityData::class)]
#[CoversClass(Activity::class)]
#[Group('crm')]
class UpdateActivityActionFunctionalTest extends FunctionalTestCase
{
    public function test_it_updates_an_existing_activity_and_syncs_attendees(): void
    {
        // Arrange

        $organizer = User::factory()->create();
        $oldAttendee = User::factory()->create();
        $newAttendee = User::factory()->create();

        $activity = Activity::factory()
            ->for($organizer, 'organizer')
            ->hasAttached($oldAttendee, [], 'attendees')
            ->create([
                'status' => ActivityStatusEnum::Scheduled,
            ]);

        $data = new UpsertActivityData(
            title: 'Demo follow-up',
            type: ActivityTypeEnum::FollowUp,
            startAt: now()->addDays(2),
            endAt: now()->addDays(2)->addMinutes(30),
            allDay: false,
            location: 'Remote',
            description: 'Send commercial proposal.',
            organizer: $organizer,
            attendeeIds: [$newAttendee->id],
            reminderMinutes: 60,
            recurrence: ActivityRecurrenceEnum::Monthly,
            status: ActivityStatusEnum::Completed,
        );

        // Act

        $updatedActivity = resolve(UpdateActivityAction::class)->execute($activity, $data);

        // Assert

        $this->assertSame('Demo follow-up', $updatedActivity->title);
        $this->assertSame(ActivityTypeEnum::FollowUp, $updatedActivity->type);
        $this->assertSame(ActivityStatusEnum::Completed, $updatedActivity->status);
        $this->assertSame(ActivityRecurrenceEnum::Monthly, $updatedActivity->recurrence);

        $this->assertDatabaseHas('activity_attendees', [
            'activity_id' => $activity->getKey(),
            'user_id' => $newAttendee->id,
        ]);

        $this->assertDatabaseMissing('activity_attendees', [
            'activity_id' => $activity->getKey(),
            'user_id' => $oldAttendee->id,
        ]);
    }
}
