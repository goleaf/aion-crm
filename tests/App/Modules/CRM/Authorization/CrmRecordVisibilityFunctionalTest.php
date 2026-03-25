<?php

namespace Tests\App\Modules\CRM\Authorization;

use App\Modules\CRM\Authorization\CrmRecordVisibility;
use App\Modules\CRM\Authorization\CrmTeamAssignmentRules;
use App\Modules\CRM\Enums\CrmRoleEnum;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\CRM\Providers\CrmRelationshipsServiceProvider;
use App\Modules\CRM\Support\CrmUserProfileFactory;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(CrmRecordVisibility::class)]
#[CoversClass(CrmTeamAssignmentRules::class)]
#[CoversClass(CrmRoleEnum::class)]
#[CoversClass(CrmUserProfileFactory::class)]
#[CoversClass(CrmRelationshipsServiceProvider::class)]
#[Group('crm')]
class CrmRecordVisibilityFunctionalTest extends FunctionalTestCase
{
    public function test_it_allows_admin_to_view_all_records(): void
    {
        // Arrange

        $teamA = CrmTeam::factory()->create();
        $teamB = CrmTeam::factory()->create();

        $admin = User::factory()->create();
        $ownerOnTeamA = User::factory()->create();
        $ownerOnTeamB = User::factory()->create();

        CrmUserProfile::factory()->for($admin)->for($teamA, 'primaryTeam')->admin()->create();
        CrmUserProfile::factory()->for($ownerOnTeamA)->for($teamA, 'primaryTeam')->rep()->create();
        CrmUserProfile::factory()->for($ownerOnTeamB)->for($teamB, 'primaryTeam')->rep()->create();

        // Act

        $canViewTeamARecord = CrmRecordVisibility::canViewRecord(
            actor: $admin,
            ownerId: $ownerOnTeamA->id,
            ownerTeamId: $teamA->id,
        );

        $canViewTeamBRecord = CrmRecordVisibility::canViewRecord(
            actor: $admin,
            ownerId: $ownerOnTeamB->id,
            ownerTeamId: $teamB->id,
        );

        // Assert

        $this->assertTrue($canViewTeamARecord);
        $this->assertTrue($canViewTeamBRecord);
    }

    public function test_it_applies_team_visibility_to_query_filters(): void
    {
        // Arrange

        $teamA = CrmTeam::factory()->create();
        $teamB = CrmTeam::factory()->create();

        $manager = User::factory()->create();
        $teammate = User::factory()->create();
        $outsideTeammate = User::factory()->create();

        $managerProfile = CrmUserProfile::factory()->for($manager)->for($teamA, 'primaryTeam')->manager()->create();

        CrmUserProfile::factory()->for($teammate)->for($teamA, 'primaryTeam')->rep()->create();
        CrmUserProfile::factory()->for($outsideTeammate)->for($teamB, 'primaryTeam')->rep()->create();

        // Act

        $visibleUserIds = CrmRecordVisibility::applyToQuery(
            query: CrmUserProfile::query(),
            actor: $manager,
            ownerColumn: 'user_id',
            teamColumn: 'primary_team_id',
        )->pluck('user_id')->all();

        // Assert

        $this->assertSame([$manager->id, $teammate->id], $visibleUserIds);
        $this->assertNotContains($outsideTeammate->id, $visibleUserIds);
        $this->assertSame('team', $managerProfile->record_visibility->value);
    }

    public function test_it_blocks_cross_team_reassignment_for_manager_and_requires_deactivated_source_owner(): void
    {
        // Arrange

        $teamA = CrmTeam::factory()->create();
        $teamB = CrmTeam::factory()->create();

        $manager = User::factory()->create();
        $fromOwner = User::factory()->create();
        $toOwnerSameTeam = User::factory()->create();
        $toOwnerOtherTeam = User::factory()->create();

        CrmUserProfile::factory()->for($manager)->for($teamA, 'primaryTeam')->manager()->create();
        CrmUserProfile::factory()->for($fromOwner)->for($teamA, 'primaryTeam')->rep()->inactive()->create();
        CrmUserProfile::factory()->for($toOwnerSameTeam)->for($teamA, 'primaryTeam')->rep()->create();
        CrmUserProfile::factory()->for($toOwnerOtherTeam)->for($teamB, 'primaryTeam')->rep()->create();

        // Act

        $canReassignSameTeam = CrmTeamAssignmentRules::canReassignOwnedRecords(
            actor: $manager,
            sourceOwner: $fromOwner,
            targetOwner: $toOwnerSameTeam,
        );

        $canReassignOtherTeam = CrmTeamAssignmentRules::canReassignOwnedRecords(
            actor: $manager,
            sourceOwner: $fromOwner,
            targetOwner: $toOwnerOtherTeam,
        );

        $fromOwnerMustBeReassigned = CrmTeamAssignmentRules::mustReassignOwnedRecordsWhenUserIsDeactivated($fromOwner);

        // Assert

        $this->assertTrue($canReassignSameTeam);
        $this->assertFalse($canReassignOtherTeam);
        $this->assertTrue($fromOwnerMustBeReassigned);
    }
}
