<?php

namespace App\Modules\CRM\Enums;

enum LeadRatingEnum: string
{
    case Hot = 'hot';

    case Warm = 'warm';

    case Cold = 'cold';

    public function label(): string
    {
        return match ($this) {
            self::Hot => 'Hot',
            self::Warm => 'Warm',
            self::Cold => 'Cold',
        };
    }
}
