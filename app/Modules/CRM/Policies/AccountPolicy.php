<?php

namespace App\Modules\CRM\Policies;

use App\Modules\CRM\Models\Account;
use App\Modules\Shared\Models\User;

class AccountPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Account $account): bool
    {
        return true;
    }
}
