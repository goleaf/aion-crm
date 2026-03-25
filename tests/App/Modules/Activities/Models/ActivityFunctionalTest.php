<?php

namespace Tests\App\Modules\Activities\Models;

use App\Modules\Activities\Models\Activity;
use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(Activity::class)]
#[Group('crm')]
class ActivityFunctionalTest extends FunctionalTestCase
{
    public function test_it_exposes_owner_attendees_and_related_record_relations(): void
    {
        // Arrange

        $owner = User::factory()->create();
        $attendee = User::factory()->create();
        $lead = Lead::factory()->create();

        $activity = Activity::factory()
            ->for($owner, 'owner')
            ->forRelatedTo($lead)
            ->create();

        $activity->attendees()->attach($attendee);

        // Act

        $ownerRelation = $activity->owner();
        $attendeesRelation = $activity->attendees();
        $relatedRelation = $activity->relatedTo();
        $activity->load(['owner', 'attendees', 'relatedTo']);

        // Assert

        $this->assertInstanceOf(BelongsTo::class, $ownerRelation);
        $this->assertInstanceOf(BelongsToMany::class, $attendeesRelation);
        $this->assertInstanceOf(MorphTo::class, $relatedRelation);
        $this->assertTrue($activity->owner->is($owner));
        $this->assertTrue($activity->attendees->firstOrFail()->is($attendee));
        $this->assertTrue($activity->relatedTo->is($lead));
    }
}
