<?php

namespace App\Providers;

use App\Modules\Activities\Models\Activity;
use App\Modules\Activities\Policies\ActivityPolicy;
use App\Modules\Auth\Services\TokenIssuers\AuthTokenIssuerContract;
use App\Modules\Auth\Services\TokenIssuers\NullTokenIssuer;
use App\Modules\CRM\Authorization\CrmRecordVisibility;
use App\Modules\CRM\Authorization\CrmTeamAssignmentRules;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\Deal;
use App\Modules\CRM\Models\Lead;
use App\Modules\CRM\Policies\AccountPolicy;
use App\Modules\CRM\Policies\ContactPolicy;
use App\Modules\CRM\Policies\DealPolicy;
use App\Modules\CRM\Policies\LeadPolicy;
use App\Modules\Shared\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /** @var class-string<AuthTokenIssuerContract> $issuer */
        $issuer = config('auth.tokens.issuer', NullTokenIssuer::class);

        $this->app->bind(AuthTokenIssuerContract::class, $issuer);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootEmailVerificationDefaults();
        $this->bootCrmAuthorizationDefaults();
    }

    private function bootEmailVerificationDefaults(): void
    {
        VerifyEmail::createUrlUsing(function (MustVerifyEmail&Model $notifiable): string {
            $apiVerificationUrl = URL::temporarySignedRoute(
                'api.v1.auth.verification.verify',
                Date::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
            );

            $queryParamsToForward = parse_url($apiVerificationUrl, PHP_URL_QUERY);

            // We pass the id and hash explicitly as the frontend needs them to call the API later,
            // along with the expires and signature query params.
            $queryParamsToForward .= '&id='.$notifiable->getKey().'&hash='.sha1($notifiable->getEmailForVerification());

            return config('webhooks.frontend.redirects.email_verification_notice')."?{$queryParamsToForward}";
        });
    }

    private function bootCrmAuthorizationDefaults(): void
    {
        Gate::policy(Account::class, AccountPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(Contact::class, ContactPolicy::class);
        Gate::policy(Deal::class, DealPolicy::class);
        Gate::policy(Lead::class, LeadPolicy::class);

        Gate::define('crm.records.view', fn (User $actor, int $ownerId, ?int $ownerTeamId): bool => CrmRecordVisibility::canViewRecord($actor, $ownerId, $ownerTeamId));

        Gate::define('crm.records.reassign', fn (User $actor, User $sourceOwner, User $targetOwner): bool => CrmTeamAssignmentRules::canReassignOwnedRecords($actor, $sourceOwner, $targetOwner));
    }
}
