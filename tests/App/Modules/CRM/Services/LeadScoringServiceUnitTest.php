<?php

namespace Tests\App\Modules\CRM\Services;

use App\Modules\CRM\DataTransferObjects\LeadScoreData;
use App\Modules\CRM\Enums\LeadRatingEnum;
use App\Modules\CRM\Enums\LeadSourceEnum;
use App\Modules\CRM\Enums\LeadStatusEnum;
use App\Modules\CRM\Services\LeadScoringService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\UnitTestCase;

#[CoversClass(LeadScoringService::class)]
#[CoversClass(LeadScoreData::class)]
#[CoversClass(LeadSourceEnum::class)]
#[CoversClass(LeadStatusEnum::class)]
#[CoversClass(LeadRatingEnum::class)]
#[Group('crm')]
class LeadScoringServiceUnitTest extends UnitTestCase
{
    public function test_it_scores_a_referral_with_full_contact_data_as_hot(): void
    {
        // Arrange

        $service = new LeadScoringService;

        // Act

        $score = $service->score(
            email: 'lead@example.com',
            phone: '+37060000001',
            leadSource: LeadSourceEnum::Referral,
            status: LeadStatusEnum::Qualified,
        );

        // Assert

        $this->assertSame(85, $score->score);
        $this->assertSame(LeadRatingEnum::Hot, $score->rating);
    }

    public function test_it_scores_a_contacted_walk_in_as_warm(): void
    {
        // Arrange

        $service = new LeadScoringService;

        // Act

        $score = $service->score(
            email: null,
            phone: '+37060000002',
            leadSource: LeadSourceEnum::WalkIn,
            status: LeadStatusEnum::Contacted,
        );

        // Assert

        $this->assertSame(51, $score->score);
        $this->assertSame(LeadRatingEnum::Warm, $score->rating);
    }

    public function test_it_scores_a_new_cold_call_as_cold(): void
    {
        // Arrange

        $service = new LeadScoringService;

        // Act

        $score = $service->score(
            email: 'lead@example.com',
            phone: null,
            leadSource: LeadSourceEnum::ColdCall,
            status: LeadStatusEnum::New,
        );

        // Assert

        $this->assertSame(28, $score->score);
        $this->assertSame(LeadRatingEnum::Cold, $score->rating);
    }
}
