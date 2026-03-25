<?php

use App\Http\Api\Auth\Controllers as AuthControllers;
use App\Http\Api\CRM\Controllers as CRMControllers;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->group(function (): void {
        Route::prefix('tokens')->group(function (): void {
            Route::post('/', AuthControllers\LoginWithCredentialsController::class)
                ->name('auth.tokens.store');

            Route::post('refresh', AuthControllers\RefreshTokenController::class)
                ->name('auth.tokens.refresh');
        });

        Route::post('oauth-tokens', AuthControllers\ContinueWithOAuthController::class)
            ->name('auth.oauth-tokens.store');

        Route::post('users', AuthControllers\RegisterWithCredentialsController::class)
            ->name('auth.users.store');

        Route::prefix('passwords')->group(function (): void {
            Route::post('reset-requests', AuthControllers\ForgotPasswordController::class)
                ->name('auth.passwords.requests.store');

            Route::patch('reset-requests', AuthControllers\ResetPasswordController::class)
                ->name('auth.passwords.resets.patch');
        });

        Route::post('two-factor/challenges', AuthControllers\TwoFactorChallengeController::class)
            ->name('auth.two-factor.challenges.store');

        Route::prefix('email')->group(function (): void {
            Route::post('verify/{id}/{hash}', AuthControllers\VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('auth.verification.verify');
        });

        Route::prefix('magic-links')->group(function (): void {
            Route::post('/', AuthControllers\SendMagicLinkController::class)
                ->name('auth.magic-links.store');

            Route::post('verifications', AuthControllers\VerifyMagicLinkController::class)
                ->name('auth.magic-links.verifications.store');
        });
    });

Route::middleware('auth')
    ->group(function (): void {
        Route::prefix('auth')
            ->group(function (): void {
                Route::post('oauth/link', AuthControllers\LinkSocialAccountController::class)
                    ->name('auth.oauth-links.store');

                Route::post('logout', AuthControllers\LogoutController::class)->name('auth.logout');

                Route::prefix('two-factor')->group(function (): void {
                    Route::post('/', AuthControllers\EnableTwoFactorController::class)
                        ->name('auth.two-factor.store');

                    Route::delete('/', AuthControllers\DisableTwoFactorController::class)
                        ->name('auth.two-factor.destroy');

                    Route::post('confirmations', AuthControllers\ConfirmTwoFactorController::class)
                        ->name('auth.two-factor.confirmations.store');

                    Route::post('recovery-codes', AuthControllers\RegenerateTwoFactorRecoveryCodesController::class)
                        ->name('auth.two-factor.recovery-codes.store');
                });

                Route::delete('oauth/link/{provider}', AuthControllers\UnlinkSocialAccountController::class)
                    ->name('auth.oauth-links.destroy');

                Route::prefix('email')->group(function (): void {
                    Route::post('verification-notification', AuthControllers\ResendEmailVerificationController::class)
                        ->middleware(['throttle:6,1'])
                        ->name('auth.verification.send');
                });
            });

        Route::prefix('crm')
            ->name('crm.')
            ->group(function (): void {
                Route::prefix('deals')
                    ->name('deals.')
                    ->group(function (): void {
                        Route::get('/', CRMControllers\ListDealsController::class)->name('index');
                        Route::post('/', CRMControllers\StoreDealController::class)->name('store');
                        Route::get('{deal}', CRMControllers\ShowDealController::class)->name('show');
                        Route::patch('{deal}', CRMControllers\UpdateDealController::class)->name('update');
                        Route::post('{deal}/close-won', CRMControllers\CloseDealAsWonController::class)->name('close-won');
                        Route::post('{deal}/close-lost', CRMControllers\CloseDealAsLostController::class)->name('close-lost');
                    });
            });
    });
