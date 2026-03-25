<?php

namespace Tests\App\Modules\CRM\Models;

use App\Modules\CRM\Enums\ActivityRecurrenceEnum;
use App\Modules\CRM\Enums\ActivityStatusEnum;
use App\Modules\CRM\Enums\ActivityTypeEnum;
use App\Modules\CRM\Foundation\Support\UuidPolicy;
use App\Modules\CRM\Models\Activity;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(Activity::class)]
#[CoversClass(ActivityTypeEnum::class)]
#[CoversClass(ActivityStatusEnum::class)]
#[CoversClass(ActivityRecurrenceEnum::class)]
#[Group('crm')]
class ActivityFunctionalTest extends FunctionalTestCase
{
    public function test_it_uses_uuid_primary_keys_and_resolves_organizer_and_attendees(): void
    {
        // Arrange

        $organizer = User::factory()->create();
        $attendee = User::factory()->create();

        $activity = Activity::factory()
            ->for($organizer, 'organizer')
            ->hasAttached($attendee, [], 'attendees')
            ->create([
                'type' => ActivityTypeEnum::Meeting,
                'status' => ActivityStatusEnum::Scheduled,
                'recurrence' => ActivityRecurrenceEnum::Weekly,
                'reminder_minutes' => 30,
            ]);

        // Act

        $activity->load(['organizer', 'attendees']);

        // Assert

        $this->assertTrue(UuidPolicy::isValid($activity->getKey()));
        $this->assertSame($organizer->id, $activity->organizer?->id);
        $this->assertCount(1, $activity->attendees);
        $this->assertSame($attendee->id, $activity->attendees->sole()->id);
        $this->assertSame(ActivityTypeEnum::Meeting, $activity->type);
        $this->assertSame(ActivityStatusEnum::Scheduled, $activity->status);
        $this->assertSame(ActivityRecurrenceEnum::Weekly, $activity->recurrence);
    }
}
