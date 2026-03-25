<?php

namespace App\Modules\CRM\Enums;

enum AccountTypeEnum: string
{
    case Customer = 'customer';
    case Partner = 'partner';
    case Prospect = 'prospect';
    case Vendor = 'vendor';
}
