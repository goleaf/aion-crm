<?php

namespace App\Modules\Activities\Policies;

use App\Modules\Activities\Models\Activity;
use App\Modules\Shared\Models\User;

class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Activity $activity): bool
    {
        return $activity->owner->is($user);
    }
}
