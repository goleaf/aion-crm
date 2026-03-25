<?php

namespace Tests\App\Livewire\CRM\Accounts;

use App\Livewire\CRM\Accounts\AccountsIndexPage;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\Shared\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(AccountsIndexPage::class)]
#[Group('crm')]
class AccountsIndexPageFunctionalTest extends FunctionalTestCase
{
    public function test_it_renders_visible_accounts_and_filters_by_search_term(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $admin = User::factory()->create();

        CrmUserProfile::factory()->for($admin)->for($team, 'primaryTeam')->admin()->create();

        Account::factory()->create(['name' => 'Acme North']);
        Account::factory()->create(['name' => 'Beacon Labs']);

        // Act

        $component = Livewire::actingAs($admin)
            ->test(AccountsIndexPage::class)
            ->set('search', 'Acme');

        // Assert

        $component
            ->assertSee('Acme North')
            ->assertViewHas('accounts', fn ($accounts): bool => $accounts->pluck('name')->values()->all() === ['Acme North']);
    }
}
