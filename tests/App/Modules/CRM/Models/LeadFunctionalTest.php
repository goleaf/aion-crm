<?php

namespace Tests\App\Modules\CRM\Models;

use App\Modules\CRM\Enums\LeadRatingEnum;
use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;
use App\Modules\CRM\Foundation\Support\UuidPolicy;
use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(Lead::class)]
#[Group('crm')]
class LeadFunctionalTest extends FunctionalTestCase
{
    public function test_it_uses_a_uuid_primary_key_and_belongs_to_an_owner(): void
    {
        // Arrange

        $owner = User::factory()->create();

        // Act

        $lead = Lead::factory()->create([
            'owner_id' => $owner->id,
        ])->load('owner');

        // Assert

        $this->assertTrue(UuidPolicy::isValid($lead->lead_id));
        $this->assertTrue($lead->owner->is($owner));
    }

    public function test_it_filters_leads_with_query_scopes(): void
    {
        // Arrange

        $owner = User::factory()->create();
        $anotherOwner = User::factory()->create();

        $expectedLead = Lead::factory()->create([
            'owner_id' => $owner->id,
            'first_name' => 'Alice',
            'company' => 'Acme',
            'lead_source' => LeadSourceEnum::Referral,
            'status' => LeadStatusEnum::Qualified,
            'rating' => LeadRatingEnum::Hot,
            'converted' => false,
        ]);

        Lead::factory()->create([
            'owner_id' => $owner->id,
            'first_name' => 'Bob',
            'company' => 'Beta',
            'lead_source' => LeadSourceEnum::ColdCall,
            'status' => LeadStatusEnum::New,
            'rating' => LeadRatingEnum::Cold,
            'converted' => false,
        ]);

        Lead::factory()->create([
            'owner_id' => $anotherOwner->id,
            'first_name' => 'Charlie',
            'company' => 'Acme Partners',
            'lead_source' => LeadSourceEnum::Referral,
            'status' => LeadStatusEnum::Qualified,
            'rating' => LeadRatingEnum::Hot,
            'converted' => true,
        ]);

        // Act

        $leads = Lead::query()
            ->listView()
            ->searchTerm('Acme')
            ->ownedBy($owner->id)
            ->withStatus(LeadStatusEnum::Qualified->value)
            ->fromSource(LeadSourceEnum::Referral->value)
            ->withRating(LeadRatingEnum::Hot->value)
            ->converted(false)
            ->get();

        // Assert

        $this->assertCount(1, $leads);
        $this->assertTrue($expectedLead->is($leads->sole()));
    }
}
