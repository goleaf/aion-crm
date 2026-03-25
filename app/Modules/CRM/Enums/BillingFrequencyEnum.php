<?php

namespace App\Modules\CRM\Enums;

enum BillingFrequencyEnum: string
{
    case Monthly = 'monthly';

    case Annual = 'annual';

    case OneTime = 'one_time';
}
