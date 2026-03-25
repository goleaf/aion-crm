<?php

namespace Tests\Integration\Http\Web\CRM\Activities;

use App\Livewire\CRM\Activities\ActivitiesIndexPage;
use App\Modules\CRM\Models\Activity;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(ActivitiesIndexPage::class)]
#[Group('crm')]
class ActivitiesIndexIntegrationTest extends FunctionalTestCase
{
    public function test_it_requires_authentication(): void
    {
        // Arrange

        // Act

        $response = $this->get(route('crm.activities.index'));

        // Assert

        $response->assertRedirect(route('login'));
    }

    public function test_it_displays_the_activities_page_for_authenticated_users(): void
    {
        // Arrange

        $organizer = User::factory()->create();

        Activity::factory()
            ->for($organizer, 'organizer')
            ->create([
                'title' => 'Pipeline review',
            ]);

        // Act

        $response = $this->actingAs($organizer, 'web')->get(route('crm.activities.index'));

        // Assert

        $response
            ->assertOk()
            ->assertSeeLivewire(ActivitiesIndexPage::class)
            ->assertSeeText('Calendar & Scheduler')
            ->assertSee('Pipeline review');
    }

    public function test_it_integrates(): void
    {
        // Arrange

        $user = User::factory()->create();

        // Act

        $response = $this->actingAs($user, 'web')->get(route('crm.activities.index'));

        // Assert

        $response->assertOk();
    }
}
