<?php

namespace App\Http\Web\Auth\Controllers;

use App\Modules\Auth\Actions\LogoutAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoutController
{
    public function __invoke(
        Request $request,
        LogoutAction $logoutAction,
    ): RedirectResponse {
        $logoutAction->execute();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }
}
