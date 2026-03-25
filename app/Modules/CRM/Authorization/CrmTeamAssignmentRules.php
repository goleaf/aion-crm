<?php

namespace App\Modules\CRM\Authorization;

use App\Modules\CRM\Enums\CrmRoleEnum;
use App\Modules\CRM\Support\CrmUserProfileFactory;
use App\Modules\Shared\Models\User;

final class CrmTeamAssignmentRules
{
    public static function mustReassignOwnedRecordsWhenUserIsDeactivated(User $owner): bool
    {
        return CrmUserProfileFactory::forUser($owner)->isDeactivated();
    }

    public static function canReassignOwnedRecords(User $actor, User $sourceOwner, User $targetOwner): bool
    {
        $actorProfile = CrmUserProfileFactory::forUser($actor);
        $sourceProfile = CrmUserProfileFactory::forUser($sourceOwner);
        $targetProfile = CrmUserProfileFactory::forUser($targetOwner);

        if (! $actorProfile->role->canManageReassignments()) {
            return false;
        }

        if (! $sourceProfile->isDeactivated()) {
            return false;
        }

        if (! $targetProfile->isActive()) {
            return false;
        }

        if ($actorProfile->role === CrmRoleEnum::Admin) {
            return true;
        }

        if ($actorProfile->primary_team_id === null || $targetProfile->primary_team_id === null) {
            return false;
        }

        return $actorProfile->primary_team_id === $targetProfile->primary_team_id;
    }
}
