<?php

namespace App\Modules\CRM\Support;

use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\Shared\Models\User;

final class CrmUserProfileFactory
{
    public static function forUser(User $user): CrmUserProfile
    {
        $profile = $user->crmProfile()->first();

        if (! $profile instanceof CrmUserProfile) {
            throw new \RuntimeException('CRM profile is required before using CRM authorization rules.');
        }

        return $profile;
    }
}
