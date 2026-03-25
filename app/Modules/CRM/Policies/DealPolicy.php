<?php

namespace App\Modules\CRM\Policies;

use App\Modules\CRM\Models\Deal;
use App\Modules\Shared\Models\User;

class DealPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Deal $deal): bool
    {
        return true;
    }
}
