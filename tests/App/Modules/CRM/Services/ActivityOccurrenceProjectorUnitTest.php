<?php

namespace Tests\App\Modules\CRM\Services;

use App\Modules\CRM\Enums\ActivityRecurrenceEnum;
use App\Modules\CRM\Enums\ActivityStatusEnum;
use App\Modules\CRM\Enums\ActivityTypeEnum;
use App\Modules\CRM\Services\ActivityOccurrenceProjector;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(ActivityOccurrenceProjector::class)]
#[Group('crm')]
class ActivityOccurrenceProjectorUnitTest extends UnitTestCase
{
    public function test_it_projects_daily_occurrences_within_a_requested_range(): void
    {
        // Arrange

        $projector = new ActivityOccurrenceProjector;

        $activity = new class
        {
            public string $id = '0195d4a2-1cbf-74a4-96f6-c5157fcb7f31';

            public string $title = 'Daily standup';

            public ActivityTypeEnum $type = ActivityTypeEnum::Reminder;

            public CarbonImmutable $start_at;

            public CarbonImmutable $end_at;

            public bool $all_day = false;

            public ActivityRecurrenceEnum $recurrence = ActivityRecurrenceEnum::Daily;

            public ActivityStatusEnum $status = ActivityStatusEnum::Scheduled;

            public function __construct()
            {
                $this->start_at = CarbonImmutable::parse('2026-03-25 09:00:00');
                $this->end_at = CarbonImmutable::parse('2026-03-25 09:15:00');
            }
        };

        // Act

        $occurrences = $projector->projectForRange(
            collect([$activity]),
            CarbonImmutable::parse('2026-03-26 00:00:00'),
            CarbonImmutable::parse('2026-03-28 23:59:59'),
        );

        // Assert

        $this->assertCount(3, $occurrences);
        $this->assertSame('2026-03-26 09:00:00', $occurrences[0]['occurs_at']->format('Y-m-d H:i:s'));
        $this->assertSame('2026-03-27 09:00:00', $occurrences[1]['occurs_at']->format('Y-m-d H:i:s'));
        $this->assertSame('2026-03-28 09:00:00', $occurrences[2]['occurs_at']->format('Y-m-d H:i:s'));
    }
}
