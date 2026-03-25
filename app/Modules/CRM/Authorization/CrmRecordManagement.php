<?php

namespace App\Modules\CRM\Authorization;

use App\Modules\CRM\Enums\CrmRoleEnum;
use App\Modules\CRM\Support\CrmUserProfileFactory;
use App\Modules\Shared\Models\User;

final class CrmRecordManagement
{
    public static function canManageRecord(User $actor, int $ownerId, ?int $ownerTeamId): bool
    {
        $profile = CrmUserProfileFactory::forUser($actor);

        if (! $profile->isActive()) {
            return false;
        }

        if ($actor->id === $ownerId) {
            return true;
        }

        if ($profile->role === CrmRoleEnum::Admin) {
            return true;
        }

        if ($profile->role !== CrmRoleEnum::Manager) {
            return false;
        }

        if ($profile->primary_team_id === null || $ownerTeamId === null) {
            return false;
        }

        return $profile->primary_team_id === $ownerTeamId;
    }

    public static function canAssignRecordToOwner(User $actor, User $targetOwner): bool
    {
        $actorProfile = CrmUserProfileFactory::forUser($actor);
        $targetProfile = CrmUserProfileFactory::forUser($targetOwner);

        if (! $actorProfile->isActive() || ! $targetProfile->isActive()) {
            return false;
        }

        if ($actor->is($targetOwner)) {
            return true;
        }

        if ($actorProfile->role === CrmRoleEnum::Admin) {
            return true;
        }

        if ($actorProfile->role !== CrmRoleEnum::Manager) {
            return false;
        }

        if ($actorProfile->primary_team_id === null || $targetProfile->primary_team_id === null) {
            return false;
        }

        return $actorProfile->primary_team_id === $targetProfile->primary_team_id;
    }
}
