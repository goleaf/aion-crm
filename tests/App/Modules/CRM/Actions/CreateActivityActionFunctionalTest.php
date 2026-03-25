<?php

namespace Tests\App\Modules\CRM\Actions;

use App\Modules\CRM\Actions\CreateActivityAction;
use App\Modules\CRM\DataTransferObjects\UpsertActivityData;
use App\Modules\CRM\Enums\ActivityRecurrenceEnum;
use App\Modules\CRM\Enums\ActivityStatusEnum;
use App\Modules\CRM\Enums\ActivityTypeEnum;
use App\Modules\CRM\Models\Activity;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(CreateActivityAction::class)]
#[CoversClass(UpsertActivityData::class)]
#[CoversClass(Activity::class)]
#[Group('crm')]
class CreateActivityActionFunctionalTest extends FunctionalTestCase
{
    public function test_it_creates_a_scheduled_activity_with_internal_attendees(): void
    {
        // Arrange

        $organizer = User::factory()->create();
        $attendee = User::factory()->create();

        $data = new UpsertActivityData(
            title: 'Discovery call',
            type: ActivityTypeEnum::Meeting,
            startAt: now()->addDay(),
            endAt: now()->addDay()->addHour(),
            allDay: false,
            location: 'Vilnius HQ',
            description: 'Discuss requirements and budget.',
            organizer: $organizer,
            attendeeIds: [$attendee->id],
            reminderMinutes: 15,
            recurrence: ActivityRecurrenceEnum::None,
            status: ActivityStatusEnum::Scheduled,
        );

        // Act

        $activity = resolve(CreateActivityAction::class)->execute($data);

        // Assert

        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertModelExists($activity);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->getKey(),
            'title' => 'Discovery call',
            'organizer_id' => $organizer->id,
            'type' => ActivityTypeEnum::Meeting->value,
            'status' => ActivityStatusEnum::Scheduled->value,
            'recurrence' => ActivityRecurrenceEnum::None->value,
            'reminder_minutes' => 15,
        ]);

        $this->assertDatabaseHas('activity_attendees', [
            'activity_id' => $activity->getKey(),
            'user_id' => $attendee->id,
        ]);
    }
}
