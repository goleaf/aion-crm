<?php

namespace Tests\Integration\Http\Web\CRM\Accounts;

use App\Livewire\CRM\Accounts\AccountsIndexPage;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(AccountsIndexPage::class)]
#[Group('crm')]
class AccountsIndexIntegrationTest extends FunctionalTestCase
{
    public function test_it_requires_authentication(): void
    {
        // Arrange

        // Act

        $response = $this->get('/crm/accounts');

        // Assert

        $response->assertRedirect(route('login'));
    }

    public function test_it_displays_the_accounts_registry_page_for_authenticated_users(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $user = User::factory()->create();

        CrmUserProfile::factory()->for($user)->for($team, 'primaryTeam')->admin()->create();

        Account::factory()->create(['name' => 'Acme North']);

        // Act

        $response = $this->actingAs($user, 'web')->get('/crm/accounts');

        // Assert

        $response
            ->assertOk()
            ->assertSee('Search accounts')
            ->assertSee('Create account')
            ->assertSee('Acme North');
    }

    public function test_it_integrates(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $user = User::factory()->create();

        CrmUserProfile::factory()->for($user)->for($team, 'primaryTeam')->admin()->create();

        // Act

        $response = $this->actingAs($user, 'web')->get('/crm/accounts');

        // Assert

        $response->assertOk();
    }
}
