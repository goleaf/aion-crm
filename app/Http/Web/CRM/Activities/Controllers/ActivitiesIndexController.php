<?php

namespace App\Http\Web\CRM\Activities\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ActivitiesIndexController
{
    public function __invoke(): View|RedirectResponse
    {
        if (! auth()->guard('web')->check()) {
            return to_route('login');
        }

        return view('pages.crm.activities');
    }
}
