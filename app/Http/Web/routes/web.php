<?php

use App\Http\Web\Auth\Controllers\OAuthRedirectController;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::get('oauth/{provider}/redirect', OAuthRedirectController::class)
    ->name('auth.oauth-redirect');

Route::get('/', function (): View|RedirectResponse {
    if (auth()->guard('web')->check()) {
        return to_route('users.index');
    }

    return view('pages.login');
})->name('login');

Route::get('/users', function (): View|RedirectResponse {
    if (! auth()->guard('web')->check()) {
        return to_route('login');
    }

    return view('pages.users');
})->name('users.index');
