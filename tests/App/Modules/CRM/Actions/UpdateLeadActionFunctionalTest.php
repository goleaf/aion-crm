<?php

namespace Tests\App\Modules\CRM\Actions;

use App\Modules\CRM\Actions\UpdateLeadAction;
use App\Modules\CRM\DataTransferObjects\UpdateLeadData;
use App\Modules\CRM\Enums\LeadRatingEnum;
use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;
use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(UpdateLeadAction::class)]
#[CoversClass(UpdateLeadData::class)]
#[Group('crm')]
class UpdateLeadActionFunctionalTest extends FunctionalTestCase
{
    public function test_it_updates_a_lead_and_recalculates_its_score(): void
    {
        // Arrange

        $owner = User::factory()->create();
        $replacementOwner = User::factory()->create();

        $lead = Lead::factory()->create([
            'owner_id' => $owner->id,
            'first_name' => 'Bob',
            'email' => 'bob@example.test',
            'phone' => null,
            'lead_source' => LeadSourceEnum::ColdCall,
            'status' => LeadStatusEnum::New,
            'score' => 28,
            'rating' => LeadRatingEnum::Cold,
        ]);

        $data = new UpdateLeadData(
            lead: $lead,
            firstName: 'Bobby',
            lastName: 'Qualified',
            company: 'Northwind',
            email: 'bobby@example.test',
            phone: '+37060000004',
            leadSource: LeadSourceEnum::Referral,
            status: LeadStatusEnum::Qualified,
            campaignId: null,
            owner: $replacementOwner,
            description: 'Requested pricing and implementation details.',
        );

        // Act

        $updatedLead = resolve(UpdateLeadAction::class)->execute($data);

        // Assert

        $this->assertTrue($updatedLead->is($lead));
        $this->assertSame('Bobby', $updatedLead->first_name);
        $this->assertSame('Qualified', $updatedLead->last_name);
        $this->assertSame('Northwind', $updatedLead->company);
        $this->assertSame('bobby@example.test', $updatedLead->email);
        $this->assertSame('+37060000004', $updatedLead->phone);
        $this->assertSame($replacementOwner->id, $updatedLead->owner_id);
        $this->assertSame(85, $updatedLead->score);
        $this->assertSame(LeadRatingEnum::Hot, $updatedLead->rating);
    }
}
