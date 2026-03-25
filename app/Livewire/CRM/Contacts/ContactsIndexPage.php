<?php

namespace App\Livewire\CRM\Contacts;

use App\Modules\CRM\Models\Contact;
use App\Modules\Shared\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ContactsIndexPage extends Component
{
    public string $search = '';

    public function render(): View
    {
        $actor = auth()->user();
        assert($actor instanceof User);

        return view('livewire.c-r-m.contacts.contacts-index-page', [
            'contacts' => Contact::query()
                ->select([
                    'id',
                    'account_id',
                    'owner_id',
                    'team_id',
                    'first_name',
                    'last_name',
                    'email',
                ])
                ->with([
                    'account:id,name',
                    'owner:id,name',
                ])
                ->visibleTo($actor)
                ->search($this->search)
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get(),
        ]);
    }
}
