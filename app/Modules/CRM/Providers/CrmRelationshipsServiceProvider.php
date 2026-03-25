<?php

namespace App\Modules\CRM\Providers;

use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\Shared\Models\User;
use Illuminate\Support\ServiceProvider;

class CrmRelationshipsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        User::resolveRelationUsing('crmProfile', fn (User $user) => $user->hasOne(CrmUserProfile::class));

        User::resolveRelationUsing(
            'crmTeam',
            fn (User $user) => $user->hasOneThrough(
                CrmTeam::class,
                CrmUserProfile::class,
                'user_id',
                'id',
                'id',
                'primary_team_id',
            ),
        );
    }
}
