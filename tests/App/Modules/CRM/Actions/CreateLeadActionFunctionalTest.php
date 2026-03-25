<?php

namespace Tests\App\Modules\CRM\Actions;

use App\Modules\CRM\Actions\CreateLeadAction;
use App\Modules\CRM\DataTransferObjects\CreateLeadData;
use App\Modules\CRM\Enums\LeadRatingEnum;
use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;
use App\Modules\CRM\Foundation\Support\UuidPolicy;
use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(CreateLeadAction::class)]
#[CoversClass(CreateLeadData::class)]
#[Group('crm')]
class CreateLeadActionFunctionalTest extends FunctionalTestCase
{
    public function test_it_creates_a_lead_and_calculates_its_score(): void
    {
        // Arrange

        $owner = User::factory()->create();

        $data = new CreateLeadData(
            firstName: 'Alice',
            lastName: 'Referral',
            company: 'Acme Corp',
            email: 'alice@acme.test',
            phone: '+37060000003',
            leadSource: LeadSourceEnum::Referral,
            status: LeadStatusEnum::Qualified,
            campaignId: null,
            owner: $owner,
            description: 'Met at industry event.',
        );

        // Act

        $lead = resolve(CreateLeadAction::class)->execute($data);

        // Assert

        $this->assertInstanceOf(Lead::class, $lead);
        $this->assertModelExists($lead);
        $this->assertTrue(UuidPolicy::isValid($lead->lead_id));
        $this->assertSame('Alice', $lead->first_name);
        $this->assertSame('Referral', $lead->last_name);
        $this->assertSame('Acme Corp', $lead->company);
        $this->assertSame('alice@acme.test', $lead->email);
        $this->assertSame($owner->id, $lead->owner_id);
        $this->assertSame(85, $lead->score);
        $this->assertSame(LeadRatingEnum::Hot, $lead->rating);
        $this->assertFalse($lead->converted);
    }
}
