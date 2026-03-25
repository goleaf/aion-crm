<?php

namespace App\Http\Web\CRM\Contacts\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactsIndexController
{
    public function __invoke(): View|RedirectResponse
    {
        if (! auth()->guard('web')->check()) {
            return to_route('login');
        }

        return view('pages.crm.contacts');
    }
}
