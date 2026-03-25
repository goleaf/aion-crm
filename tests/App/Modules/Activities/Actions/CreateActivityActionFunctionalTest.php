<?php

namespace Tests\App\Modules\Activities\Actions;

use App\Modules\Activities\Actions\CreateActivityAction;
use App\Modules\Activities\DataTransferObjects\CreateActivityData;
use App\Modules\Activities\Enums\ActivityPriorityEnum;
use App\Modules\Activities\Enums\ActivityStatusEnum;
use App\Modules\Activities\Enums\ActivityTypeEnum;
use App\Modules\Activities\Models\Activity;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\Deal;
use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(CreateActivityAction::class)]
#[CoversClass(CreateActivityData::class)]
#[Group('crm')]
class CreateActivityActionFunctionalTest extends FunctionalTestCase
{
    #[DataProvider('relatedModelsProvider')]
    public function test_it_creates_an_activity_for_any_supported_related_record(string $relatedModelClass): void
    {
        // Arrange

        $owner = User::factory()->create();
        $attendee = User::factory()->create();
        $relatedRecord = $relatedModelClass::factory()->create();

        $data = new CreateActivityData(
            type: ActivityTypeEnum::Meeting,
            subject: 'Discovery Call',
            description: 'Discuss requirements and next steps.',
            status: ActivityStatusEnum::Planned,
            priority: ActivityPriorityEnum::High,
            dueDate: now()->addDay(),
            durationMinutes: 45,
            outcome: null,
            relatedTo: $relatedRecord,
            owner: $owner,
            attendees: [$attendee],
            reminderAt: now()->addHour(),
        );

        // Act

        $activity = resolve(CreateActivityAction::class)->execute($data);

        // Assert

        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertModelExists($activity);
        $this->assertTrue($activity->owner->is($owner));
        $this->assertTrue($activity->relatedTo->is($relatedRecord));
        $this->assertCount(1, $activity->attendees);
        $this->assertTrue($activity->attendees->firstOrFail()->is($attendee));
    }

    public static function relatedModelsProvider(): Generator
    {
        yield 'account' => [
            'relatedModelClass' => Account::class,
        ];

        yield 'contact' => [
            'relatedModelClass' => Contact::class,
        ];

        yield 'lead' => [
            'relatedModelClass' => Lead::class,
        ];

        yield 'deal' => [
            'relatedModelClass' => Deal::class,
        ];
    }
}
