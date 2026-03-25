<?php

namespace App\Modules\CRM\Authorization;

use App\Modules\CRM\Foundation\Enums\RecordVisibilityEnum;
use App\Modules\CRM\Support\CrmUserProfileFactory;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class CrmRecordVisibility
{
    public static function canViewRecord(User $actor, int $ownerId, ?int $ownerTeamId): bool
    {
        $profile = CrmUserProfileFactory::forUser($actor);

        if (! $profile->isActive()) {
            return false;
        }

        if ($profile->record_visibility === RecordVisibilityEnum::All) {
            return true;
        }

        if ($ownerId === $actor->id) {
            return true;
        }

        if ($profile->record_visibility !== RecordVisibilityEnum::Team) {
            return false;
        }

        if ($profile->primary_team_id === null || $ownerTeamId === null) {
            return false;
        }

        return $profile->primary_team_id === $ownerTeamId;
    }

    public static function applyToQuery(Builder $query, User $actor, string $ownerColumn = 'owner_id', string $teamColumn = 'team_id'): Builder
    {
        $profile = CrmUserProfileFactory::forUser($actor);

        if (! $profile->isActive()) {
            return $query->whereIn($ownerColumn, []);
        }

        if ($profile->record_visibility === RecordVisibilityEnum::All) {
            return $query;
        }

        if ($profile->record_visibility === RecordVisibilityEnum::Own) {
            return $query->where($ownerColumn, $actor->id);
        }

        $primaryTeamId = $profile->primary_team_id;

        if ($primaryTeamId === null) {
            return $query->where($ownerColumn, $actor->id);
        }

        return $query->where(function (Builder $builder) use ($actor, $ownerColumn, $teamColumn, $primaryTeamId): void {
            $builder
                ->where($ownerColumn, $actor->id)
                ->orWhere($teamColumn, $primaryTeamId);
        });
    }
}
