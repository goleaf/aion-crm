<?php

namespace App\Livewire\CRM\Accounts;

use App\Modules\CRM\Models\Account;
use App\Modules\Shared\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AccountsIndexPage extends Component
{
    public string $search = '';

    public function render(): View
    {
        $actor = auth()->user();
        assert($actor instanceof User);

        return view('livewire.c-r-m.accounts.accounts-index-page', [
            'accounts' => Account::query()
                ->select([
                    'id',
                    'name',
                    'type',
                    'owner_id',
                    'team_id',
                ])
                ->with('owner:id,name')
                ->visibleTo($actor)
                ->search($this->search)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
