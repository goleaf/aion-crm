<?php

namespace App\Modules\CRM\Policies;

use App\Modules\CRM\Models\Contact;
use App\Modules\Shared\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contact $contact): bool
    {
        return true;
    }
}
