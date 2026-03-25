<?php

namespace App\Modules\CRM\Enums;

enum ContactLeadSourceEnum: string
{
    case Referral = 'referral';
    case Website = 'website';
    case Outbound = 'outbound';
    case Event = 'event';
    case Partner = 'partner';
    case Other = 'other';
}
