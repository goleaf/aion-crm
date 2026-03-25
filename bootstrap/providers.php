<?php

use App\Modules\Auth\Providers\AuthRelationshipsServiceProvider;
use App\Modules\Auth\Providers\TwoFactorAuthenticationServiceProvider;
use App\Modules\CRM\Providers\CrmRelationshipsServiceProvider;
use App\Modules\CRMFoundation\Providers\CrmFoundationServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    CrmFoundationServiceProvider::class,
    CrmRelationshipsServiceProvider::class,
    AuthRelationshipsServiceProvider::class,
    TwoFactorAuthenticationServiceProvider::class,
];
