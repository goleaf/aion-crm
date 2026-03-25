<?php

namespace Tests\App\Livewire\CRM\Leads;

use App\Livewire\CRM\Leads\LeadsIndexPage;
use App\Modules\CRM\Enums\LeadRatingEnum;
use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;
use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(LeadsIndexPage::class)]
#[Group('crm')]
class LeadsIndexPageFunctionalTest extends FunctionalTestCase
{
    public function test_it_lists_existing_leads_and_filters_them_by_status(): void
    {
        // Arrange

        $user = User::factory()->create();

        Lead::factory()->create([
            'owner_id' => $user->id,
            'first_name' => 'Alice',
            'status' => LeadStatusEnum::Qualified,
            'rating' => LeadRatingEnum::Hot,
        ]);

        Lead::factory()->create([
            'owner_id' => $user->id,
            'first_name' => 'Bob',
            'status' => LeadStatusEnum::New,
            'rating' => LeadRatingEnum::Cold,
        ]);

        $this->actingAs($user, 'web');

        // Act

        $component = Livewire::test(LeadsIndexPage::class)
            ->set('statusFilter', LeadStatusEnum::Qualified->value);

        // Assert

        $component
            ->assertSee('Alice')
            ->assertDontSee('Bob');
    }

    public function test_it_creates_and_updates_a_lead_from_the_page(): void
    {
        // Arrange

        $user = User::factory()->create();

        $this->actingAs($user, 'web');

        // Act

        $component = Livewire::test(LeadsIndexPage::class)
            ->set('first_name', 'Dana')
            ->set('last_name', 'Buyer')
            ->set('company', 'Delta LLC')
            ->set('email', 'dana@delta.test')
            ->set('phone')
            ->set('lead_source', LeadSourceEnum::InternalForm->value)
            ->set('status', LeadStatusEnum::New->value)
            ->set('owner_id', $user->id)
            ->set('description', 'Requested a product demo.')
            ->call('save');

        $createdLead = Lead::query()->where('email', 'dana@delta.test')->firstOrFail();

        $component
            ->call('editLead', $createdLead->lead_id)
            ->set('status', LeadStatusEnum::Qualified->value)
            ->set('phone', '+37060000005')
            ->call('save');

        // Assert

        $component->assertHasNoErrors();

        $createdLead->refresh();

        $this->assertSame(LeadStatusEnum::Qualified, $createdLead->status);
        $this->assertSame('+37060000005', $createdLead->phone);
        $this->assertSame(LeadRatingEnum::Hot, $createdLead->rating);
    }
}
