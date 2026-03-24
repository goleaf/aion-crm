<?php

namespace App\Livewire\Users;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class UsersTablePage extends Component
{
    public array $users = [];

    public function mount(): void
    {
        $users = config()->array('demo-users.users');

        $this->users = $users;
    }

    public function render(): View
    {
        return view('livewire.users.users-table-page');
    }
}
