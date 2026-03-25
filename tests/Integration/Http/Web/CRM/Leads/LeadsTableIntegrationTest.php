<?php

namespace Tests\Integration\Http\Web\CRM\Leads;

use App\Livewire\CRM\Leads\LeadsIndexPage;
use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(LeadsIndexPage::class)]
#[Group('crm')]
class LeadsTableIntegrationTest extends FunctionalTestCase
{
    public function test_it_requires_authentication(): void
    {
        // Arrange

        // Act

        $response = $this->get('/leads');

        // Assert

        $response->assertRedirect(route('login'));
    }

    public function test_it_displays_the_leads_page_for_authenticated_users(): void
    {
        // Arrange

        $user = User::factory()->create();

        Lead::factory()->create([
            'first_name' => 'Inbound',
            'last_name' => 'Prospect',
            'owner_id' => $user->id,
        ]);

        // Act

        $response = $this->actingAs($user, 'web')->get('/leads');

        // Assert

        $response
            ->assertOk()
            ->assertSeeLivewire(LeadsIndexPage::class)
            ->assertSee('Leads')
            ->assertSee('Inbound Prospect');
    }
}
