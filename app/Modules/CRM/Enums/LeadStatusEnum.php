<?php

namespace App\Modules\CRM\Enums;

enum LeadStatusEnum: string
{
    case New = 'new';

    case Contacted = 'contacted';

    case Qualified = 'qualified';

    case Unqualified = 'unqualified';

    case Converted = 'converted';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Contacted => 'Contacted',
            self::Qualified => 'Qualified',
            self::Unqualified => 'Unqualified',
            self::Converted => 'Converted',
        };
    }
}
