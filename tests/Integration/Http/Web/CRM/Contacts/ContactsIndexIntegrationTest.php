<?php

namespace Tests\Integration\Http\Web\CRM\Contacts;

use App\Livewire\CRM\Contacts\ContactsIndexPage;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(ContactsIndexPage::class)]
#[Group('crm')]
class ContactsIndexIntegrationTest extends FunctionalTestCase
{
    public function test_it_requires_authentication(): void
    {
        // Arrange

        // Act

        $response = $this->get('/crm/contacts');

        // Assert

        $response->assertRedirect(route('login'));
    }

    public function test_it_displays_the_contacts_registry_page_for_authenticated_users(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $user = User::factory()->create();

        CrmUserProfile::factory()->for($user)->for($team, 'primaryTeam')->admin()->create();

        Contact::factory()->create([
            'first_name' => 'Ava',
            'last_name' => 'Stone',
        ]);

        // Act

        $response = $this->actingAs($user, 'web')->get('/crm/contacts');

        // Assert

        $response
            ->assertOk()
            ->assertSee('Search contacts')
            ->assertSee('Create contact')
            ->assertSee('Ava')
            ->assertSee('Stone');
    }

    public function test_it_integrates(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $user = User::factory()->create();

        CrmUserProfile::factory()->for($user)->for($team, 'primaryTeam')->admin()->create();

        // Act

        $response = $this->actingAs($user, 'web')->get('/crm/contacts');

        // Assert

        $response->assertOk();
    }
}
