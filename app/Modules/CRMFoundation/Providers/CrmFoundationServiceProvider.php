<?php

namespace App\Modules\CRMFoundation\Providers;

use App\Modules\CRMFoundation\Contracts\WorkspaceContextContract;
use App\Modules\CRMFoundation\Tenancy\NullWorkspaceContext;
use Illuminate\Support\ServiceProvider;

class CrmFoundationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WorkspaceContextContract::class, NullWorkspaceContext::class);
    }
}
