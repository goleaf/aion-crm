<?php

namespace Tests\App\Livewire\CRM\Contacts;

use App\Livewire\CRM\Contacts\ContactsIndexPage;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\Shared\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(ContactsIndexPage::class)]
#[Group('crm')]
class ContactsIndexPageFunctionalTest extends FunctionalTestCase
{
    public function test_it_renders_visible_contacts_and_filters_by_search_term(): void
    {
        // Arrange

        $team = CrmTeam::factory()->create();
        $admin = User::factory()->create();

        CrmUserProfile::factory()->for($admin)->for($team, 'primaryTeam')->admin()->create();

        Contact::factory()->create([
            'first_name' => 'Ava',
            'last_name' => 'Stone',
        ]);

        Contact::factory()->create([
            'first_name' => 'Liam',
            'last_name' => 'Brooks',
        ]);

        // Act

        $component = Livewire::actingAs($admin)
            ->test(ContactsIndexPage::class)
            ->set('search', 'Ava');

        // Assert

        $component
            ->assertSee('Ava')
            ->assertViewHas('contacts', fn ($contacts): bool => $contacts->pluck('first_name')->values()->all() === ['Ava']);
    }
}
