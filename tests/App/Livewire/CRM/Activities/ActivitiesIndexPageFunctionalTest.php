<?php

namespace Tests\App\Livewire\CRM\Activities;

use App\Livewire\CRM\Activities\ActivitiesIndexPage;
use App\Modules\CRM\Enums\ActivityStatusEnum;
use App\Modules\CRM\Models\Activity;
use App\Modules\Shared\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(ActivitiesIndexPage::class)]
#[Group('crm')]
class ActivitiesIndexPageFunctionalTest extends FunctionalTestCase
{
    public function test_it_creates_lists_and_filters_due_activities(): void
    {
        // Arrange

        $organizer = User::factory()->create();
        $attendee = User::factory()->create();

        Activity::factory()
            ->for($organizer, 'organizer')
            ->create([
                'title' => 'Quarterly review',
                'start_at' => now()->addMinutes(20),
                'end_at' => now()->addMinutes(80),
                'reminder_minutes' => 30,
            ]);

        $this->actingAs($organizer, 'web');

        // Act

        $component = Livewire::actingAs($organizer)
            ->test(ActivitiesIndexPage::class)
            ->set('title', 'Discovery call')
            ->set('type', 'meeting')
            ->set('startAt', now()->addDay()->format('Y-m-d\\TH:i'))
            ->set('endAt', now()->addDay()->addHour()->format('Y-m-d\\TH:i'))
            ->set('location', 'Vilnius HQ')
            ->set('description', 'Discuss requirements and budget.')
            ->set('attendeeIds', [$attendee->id])
            ->set('reminderMinutes', 15)
            ->set('recurrence', 'none')
            ->set('status', 'scheduled')
            ->call('save')
            ->set('reminderFilter', 'due');

        // Assert

        $component
            ->assertSee('Quarterly review')
            ->assertDontSee('Discovery call');

        $this->assertDatabaseHas('activities', [
            'title' => 'Discovery call',
            'organizer_id' => $organizer->id,
            'status' => ActivityStatusEnum::Scheduled->value,
        ]);

        $this->assertDatabaseCount('activity_attendees', 1);
    }
}
