<?php

namespace App\Modules\CRM\Enums;

enum DealSourceEnum: string
{
    case Inbound = 'inbound';

    case Outbound = 'outbound';

    case Referral = 'referral';

    case Event = 'event';

    case Partner = 'partner';

    case ExistingCustomer = 'existing_customer';
}
