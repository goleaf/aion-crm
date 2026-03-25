<?php

namespace App\Modules\CRM\Enums;

enum ContactPreferredChannelEnum: string
{
    case Email = 'email';

    case Phone = 'phone';

    case Sms = 'sms';
}
