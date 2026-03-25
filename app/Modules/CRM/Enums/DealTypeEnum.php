<?php

namespace App\Modules\CRM\Enums;

enum DealTypeEnum: string
{
    case NewBusiness = 'new_business';

    case Renewal = 'renewal';

    case Upsell = 'upsell';
}
