<?php

namespace App\Modules\CRM\Policies;

use App\Modules\CRM\Models\Lead;
use App\Modules\Shared\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Lead $lead): bool
    {
        return true;
    }
}
