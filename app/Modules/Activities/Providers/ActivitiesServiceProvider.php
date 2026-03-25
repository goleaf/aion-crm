<?php

namespace App\Modules\Activities\Providers;

use App\Modules\Activities\Models\Activity;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\Deal;
use App\Modules\CRM\Models\Lead;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class ActivitiesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'account' => Account::class,
            'activity' => Activity::class,
            'contact' => Contact::class,
            'deal' => Deal::class,
            'lead' => Lead::class,
        ]);
    }
}
