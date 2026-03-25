<?php

namespace App\Modules\CRM\Support;

use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\Shared\Models\User;
use RuntimeException;

final class CrmUserProfileFactory
{
    public static function forUser(User $user): CrmUserProfile
    {
        $profile = $user->crmProfile()->first();

        throw_unless($profile instanceof CrmUserProfile, RuntimeException::class, 'CRM profile is required before using CRM authorization rules.');

        return $profile;
    }
}
