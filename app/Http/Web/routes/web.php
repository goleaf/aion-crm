<?php

use App\Http\Web\Auth\Controllers\LogoutController;
use App\Http\Web\Auth\Controllers\OAuthRedirectController;
use App\Http\Web\CRM\Activities\Controllers\ActivitiesIndexController;
use App\Modules\CRM\Models\Deal;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::middleware([])
    ->prefix('auth')
    ->name('auth.')
    ->group(function (): void {
        Route::get('oauth/{provider}/redirect', OAuthRedirectController::class)
            ->name('oauth-redirect');
    });

Route::middleware([])
    ->prefix('')
    ->name('')
    ->group(function (): void {
        Route::get('/', function (): View|RedirectResponse {
            if (auth()->guard('web')->check()) {
                return to_route('users.index');
            }

            return view('pages.login');
        })->name('login');
    });

Route::middleware('auth')
    ->prefix('')
    ->name('')
    ->group(function (): void {
        Route::post('/logout', LogoutController::class)->name('logout');

        Route::view('/users', 'pages.users')->name('users.index');
        Route::view('/leads', 'pages.leads')->name('leads.index');

        Route::prefix('crm')
            ->name('crm.')
            ->group(function (): void {
                Route::view('accounts', 'pages.crm.accounts.index')->name('accounts.index');
                Route::view('contacts', 'pages.crm.contacts.index')->name('contacts.index');
                Route::view('deals', 'pages.crm.deals.index')->name('deals.index');
                Route::view('deals/create', 'pages.crm.deals.create')->name('deals.create');

                Route::get('deals/{deal}/edit', fn(Deal $deal): View => view('pages.crm.deals.edit', ['deal' => $deal]))->name('deals.edit');
            });
    });

Route::middleware([])
    ->prefix('crm')
    ->name('crm.')
    ->group(function (): void {
        Route::get('activities', ActivitiesIndexController::class)
            ->name('activities.index');
    });
